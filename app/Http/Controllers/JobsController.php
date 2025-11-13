<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $query = JobListing::with('poster')->active();

        // Filter by employment type
        if ($request->has('employment_type') && $request->employment_type) {
            $query->where('employment_type', $request->employment_type);
        }

        // Filter by location
        if ($request->has('location') && $request->location) {
            if ($request->location === 'remote') {
                $query->where('is_remote', true);
            } else {
                $query->where('location', 'like', '%' . $request->location . '%');
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $jobs = $query->latest()->paginate(15);

        $employmentTypes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'freelance' => 'Freelance',
        ];

        $featuredJobs = JobListing::with('poster')
            ->active()
            ->featured()
            ->limit(5)
            ->get();

        return view('jobs.index', compact('jobs', 'employmentTypes', 'featuredJobs'));
    }

    public function show(JobListing $job)
    {
        if (!$job->is_active && $job->posted_by !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(404);
        }

        $job->incrementViews();
        $job->load('poster');

        $similarJobs = JobListing::with('poster')
            ->active()
            ->where('id', '!=', $job->id)
            ->where(function($q) use ($job) {
                $q->where('employment_type', $job->employment_type)
                  ->orWhere('company_name', $job->company_name)
                  ->orWhere('location', 'like', '%' . $job->location . '%');
            })
            ->limit(4)
            ->get();

        return view('jobs.show', compact('job', 'similarJobs'));
    }

    public function create()
    {
        $employmentTypes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'freelance' => 'Freelance',
        ];

        return view('jobs.create', compact('employmentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'company_name' => 'required|string|max:255',
            'employment_type' => 'required|string',
            'location' => 'required|string|max:255',
            'is_remote' => 'boolean',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'salary_currency' => 'required_with:salary_min,salary_max',
            'application_url' => 'nullable|url',
            'contact_email' => 'required|email',
            'application_deadline' => 'nullable|date|after:today',
        ]);

        $job = JobListing::create([
            ...$validated,
            'posted_by' => auth()->id(),
            'is_active' => true,
        ]);

        // Notify admin about new job posting
        $this->notifyAdminAboutNewJob($job);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job listing created successfully! It will be reviewed before publishing.');
    }

    public function apply(JobListing $job, Request $request)
    {
        $request->validate([
            'cover_letter' => 'required|string|max:2000',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB
        ]);

        // Store resume
        $resumePath = $request->file('resume')->store('job-applications', 'public');

        // Create application record
        $application = \App\Models\JobApplication::create([
            'job_listing_id' => $job->id,
            'user_id' => auth()->id(),
            'cover_letter' => $request->cover_letter,
            'resume_path' => $resumePath,
            'status' => 'submitted',
        ]);

        // Send email notification to job poster
        Mail::send('emails.job-application', [
            'job' => $job,
            'application' => $application,
            'applicant' => auth()->user(),
        ], function($message) use ($job, $application) {
            $message->to($job->contact_email)
                    ->subject('New Job Application: ' . $job->title);
        });

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Application submitted successfully!');
    }

    public function myJobs()
    {
        $jobs = JobListing::where('posted_by', auth()->id())
            ->withCount('applications')
            ->latest()
            ->paginate(15);

        return view('jobs.my-jobs', compact('jobs'));
    }

    private function notifyAdminAboutNewJob(JobListing $job)
    {
        // This would typically send an email to admin
        // For now, we'll just log it
        \Log::info('New job listing created', [
            'job_id' => $job->id,
            'title' => $job->title,
            'company' => $job->company_name,
            'posted_by' => $job->poster->name,
        ]);
    }
}
