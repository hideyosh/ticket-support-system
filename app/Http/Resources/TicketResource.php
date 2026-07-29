<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'ticket_number'  => $this->ticket_number,
            'title'          => $this->title,
            'description'    => $this->description,
            'status'         => $this->status,
            'due_date'       => $this->due_date?->toIso8601String(),
            'created_at'     => $this->created_at->toIso8601String(),
            'category'       => $this->whenLoaded('category', fn () => [
                'id'   => $this->category->id,
                'name' => $this->category->category_name,
            ]),
            'priority'       => $this->whenLoaded('priority', fn () => [
                'id'   => $this->priority->id,
                'name' => $this->priority->priority_name,
            ]),
            'labels'         => $this->whenLoaded('labels', fn () => $this->labels->map(fn ($label) => [
                'id'   => $label->id,
                'name' => $label->label_name,
            ])),
            'creator'        => $this->whenLoaded('creator', fn () => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'assigned_agent' => $this->whenLoaded('assignedAgent', fn () => $this->assignedAgent ? [
                'id'   => $this->assignedAgent->id,
                'name' => $this->assignedAgent->name,
            ] : null),
        ];
    }
}
