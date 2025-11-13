<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('featured_image')->nullable();
            $table->string('type'); // scholarship, infrastructure, emergency, general
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('allowed_payment_methods')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_active']);
            $table->index('is_featured');
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('donor_name');
            $table->string('donor_email');
            $table->string('donor_phone')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('GHS');
            $table->string('payment_method'); // card, mobile_money, bank_transfer
            $table->string('transaction_id')->unique();
            $table->string('status'); // pending, completed, failed, refunded
            $table->boolean('is_anonymous')->default(false);
            $table->text('message')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
            
            $table->index(['campaign_id', 'status']);
            $table->index('transaction_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('donations');
        Schema::dropIfExists('campaigns');
    }
};
