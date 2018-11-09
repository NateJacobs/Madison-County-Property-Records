<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Detail extends JsonResource
{

    public function toArray($request)
    {
        return [
        	'property_id' => $this->id,
			'print_key' => $this->printkey,
			'tax_id' => $this->tax_id,
			'swis_code' => $this->swis_code,
			'tax_valuation' => $this->fmv_value,
			'tax_valuation_year' => $this->fmv_year,
        	'street_number' => $this->street_number,
        	'street_name' => $this->street_name,
			'full_street_address' => $this->full_street_address,
			'municipality' => $this->municipality,
			'neighborhood_code' => $this->neighborhood_code,
			'acres' => $this->acres,
			'square_footage' => $this->square_footage,
			'stories' => $this->stories,
			'bedrooms' => $this->bedrooms,
			'full_bathrooms' => $this->full_baths,
			'half_bathrooms' => $this->half_baths,
			'year_built' => $this->year_built,
			'class_code' => $this->class_code,
			'class_description' => $this->class_description,
			'description' => $this->description,
			'owners' => new OwnerCollection( $this->whenLoaded( 'owners' ) ),
			'sales_history' => Sale::collection( $this->whenLoaded( 'sales' ) ),
			'links' => [
				'self' => route( 'singleProperty', [ 'id' => $this->id ] ),
				'report' => $this->report_url,
			]
        ];
    }
}
