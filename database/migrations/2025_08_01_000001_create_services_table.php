<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('slug', 32)->unique();
            $table->enum('type', ['web_server', 'database', 'cache', 'mail', 'other'])->default('other');
            $table->enum('status', ['running', 'stopped', 'unknown'])->default('unknown');
            $table->unsignedSmallInteger('port')->nullable();
            $table->string('version', 32)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
