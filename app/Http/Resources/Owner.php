<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Owner extends JsonResource {

    public function toArray( $request ) {
        return [
        	'property_id' => $this->detail_id,
        	'owner_name' => $this->name,
			'owner_name_two' => $this->secondary_name,
			'full_street_address' => $this->street,
			'city' => $this->city,
			'state' => $this->state,
			'zipcode' => $this->zipcode,
			'property' => new OwnerDetailCollection( $this->whenLoaded( 'details' ) ),
			'links' => [
				'self' => route( 'singleOwner', [ 'id' => $this->id ] ),
				'property' => route( 'singleProperty', [ 'id' => $this->detail_id ] ),
			]
        ];
    }
}
