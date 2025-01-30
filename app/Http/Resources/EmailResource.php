<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailResource extends JsonResource
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
            'company'    => CompanyResource::make($this->company),
            'subject'    => $this->subject,
            'from_email' => $this->from_email,
            'to_email'   => $this->to_email,
            'message'    => $this->message,
//            'other_data' => $this->other_data,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
