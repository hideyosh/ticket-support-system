<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

class CommentPolicy
{
    /**
     * Melihat daftar komentar.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Melihat detail komentar.
     */
    public function view(User $user, Comment $comment): bool
    {
        $roleName = $user->role?->role_name;

        // Customer tidak boleh melihat internal_note
        if ($roleName === 'customer' && $comment->type === 'internal_note') {
            return false;
        }

        return true;
    }

    /**
     * Membuat komentar sesuai tipe.
     */
    public function create(User $user, string $type = 'public_comment'): bool
    {
        $roleName = $user->role?->role_name;

        // Customer tidak boleh membuat internal_note
        if ($roleName === 'customer' && $type === 'internal_note') {
            return false;
        }

        return true;
    }

    /**
     * Memperbarui komentar.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Hanya pemilik komentar yang boleh mengubah
        return $user->id === $comment->user_id;
    }

    /**
     * Menghapus komentar.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Hanya pemilik komentar yang boleh menghapus
        return $user->id === $comment->user_id;
    }
}
