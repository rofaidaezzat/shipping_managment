<?php

use App\Http\Controllers\Companies\CompanyController;
use App\Http\Controllers\Containers\ContainerController;
use App\Http\Controllers\Ports\PortController;
use Illuminate\Support\Facades\Route;

// Redirect home to companies
Route::get('/', function () {
    return redirect()->route('companies.index');
});

// ── Companies Routes ─────────────────────────────────────────────────
Route::get('/companies',                  [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/create',           [CompanyController::class, 'create'])->name('companies.create');
Route::post('/companies',                 [CompanyController::class, 'store'])->name('companies.store');
Route::get('/companies/{company}',        [CompanyController::class, 'show'])->name('companies.show');
Route::get('/companies/{company}/edit',   [CompanyController::class, 'edit'])->name('companies.edit');
Route::put('/companies/{company}',        [CompanyController::class, 'update'])->name('companies.update');
Route::delete('/companies/{company}',     [CompanyController::class, 'destroy'])->name('companies.destroy');

// ── Ports Routes ──────────────────────────────────────────────────────
Route::get('/ports',                      [PortController::class, 'index'])->name('ports.index');
Route::get('/ports/export',               [PortController::class, 'export'])->name('ports.export');
Route::get('/ports/create',               [PortController::class, 'create'])->name('ports.create');
Route::post('/ports',                     [PortController::class, 'store'])->name('ports.store');
Route::get('/ports/{port}',               [PortController::class, 'show'])->name('ports.show');
Route::get('/ports/{port}/edit',          [PortController::class, 'edit'])->name('ports.edit');
Route::put('/ports/{port}',               [PortController::class, 'update'])->name('ports.update');
Route::delete('/ports/{port}',            [PortController::class, 'destroy'])->name('ports.destroy');

// ── Containers Routes ─────────────────────────────────────────────────
Route::get('/containers',                 [ContainerController::class, 'index'])->name('containers.index');
Route::get('/containers/create',          [ContainerController::class, 'create'])->name('containers.create');
Route::post('/containers',                [ContainerController::class, 'store'])->name('containers.store');
Route::get('/containers/{container}',     [ContainerController::class, 'show'])->name('containers.show');
Route::get('/containers/{container}/edit',[ContainerController::class, 'edit'])->name('containers.edit');
Route::put('/containers/{container}',     [ContainerController::class, 'update'])->name('containers.update');
Route::delete('/containers/{container}',  [ContainerController::class, 'destroy'])->name('containers.destroy');

// ── Invoices Routes ───────────────────────────────────────────────────
use App\Http\Controllers\Invoices\InvoiceController;
Route::get('/invoices',                    [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create',             [InvoiceController::class, 'create'])->name('invoices.create');
Route::post('/invoices',                   [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/invoices/{invoice}',          [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoices/{invoice}/edit',     [InvoiceController::class, 'edit'])->name('invoices.edit');
Route::put('/invoices/{invoice}',          [InvoiceController::class, 'update'])->name('invoices.update');
Route::delete('/invoices/{invoice}',       [InvoiceController::class, 'destroy'])->name('invoices.destroy');
