<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model {

	protected $hidden = [ 'created_at', 'updated_at' ];

	protected $fillable = [
		'detail_id',
		'date',
		'price',
		'prior_owner',
	];

	public function details() {
		return $this->belongsTo( 'App\Models\Detail' );
	}
}
