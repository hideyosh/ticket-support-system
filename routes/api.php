<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LabelController;
use App\Http\Controllers\Api\PriorityController;
use App\Http\Controllers\Api\SlaRuleController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TicketActions\UpdateTicketPriorityController;
use App\Http\Controllers\Api\TicketActions\ManageTicketLabelsController;
use App\Http\Controllers\Api\TicketActions\SubmitTicketReplyController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Management (Admin Only)
    Route::get('/users', [UserController::class, 'index']);

    // User Profile
    Route::get('/profile', [UserController::class, 'showProfile']);
    Route::put('/profile/edit', [UserController::class, 'updateProfile']);

    // Read Category, Label, Priority, SLA Rule, Team, Role
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/labels', [LabelController::class, 'index']);
    Route::get('/priorities', [PriorityController::class, 'index']);
    Route::get('/sla-rules', [SlaRuleController::class, 'index']);
    Route::get('/teams', [TeamController::class, 'index']);
    Route::get('/roles', [RoleController::class, 'index']);

    // CRUD Ticket
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::post('/tickets/create', [TicketController::class, 'store']);
    Route::delete('/tickets/{ticket}/delete', [TicketController::class, 'destroy']);

    // Assign & status
    Route::patch('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'status'])->name('tickets.status');

});
