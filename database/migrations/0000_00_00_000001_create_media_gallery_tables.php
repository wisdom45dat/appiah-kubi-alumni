<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('privacy')->default('public'); // public, alumni, private
            $table->foreignId('created_by')->constrained('users');
            $table->integer('photo_count')->default(0);
            $table->integer('video_count')->default(0);
            $table->string('category')->default('general'); // school_days, reunions, events, etc.
            $table->year('album_year')->nullable(); // Year the photos are from
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['category', 'album_year']);
            $table->index('privacy');
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // image, video
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->text('caption')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable(); // EXIF data for images
            $table->integer('views')->default(0);
            $table->integer('likes_count')->default(0);
            $table->string('privacy')->default('public');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['album_id', 'file_type']);
            $table->index('uploaded_by');
        });

        Schema::create('media_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['media_id', 'user_id']);
        });

        Schema::create('media_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('media_comments')->onDelete('cascade');
            $table->text('comment');
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['media_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_comments');
        Schema::dropIfExists('media_likes');
        Schema::dropIfExists('media');
        Schema::dropIfExists('albums');
    }
};
