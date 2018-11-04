<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model {

	protected $hidden = [ 'created_at', 'updated_at' ];

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
}
