<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model {

	protected $hidden = [ 'created_at', 'updated_at', 'back_up_address' ];

	protected $appends = [ 'full_street_address', 'self_url' ];

	protected $fillable = [
		'municipality',
		'street_number',
		'street_name',
		'acres',
		'tax_id',
		'neighborhood_code',
		'fmv_value',
		'fmv_year',
		'square_footage',
		'stories',
		'bedrooms',
		'full_baths',
		'half_baths',
		'year_built',
		'swis_code',
		'class_code',
		'class_description',
		'description',
	];

	public function owners() {
		return $this->hasMany( 'App\Models\Owner' );
	}

	public function sales() {
		return $this->hasMany( 'App\Models\Sale' );
	}

	public function getFullStreetAddressAttribute() {
		return "{$this->street_number} {$this->street_name}";
	}

	public function getFmvValueAttribute( $value ) {
		return '$'.number_format( $value );
	}

	public function getSelfUrlAttribute() {
		return url("/api/v1/property/{$this->id}");
	}
}
