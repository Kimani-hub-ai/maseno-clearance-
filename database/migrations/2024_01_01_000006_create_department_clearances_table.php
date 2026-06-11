<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                  ->constrained('clearance_applications')
                  ->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // One record per department per application
            $table->unique(['application_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_clearances');
    }
};
