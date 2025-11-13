<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('graduation_year');
            $table->string('house')->nullable(); // House system: Perseverance, Integrity, etc.
            $table->text('bio')->nullable();
            $table->string('current_profession')->nullable();
            $table->string('current_company')->nullable();
            $table->string('current_city')->nullable();
            $table->string('current_country')->nullable();
            $table->json('social_links')->nullable(); // LinkedIn, Facebook, Twitter, etc.
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['graduation_year', 'is_active']);
            $table->index('house');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
