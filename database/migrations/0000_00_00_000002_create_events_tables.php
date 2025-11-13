<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('featured_image')->nullable();
            $table->string('type'); // reunion, fundraising, networking, workshop
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('venue_name');
            $table->text('venue_address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->integer('capacity')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->json('registration_fields')->nullable(); // Custom fields for registration
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'start_date']);
            $table->index('is_published');
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, attended
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->json('registration_data')->nullable(); // Custom field responses
            $table->integer('guests_count')->default(0);
            $table->text('special_requirements')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
            
            $table->unique(['event_id', 'user_id']);
            $table->index(['event_id', 'status']);
        });

        Schema::create('event_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            $table->index(['event_id', 'is_featured']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_photos');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('events');
    }
};
