<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model {

	protected $hidden = [ 'id', 'created_at', 'updated_at' ];

	protected $appends = [ 'property_url' ];

	protected $fillable = [
		'detail_id',
		'name',
		'secondary_name',
		'street',
		'city',
		'state',
		'zipcode',
	];

	public function details() {
		return $this->belongsTo( 'App\Models\Detail' );
	}

	public function getPropertyUrlAttribute() {
		return url("/api/v1/property/{$this->detail_id}");
	}
}
