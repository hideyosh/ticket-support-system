<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable('ticket_id', 'user_id', 'body', 'type')]
class Comment extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Local scope untuk filter komentar berdasarkan role user yang login.
     *
     * - Customer: hanya bisa lihat public_comment
     * - Agent, Supervisor, Admin: bisa lihat semua (public_comment & internal_note)
     *
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->role->role_name === 'customer') {
            return $query->where('type', 'public_comment');
        }

        return $query;
    }
}
