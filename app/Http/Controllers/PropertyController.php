<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertyController extends Controller {
	public function show( Request $request ) {

		$where = $this->build_search_where( $request );

		if ( empty( $where ) ) {
			return response()->json([
				'error_msg' => 'You must pass at least one search query'
			]);
		}

		$property_record = \App\Models\Detail::where( $where )->get();

		if ( $request->filled('include') ) {
			$property_record = $this->add_lazy_load( $property_record, $request->input('include') );
		}

		return [
			'count' => $property_record->count(),
			'results' => $property_record,
		];
	}

	private function add_lazy_load( $query, $type ) {
		$include_type = explode( ',', $type );

		if ( 1 < count( $include_type ) ) {
			foreach ( $include_type as $includes ) {
				$query->load( $includes );
			}
		} else {
			$query->load( $type );
		}

		return $query;
	}

	private function build_search_where( $request ) {
		$where = [];

		if ( $request->filled('street-number') ) {
			$where[] = [ 'street_number', $request->input('street-number') ];
		}

		if ( $request->filled('street-name') ) {
			$where[] = [ 'street_name', $request->input('street-name') ];
		}

		if ( $request->filled('city') ) {
			$where[] = [ 'municipality', $request->input('city') ];
		}

		if ( $request->filled('class') ) {
			$where[] = [ 'property_class_code', $request->input('class') ];
		}

		if ( $request->filled('year-built') ) {
			$where[] = [ 'year_built', $request->input('year-built') ];
		}

		if ( $request->filled('built-before') ) {
			$where[] = [ 'year_built', '<=', $request->input('built-before') ];
			$where[] = [ 'year_built', '!=', 0 ];
		}

		if ( $request->filled('built-after') ) {
			$where[] = [ 'year_built', '>=', $request->input('built-after') ];
			$where[] = [ 'year_built', '!=', 0 ];
		}

		if ( $request->filled('sqft-bigger') ) {
			$where[] = [ 'square_footage', '>=', $request->input('sqft-bigger') ];
			$where[] = [ 'square_footage', '!=', 0 ];
		}

		if ( $request->filled('sqft-smaller') ) {
			$where[] = [ 'square_footage', '<=', $request->input('sqft-smaller') ];
			$where[] = [ 'square_footage', '!=', 0 ];
		}

		if ( $request->filled('acre-bigger') ) {
			$where[] = [ 'acres', '>=', $request->input('acre-bigger') ];
			$where[] = [ 'acres', '!=', 0 ];
		}

		if ( $request->filled('acre-smaller') ) {
			$where[] = [ 'acres', '<=', $request->input('acre-smaller') ];
			$where[] = [ 'acres', '!=', 0 ];
		}

		return $where;
	}
}
