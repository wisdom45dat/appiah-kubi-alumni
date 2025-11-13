<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // general, school_memories, career, projects
            $table->integer('topics_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
        });

        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->foreignId('last_reply_by')->nullable()->constrained('users');
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['forum_id', 'is_pinned']);
            $table->index('created_at');
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('forum_posts')->onDelete('cascade');
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['topic_id', 'created_at']);
        });

        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->text('content');
            $table->string('featured_image')->nullable();
            $table->string('author_name');
            $table->foreignId('author_id')->constrained('users');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->json('tags')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_published', 'published_at']);
            $table->index('is_featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forums');
    }
};
