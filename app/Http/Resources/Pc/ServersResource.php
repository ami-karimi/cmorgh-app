<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'server_location' => $this->server_location,
            'flag' => $this->flag,
            'ipaddress' => $this->ipaddress,
            'l2tp_address' => $this->l2tp_address,
        ];
    }
}
