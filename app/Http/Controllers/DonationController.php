<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        if (config('services.razorpay.key') && config('services.razorpay.secret')) {
            $this->razorpay = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );
        }
    }

    public function index()
    {
        $campaigns = Campaign::with('creator')
            ->ongoing()
            ->active()
            ->latest()
            ->paginate(12);

        $featuredCampaigns = Campaign::with('creator')
            ->featured()
            ->ongoing()
            ->active()
            ->limit(3)
            ->get();

        $recentDonations = Donation::with(['campaign', 'donor'])
            ->completed()
            ->recent(7)
            ->limit(10)
            ->get();

        $totalRaised = Donation::completed()->sum('amount');
        $totalDonors = Donation::completed()->distinct('donor_email')->count();

        return view('donations.index', compact('campaigns', 'featuredCampaigns', 'recentDonations', 'totalRaised', 'totalDonors'));
    }

    public function showCampaign(Campaign $campaign)
    {
        if (!$campaign->is_active) {
            abort(404);
        }

        $campaign->load('creator');
        
        $donations = $campaign->donations()
            ->completed()
            ->with('donor')
            ->latest()
            ->paginate(20);

        $topDonors = $campaign->donations()
            ->completed()
            ->select('donor_name', \DB::raw('SUM(amount) as total_donated'))
            ->groupBy('donor_name')
            ->orderBy('total_donated', 'desc')
            ->limit(10)
            ->get();

        return view('donations.campaign', compact('campaign', 'donations', 'topDonors'));
    }

    public function createDonation(Campaign $campaign, Request $request)
    {
        if (!$campaign->is_active) {
            return redirect()->back()->with('error', 'This campaign is no longer active.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email',
            'donor_phone' => 'nullable|string|max:20',
            'message' => 'nullable|string|max:500',
            'is_anonymous' => 'boolean',
            'payment_method' => 'required|in:card,mobile_money,bank_transfer',
        ]);

        // Create donation record
        $donation = Donation::create([
            'campaign_id' => $campaign->id,
            'user_id' => auth()->id(),
            'donor_name' => $validated['donor_name'],
            'donor_email' => $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'],
            'amount' => $validated['amount'],
            'currency' => 'GHS',
            'payment_method' => $validated['payment_method'],
            'transaction_id' => 'TXN_' . Str::random(16),
            'status' => 'pending',
            'is_anonymous' => $request->boolean('is_anonymous'),
            'message' => $validated['message'],
        ]);

        // Process payment based on method
        if ($validated['payment_method'] === 'card' && $this->razorpay) {
            return $this->processRazorpayPayment($donation);
        }

        // For other payment methods, show instructions
        return view('donations.payment-instructions', compact('donation', 'campaign'));
    }

    public function paymentSuccess(Donation $donation)
    {
        if ($donation->status !== 'completed') {
            // Verify payment with payment gateway
            $this->verifyPayment($donation);
        }

        $donation->load('campaign');

        return view('donations.success', compact('donation'));
    }

    public function paymentWebhook(Request $request)
    {
        // Handle payment webhooks from payment gateway
        $payload = $request->all();
        
        // Verify webhook signature
        if ($this->verifyWebhookSignature($request)) {
            $this->processWebhookPayload($payload);
        }

        return response()->json(['status' => 'ok']);
    }

    private function processRazorpayPayment(Donation $donation)
    {
        try {
            $order = $this->razorpay->order->create([
                'receipt' => $donation->transaction_id,
                'amount' => $donation->amount * 100, // Convert to paise
                'currency' => 'GHS',
                'payment_capture' => 1,
            ]);

            $donation->update([
                'payment_details' => ['razorpay_order_id' => $order->id]
            ]);

            return view('donations.razorpay-checkout', compact('donation', 'order'));

        } catch (\Exception $e) {
            \Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    private function verifyPayment(Donation $donation)
    {
        // Implement payment verification logic based on payment method
        // This would typically check with the payment gateway API
        
        // For demo purposes, mark as completed
        $donation->markAsCompleted();
    }

    private function verifyWebhookSignature(Request $request)
    {
        // Implement webhook signature verification
        return true; // Simplified for demo
    }

    private function processWebhookPayload($payload)
    {
        // Process webhook payload and update donation status
        if (isset($payload['event']) && $payload['event'] === 'payment.captured') {
            $paymentId = $payload['payload']['payment']['entity']['id'];
            
            $donation = Donation::where('payment_details->razorpay_payment_id', $paymentId)->first();
            if ($donation) {
                $donation->markAsCompleted();
            }
        }
    }

    public function myDonations()
    {
        $donations = Donation::where('user_id', auth()->id())
            ->with('campaign')
            ->latest()
            ->paginate(20);

        $totalDonated = Donation::where('user_id', auth()->id())
            ->completed()
            ->sum('amount');

        return view('donations.my-donations', compact('donations', 'totalDonated'));
    }
}
