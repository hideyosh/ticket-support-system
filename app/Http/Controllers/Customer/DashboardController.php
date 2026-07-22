<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
// use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [
            'myTicketsCount' => Ticket::where('created_by', $user->id)->count(),

            'openTicketsCount' => Ticket::where('created_by', $user->id)
                ->where('status', 'open')
                ->count(),

            'resolvedTicketsCount' => Ticket::where('created_by', $user->id)
                ->where('status', 'resolved')
                ->count(),

            'recentTickets' => Ticket::where('created_by', $user->id)
                ->with(['category', 'priority', 'assignedAgent'])
                ->orderByDesc('updated_at')
                ->take(5)
                ->get(),
        ];
        return view('customer.dashboard', compact('data'));
    }
}
