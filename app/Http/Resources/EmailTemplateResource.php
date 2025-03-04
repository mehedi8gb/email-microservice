<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Regular expression to find all {{variable_name}} in the body
        preg_match_all('/\{\{(.*?)}}/', $this->body, $matches);

        // Extract the variable names (the part inside {{ }})
        $variables = $matches[1] ?? [];

        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'body' => $this->body,
            'variables' => $variables,  // Return the extracted variables
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

}
