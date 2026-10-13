<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();                                          // BIGINT AUTO_INCREMENT PK
            $table->foreignId('port_id')->nullable()->constrained('ports')->restrictOnDelete();
            $table->string('container_number', 20)->unique();      // Global unique
            $table->enum('type', ['20GP', '40GP', '40HC']);
            $table->enum('status', ['AVAILABLE', 'IN_USE', 'MAINTENANCE']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
