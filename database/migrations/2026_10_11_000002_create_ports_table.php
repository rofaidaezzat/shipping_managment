<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->id();                                          // BIGINT AUTO_INCREMENT PK
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('type', 50)->default('Sea');
            $table->string('name', 150);
            $table->string('country', 100)->nullable();
            $table->string('code', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Composite unique: code must be unique within the same company
            $table->unique(['company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
