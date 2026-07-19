<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->dashboardRoute());
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resources([
        'users' => Admin\UserController::class,
        'roles' => Admin\RoleController::class,
        'categories' => Admin\CategoryController::class,
        'priorities' => Admin\PriorityController::class,
        'labels' => Admin\LabelController::class,
        'sla-rules' => Admin\SlaRuleController::class,
        'tickets' => Admin\TicketController::class,
        'teams' => Admin\TeamController::class
    ]);
    Route::patch('/tickets/{ticket}/assign', [Admin\TicketController::class, 'assign'])->name('tickets.assign');
    Route::patch('/tickets/{ticket}/status', [Admin\TicketController::class, 'status'])->name('tickets.status');
    Route::put('/tickets/{ticket}/labels', [Admin\TicketController::class, 'labels'])->name('tickets.labels');
    Route::post('/tickets/{ticket}/comments', [Admin\TicketController::class, 'storeComment'])->name('tickets.comments');
    Route::get('/activity-logs', [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
});

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [Customer\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('tickets', Customer\TicketController::class)->only(['index', 'create', 'store', 'show']);

    Route::post('/tickets/{ticket}/attachments', [Customer\AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('/tickets/{ticket}/attachments/{attachment}/download', [Customer\AttachmentController::class, 'download'])->name('attachments.download');
    Route::post('/tickets/{ticket}/comments', [Customer\CommentController::class, 'store'])->name('comments.store');
});

Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [Supervisor\DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
