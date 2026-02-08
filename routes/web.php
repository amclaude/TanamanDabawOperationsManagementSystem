<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SetupController;
use App\Models\User;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;



Route::middleware(['not.installed'])->group(function () {
    Route::get('/setup', [SetupController::class, 'index'])->name('setup');
    Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');
});

Route::middleware(['guest', 'ensure.setup'])->group(function () {
    Route::get('/', function () {
        return redirect('/login');
    });

    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Placeholder for Forgot Password (prevents 404 error)
    Route::get('/forgot-password', function () {
        return "<h1>Reset Password</h1><p>Contact Admin.</p>";
    })->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
// Placeholder for Forgot Password
Route::get('/forgot-password', function () {
    return "<h1>Reset Password</h1><p>Contact your system administrator to reset your password.</p>";
})->name('password.request');

Route::middleware(['auth', 'restrict.user', 'role:Admin,Operations Manager'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Clients
Route::middleware(['auth', 'restrict.user'])->group(function () {
    Route::get('/clients', [ClientController::class, 'index'])
        ->middleware('role:Admin,Operations Manager')
        ->name('clients');
    Route::post('/clients', [ClientController::class, 'create'])
        ->middleware('role:Admin,Operations Manager')
        ->name('clients.create');
    Route::put('clients/{id}', [ClientController::class, 'update'])
        ->middleware('role:Admin,Operations Manager')
        ->name('clients.update');
    Route::delete('clients/{id}', [ClientController::class, 'destroy'])
        ->middleware('role:Admin')
        ->name('clients.destroy');
    Route::get('/clients/{id}', [ClientController::class, 'show'])
        ->middleware('role:Admin,Operations Manager') // Restrict to this roles only
        ->name('clients.panel');
});

// Projects
Route::middleware(['auth', 'restrict.user', 'role:Admin,Operations Manager'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects');
    Route::post('/projects', [ProjectController::class, 'create'])
        ->name('projects.create');
    Route::put('projects/{id}', [ProjectController::class, 'update'])
        ->name('projects.update');
    Route::patch('/projects/{id}/complete', [ProjectController::class, 'complete'])
        ->name('projects.complete');
    Route::delete('projects/{id}', [ProjectController::class, 'destroy'])
        ->name('projects.destroy');
    Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.panel');
});

Route::middleware(['auth', 'role:Admin,Operations Manager,Head Landscaper'])->group(function () {
    // Allow Head Landscapers to upload photos
    Route::post('/projects/{id}/upload', [ProjectController::class, 'uploadImage'])->name('projects.upload');
});

// Employees
Route::middleware(['auth', 'restrict.user'])->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('role:Admin,Operations Manager')
        ->name('employees');
    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('role:Admin,Operations Manager')
        ->name('employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])
        ->middleware('role:Admin,Operations Manager')
        ->name('employees.update');
    Route::put('/employees/{id}/deactivate', [EmployeeController::class, 'deactivate'])
        ->middleware('role:Admin')
        ->name('employees.deactivate');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])
        ->middleware('role:Admin,Operations Manager')
        ->name('employees.destroy');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])
        ->middleware('role:Admin,Operations Manager')
        ->name('employees.panel');
});


Route::middleware(['auth', 'restrict.user', 'role:Admin,Operations Manager'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store'); // Add Item
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update'); // Edit Item
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy'); // Delete Item
    Route::post('/inventory/{id}/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
    Route::post('/inventory/{id}/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
});

Route::middleware(['auth', 'restrict.user', 'role:Admin,Operations Manager'])->group(function () {
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::put('/quotes/{id}', [QuoteController::class, 'update'])->name('quotes.update'); // For Edit
    Route::delete('/quotes/{id}', [QuoteController::class, 'destroy'])->name('quotes.destroy');
});

Route::middleware(['auth', 'restrict.user', 'role:Admin,Operations Manager'])->group(function () {
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::put('/invoices/{id}/pay', [App\Http\Controllers\InvoiceController::class, 'markAsPaid'])->name('invoices.pay');
    Route::post('/invoices/{id}/send', [App\Http\Controllers\InvoiceController::class, 'sendEmail'])->name('invoices.send');
    Route::get('/projects/{id}/invoice-data', [ProjectController::class, 'getInvoiceData']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'restrict.user', 'role:Head Landscaper,Field Crew'])->group(function () {
    Route::get('/assigned/projects', [ProjectController::class, 'index'])->name('employee.projects');
    Route::get('/assigned/projects/{id}', [ProjectController::class, 'show'])->name('employee.panel');
});
