<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // Ensure status column on receipts
            if (\Illuminate\Support\Facades\Schema::hasTable('receipts') && !\Illuminate\Support\Facades\Schema::hasColumn('receipts', 'status')) {
                \Illuminate\Support\Facades\Schema::table('receipts', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('status', 20)->default('completed')->after('payment_method');
                });
            }

            // Ensure system_logs table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('system_logs')) {
                \Illuminate\Support\Facades\Schema::create('system_logs', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('level', 20)->default('error');
                    $table->text('message');
                    $table->string('exception', 255)->nullable();
                    $table->string('file', 500)->nullable();
                    $table->integer('line')->nullable();
                    $table->longText('stack_trace')->nullable();
                    $table->string('user', 150)->nullable();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->unsignedBigInteger('created_by')->nullable(); // FK → users.id
                    $table->string('company', 150)->nullable();
                    $table->text('url')->nullable();
                    $table->string('method', 15)->nullable();
                    $table->string('route_name', 150)->nullable();
                    $table->string('ip', 45)->nullable();
                    $table->timestamps();

                    $table->index('level');
                    $table->index('created_at');
                });
            } elseif (!\Illuminate\Support\Facades\Schema::hasColumn('system_logs', 'created_by')) {
                // Add created_by to existing system_logs table
                \Illuminate\Support\Facades\Schema::table('system_logs', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('user_id');
                });
            }

            // Ensure created_by, causer_name, causer_email columns on activity_log
            if (\Illuminate\Support\Facades\Schema::hasTable('activity_log')) {
                \Illuminate\Support\Facades\Schema::table('activity_log', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_log', 'created_by')) {
                        $table->string('created_by')->nullable()->after('causer_id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_log', 'causer_name')) {
                        $table->string('causer_name')->nullable()->after('created_by');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_log', 'causer_email')) {
                        $table->string('causer_email')->nullable()->after('causer_name');
                    }
                });
            }

            // Ensure is_super_admin and role columns on users table
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_super_admin')) {
                        $table->boolean('is_super_admin')->default(false)->after('password');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
                        $table->string('role', 50)->default('Employee')->after('is_super_admin');
                    }
                });

                // Ensure default test accounts exist
                if (!\App\Models\User::where('email', 'admin@shipping.com')->exists()) {
                    \App\Models\User::create([
                        'name'           => 'Master Super Admin',
                        'email'          => 'admin@shipping.com',
                        'password'       => \Illuminate\Support\Facades\Hash::make('password123'),
                        'role'           => 'Super Admin',
                        'is_super_admin' => true,
                    ]);
                }

                if (!\App\Models\User::where('email', 'employee@shipping.com')->exists()) {
                    \App\Models\User::create([
                        'name'           => 'John Employee',
                        'email'          => 'employee@shipping.com',
                        'password'       => \Illuminate\Support\Facades\Hash::make('password123'),
                        'role'           => 'Employee',
                        'is_super_admin' => false,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore during initial setup or migration commands
        }
    }
}
