<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Detail as DetailResource;
use App\Http\Resources\OwnerCollection;
use App\Models\Owner;

class OwnerController extends Controller {
	public function show( Request $request ) {
		return new OwnerCollection(
			Owner::with( [ 'details' ] )->where( 'id', $request->id )->simplePaginate()
		);
	}
}
