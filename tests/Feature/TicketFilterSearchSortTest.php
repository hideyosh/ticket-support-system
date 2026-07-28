<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketFilterSearchSortTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $agent;
    protected User $customer;
    protected Category $category;
    protected Priority $priorityHigh;
    protected Priority $priorityLow;
    protected Label $labelBug;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole    = Role::create(['role_name' => 'admin']);
        $agentRole    = Role::create(['role_name' => 'agent']);
        $customerRole = Role::create(['role_name' => 'customer']);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'name'    => 'Admin User',
            'email'   => 'admin@example.com',
        ]);

        $this->agent = User::factory()->create([
            'role_id' => $agentRole->id,
            'name'    => 'Agent One',
            'email'   => 'agent1@example.com',
        ]);

        $this->customer = User::factory()->create([
            'role_id' => $customerRole->id,
            'name'    => 'Budi Customer',
            'email'   => 'budi@example.com',
        ]);

        $this->category     = Category::create(['category_name' => 'Technical']);
        $this->priorityHigh = Priority::create(['priority_name' => 'High', 'sla_hours' => 24]);
        $this->priorityLow  = Priority::create(['priority_name' => 'Low', 'sla_hours' => 72]);
        $this->labelBug     = Label::create(['label_name' => 'Bug', 'color' => '#ff0000']);
    }

    public function test_scope_filter_role_scoping_for_agent(): void
    {
        $ticketAgent = Ticket::create([
            'ticket_number' => 'TCK-2026-000001',
            'title'         => 'Agent Ticket',
            'description'   => 'Assigned to Agent One',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityHigh->id,
            'status'        => 'open',
            'created_by'    => $this->customer->id,
            'assigned_to'   => $this->agent->id,
        ]);

        $otherTicket = Ticket::create([
            'ticket_number' => 'TCK-2026-000002',
            'title'         => 'Unassigned Ticket',
            'description'   => 'Not assigned',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityLow->id,
            'status'        => 'open',
            'created_by'    => $this->customer->id,
            'assigned_to'   => null,
        ]);

        $agentTickets = Ticket::filter([], $this->agent)->get();
        $this->assertCount(1, $agentTickets);
        $this->assertTrue($agentTickets->contains($ticketAgent));
        $this->assertFalse($agentTickets->contains($otherTicket));

        $adminTickets = Ticket::filter([], $this->admin)->get();
        $this->assertCount(2, $adminTickets);
    }

    public function test_scope_filter_multi_column_search(): void
    {
        $t1 = Ticket::create([
            'ticket_number' => 'TCK-MATCH-001',
            'title'         => 'Printer broken',
            'description'   => 'Issue with hardware',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityHigh->id,
            'status'        => 'open',
            'created_by'    => $this->customer->id,
        ]);

        $t2 = Ticket::create([
            'ticket_number' => 'TCK-2026-002',
            'title'         => 'Network error',
            'description'   => 'Cannot connect printer',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityLow->id,
            'status'        => 'open',
            'created_by'    => $this->customer->id,
        ]);

        $resultsNumber = Ticket::filter(['search' => 'TCK-MATCH-001'], $this->admin)->get();
        $this->assertCount(1, $resultsNumber);
        $this->assertTrue($resultsNumber->contains($t1));

        $resultsDescription = Ticket::filter(['search' => 'hardware'], $this->admin)->get();
        $this->assertCount(1, $resultsDescription);
        $this->assertTrue($resultsDescription->contains($t1));

        $resultsCustomerName = Ticket::filter(['search' => 'Budi'], $this->admin)->get();
        $this->assertCount(2, $resultsCustomerName);

        $resultsCustomerEmail = Ticket::filter(['search' => 'budi@example.com'], $this->admin)->get();
        $this->assertCount(2, $resultsCustomerEmail);
    }

    public function test_scope_filter_overdue_tickets(): void
    {
        $overdueTicket = Ticket::create([
            'ticket_number' => 'TCK-OVERDUE-01',
            'title'         => 'Overdue Ticket',
            'description'   => 'Due in past',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityHigh->id,
            'status'        => 'in_progress',
            'created_by'    => $this->customer->id,
            'due_date'      => now()->subDays(2),
        ]);

        $normalTicket = Ticket::create([
            'ticket_number' => 'TCK-NORMAL-01',
            'title'         => 'Normal Ticket',
            'description'   => 'Due in future',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityLow->id,
            'status'        => 'in_progress',
            'created_by'    => $this->customer->id,
            'due_date'      => now()->addDays(2),
        ]);

        $resolvedOverdueTicket = Ticket::create([
            'ticket_number' => 'TCK-RESOLVED-01',
            'title'         => 'Resolved Past Due Ticket',
            'description'   => 'Was past due but resolved',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityHigh->id,
            'status'        => 'resolved',
            'created_by'    => $this->customer->id,
            'due_date'      => now()->subDays(2),
        ]);

        $results = Ticket::filter(['overdue' => '1'], $this->admin)->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($overdueTicket));
        $this->assertFalse($results->contains($normalTicket));
        $this->assertFalse($results->contains($resolvedOverdueTicket));
    }

    public function test_scope_filter_labels_and_sorting(): void
    {
        $t1 = Ticket::create([
            'ticket_number' => 'TCK-AAA',
            'title'         => 'Ticket A',
            'description'   => 'Desc A',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityLow->id,
            'status'        => 'open',
            'created_by'    => $this->customer->id,
            'created_at'    => now()->subDays(5),
        ]);
        $t1->labels()->attach($this->labelBug->id);

        $t2 = Ticket::create([
            'ticket_number' => 'TCK-BBB',
            'title'         => 'Ticket B',
            'description'   => 'Desc B',
            'category_id'   => $this->category->id,
            'priority_id'   => $this->priorityHigh->id,
            'status'        => 'open',
            'created_by'    => $this->customer->id,
            'created_at'    => now()->subDays(1),
        ]);

        $bugTickets = Ticket::filter(['label_id' => $this->labelBug->id], $this->admin)->get();
        $this->assertCount(1, $bugTickets);
        $this->assertTrue($bugTickets->contains($t1));

        $sortedAsc = Ticket::filter(['sort_by' => 'created_at', 'sort_order' => 'asc'], $this->admin)->get();
        $this->assertEquals('TCK-AAA', $sortedAsc->first()->ticket_number);

        $sortedDesc = Ticket::filter(['sort_by' => 'created_at', 'sort_order' => 'desc'], $this->admin)->get();
        $this->assertEquals('TCK-BBB', $sortedDesc->first()->ticket_number);
    }
}
