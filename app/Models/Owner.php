<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model {

	protected $hidden = [ 'created_at', 'updated_at' ];

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
}
