<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('system_logs')) {
            Schema::create('system_logs', function (Blueprint $table) {
                $table->id();
                $table->string('level', 20)->default('error'); // error, warning, info, critical, debug
                $table->text('message');
                $table->string('exception', 255)->nullable();
                $table->string('file', 500)->nullable();
                $table->integer('line')->nullable();
                $table->longText('stack_trace')->nullable();
                $table->string('user', 150)->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('company', 150)->nullable();
                $table->text('url')->nullable();
                $table->string('method', 15)->nullable();
                $table->string('route_name', 150)->nullable();
                $table->string('ip', 45)->nullable();
                $table->timestamps();

                // Indexes for fast querying & filtering
                $table->index('level');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
