<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void{
        $connection = config('activitylog.database_connection');
        $tableName  = config('activitylog.table_name', 'activity_log');

        if (!Schema::connection($connection)->hasTable($tableName)) {
            Schema::connection($connection)->create($tableName, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->nullableMorphs('subject', 'subject');
                $table->string('event')->nullable();
                $table->nullableMorphs('causer', 'causer');
                $table->string('created_by')->nullable();
                $table->string('causer_name')->nullable();
                $table->string('causer_email')->nullable();
                $table->json('properties')->nullable();
                $table->uuid('batch_uuid')->nullable();
                $table->timestamps();

                $table->index('log_name');
                $table->index('created_by');
            });
        }
    }

    public function down(): void
    {
        $connection = config('activitylog.database_connection');
        $tableName  = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->dropIfExists($tableName);
    }
};
