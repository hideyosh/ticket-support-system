<?php

use App\Models\Category;
use App\Models\Priority;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;

test('admin can view ticket detail and assign section', function () {
    $adminRole = Role::create(['role_name' => 'admin', 'description' => 'Administrator']);
    $agentRole = Role::create(['role_name' => 'agent', 'description' => 'Agent']);

    $admin = User::factory()->create([
        'name' => 'Admin Utama',
        'role_id' => $adminRole->id,
    ]);

    $agent = User::factory()->create([
        'name' => 'Budi Agent',
        'role_id' => $agentRole->id,
    ]);

    $category = Category::create(['category_name' => 'Bug']);
    $priority = Priority::create(['priority_name' => 'High']);

    $ticket = Ticket::create([
        'ticket_number' => 'TCK-2026-000001',
        'title' => 'Layanan login error',
        'description' => 'Pengguna tidak bisa login setelah reset password.',
        'category_id' => $category->id,
        'priority_id' => $priority->id,
        'status' => 'open',
        'created_by' => $admin->id,
        'assigned_to' => null,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.tickets.show', $ticket));

    $response->assertOk();
    $response->assertSeeText('Detail Tiket');
    $response->assertSeeText('Assign Agent');
    $response->assertSee($agent->name);
});
