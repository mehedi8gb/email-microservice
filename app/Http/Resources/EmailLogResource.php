<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'company'    => CompanyResource::make($this->whenLoaded('company')),
            'from_email' => $this->from_email,
            'to_email'   => $this->to_email,
            'subject'    => $this->subject,
            'message'    => $this->message,
            'status'     => $this->status,
            'other_data' => $this->other_data,
            'error'      => $this->when(!is_null($this->error), $this->error), // ✅ Only added if error exists
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];

    }
}
