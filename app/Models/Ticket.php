<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(
    'ticket_number',
    'title',
    'description',
    'category_id',
    'priority_id',
    'status',
    'created_by',
    'assigned_to',
    'due_date',
    'resolved_at',
    'closed_at'
)]
class Ticket extends Model
{
    protected function casts(): array
    {
        return [
            'due_date'    => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at'   => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'ticket_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'ticket_label', 'ticket_id', 'label_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'bg-info',
            'assigned' => 'bg-primary',
            'in_progress' => 'bg-warning',
            'waiting_for_customer' => 'bg-light text-dark',
            'resolved' => 'bg-success',
            'closed' => 'bg-secondary',
            'reopened' => 'bg-danger',
            'escalated' => 'bg-danger',
        };
    }

    public function customerVisibleLogs()
    {
        return $this->activityLogs()
            ->whereIn('action', [
                'Ticket created',
                'Status changed',
                'Comment added',
                'Attachment uploaded',
                'Ticket resolved',
                'Ticket reopened',
            ])
            ->latest();
    }

    public function scopeFilter(Builder $query, array $filters, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        // 1. Role-based scoping & Assigned Agent filter
        if ($user && $user->role?->role_name === 'agent') {
            $query->where('assigned_to', $user->id);
        } elseif (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        // 2. Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 3. Priority
        if (!empty($filters['priority_id'])) {
            $query->where('priority_id', $filters['priority_id']);
        }

        // 4. Category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // 5. Label
        if (!empty($filters['label_id'])) {
            $query->whereHas('labels', function (Builder $q) use ($filters) {
                $q->where('labels.id', $filters['label_id']);
            });
        }

        // 6. Created Date Range
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        // 7. Due Date Range
        if (!empty($filters['due_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_from']);
        }
        if (!empty($filters['due_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_to']);
        }

        // 8. Overdue Only (status != closed/resolved and due_date < now())
        if (!empty($filters['overdue']) && filter_var($filters['overdue'], FILTER_VALIDATE_BOOLEAN)) {
            $query->whereNotIn('status', ['closed', 'resolved'])
                ->where('due_date', '<', now());
        }

        // 9. Multi-Column Search (ticket_number, title, description, customer name, customer email)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('creator', function (Builder $userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // 10. Sorting (created_at, updated_at, priority_id, due_at/due_date, status)
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = isset($filters['sort_order']) && strtolower($filters['sort_order']) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'created_at'  => 'created_at',
            'updated_at'  => 'updated_at',
            'priority_id' => 'priority_id',
            'due_at'      => 'due_date',
            'due_date'    => 'due_date',
            'status'      => 'status',
        ];

        $sortColumn = $allowedSorts[$sortBy] ?? 'created_at';
        $query->orderBy($sortColumn, $sortOrder);

        return $query;
    }
}
