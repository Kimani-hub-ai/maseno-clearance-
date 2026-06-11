<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reg_number')->unique();   // e.g. MAS/001/2020
            $table->string('full_name');
            $table->string('faculty');
            $table->string('department');
            $table->string('programme');
            $table->year('graduation_year');
            $table->string('phone', 15)->nullable();
            $table->enum('status', ['active', 'completed', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
