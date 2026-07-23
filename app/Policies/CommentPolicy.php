<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Memeriksa apakah user diizinkan melihat daftar komentar.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Memeriksa apakah user diizinkan melihat spesifik komentar tertentu.
     */
    public function view(User $user, Comment $comment): Response
    {
        // Safe-navigation operator (?->) mencegah error jika role bernilai null
        $roleName = $user->role?->role_name;

        // Jika user adalah Customer dan komentar bertipe internal_note, tolak akses
        if ($roleName === 'customer' && $comment->type === 'internal_note') {
            return Response::deny('Anda tidak memiliki akses untuk melihat catatan internal.');
        }

        return Response::allow();
    }

    /**
     * Memeriksa apakah user diizinkan membuat komentar berdasarkan tipe yang dikirim.
     *
     * Cara panggil di Controller: $this->authorize('create', [Comment::class, $type]);
     * Cara panggil di Blade: @can('create', [App\Models\Comment::class, 'internal_note'])
     */
    public function create(User $user, string $type = 'public_comment'): Response
    {
        $roleName = $user->role?->role_name;

        // Tolak jika Customer mencoba membuat internal_note
        if ($roleName === 'customer' && $type === 'internal_note') {
            return Response::deny('Hanya Agent, Supervisor, dan Admin yang dapat membuat catatan internal.');
        }

        return Response::allow();
    }

    /**
     * Memeriksa apakah user diizinkan memperbarui komentar.
     */
    public function update(User $user, Comment $comment): Response
    {
        // User hanya dapat memperbarui komentar ciptaannya sendiri
        return $user->id === $comment->user_id
            ? Response::allow()
            : Response::deny('Anda hanya dapat mengubah komentar milik Anda sendiri.');
    }

    /**
     * Memeriksa apakah user diizinkan menghapus komentar.
     */
    public function delete(User $user, Comment $comment): Response
    {
        // Setiap user HANYA dapat menghapus komentar milik mereka sendiri
        return $user->id === $comment->user_id
            ? Response::allow()
            : Response::deny('Anda hanya dapat menghapus komentar milik Anda sendiri.');
    }
}
