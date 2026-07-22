<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained('categories', 'id')->onDelete('restrict');
            $table->foreignId('priority_id')->constrained('priorities', 'id')->onDelete('restrict');
            $table->enum('status', ['open', 'assigned', 'in_progress', 'waiting_for_customer', 'resolved', 'closed', 'reopened', 'escalated'])->default('open');
            $table->foreignId('created_by')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */ 
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
