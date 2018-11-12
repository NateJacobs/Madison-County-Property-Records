<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Sale extends JsonResource {
    public function toArray($request)
    {
        return [
        	'property_id' => $this->detail_id,
			'sales_date' => $this->date,
			'sales_price' => $this->price,
        	'prior_owner_name' => $this->prior_owner,
        ];
    }
}
