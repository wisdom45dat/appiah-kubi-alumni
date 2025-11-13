<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('company_name');
            $table->string('employment_type'); // full_time, part_time, contract, internship
            $table->string('location');
            $table->boolean('is_remote')->default(false);
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency')->default('GHS');
            $table->string('application_url')->nullable();
            $table->string('contact_email');
            $table->date('application_deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('posted_by')->constrained('users');
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['employment_type', 'is_active']);
            $table->index('is_featured');
        });

        Schema::create('mentorship_programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category'); // career, academic, business, life_skills
            $table->json('skills_covered')->nullable();
            $table->string('duration')->nullable(); // e.g., "3 months"
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['category', 'is_active']);
        });

        Schema::create('mentorship_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('mentorship_programs')->onDelete('cascade');
            $table->foreignId('mentor_id')->constrained('users');
            $table->foreignId('mentee_id')->constrained('users');
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('goals')->nullable();
            $table->timestamps();
            
            $table->unique(['mentor_id', 'mentee_id', 'program_id']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mentorship_matches');
        Schema::dropIfExists('mentorship_programs');
        Schema::dropIfExists('job_listings');
    }
};
