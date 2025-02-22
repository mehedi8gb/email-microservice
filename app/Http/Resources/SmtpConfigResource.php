<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmtpConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
//            'company' => CompanyResource::make($this->company),
            'host' => $this->host,
            'port' => $this->port,
            'from_email' => $this->from_email,
            'from_name' => $this->from_name,
            'username' => $this->username,
            'encryption' => $this->encryption,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
