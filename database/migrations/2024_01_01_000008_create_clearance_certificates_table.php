<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearance_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                  ->unique()
                  ->constrained('clearance_applications')
                  ->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('qr_code_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('verification_token')->unique();
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance_certificates');
    }
};