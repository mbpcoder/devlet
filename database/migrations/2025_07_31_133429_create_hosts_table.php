<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('full_path');
            $table->char('php_version', 4)->nullable();
            $table->string('domain', 128)->unique();
            $table->string('document_root');
            $table->enum('web_server', ['apache2', 'nginx'])->default('apache2');
            $table->string('framework', 32)->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('ssl_enabled')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosts');
    }
};
