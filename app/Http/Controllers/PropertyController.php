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

		if ( $request->filled('municipality') ) {
			$where[] = [ 'municipality', $request->input('municipality') ];
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

		if ( $request->filled('sqft-greater') ) {
			$where[] = [ 'square_footage', '>=', $request->input('sqft-greater') ];
			$where[] = [ 'square_footage', '!=', 0 ];
		}

		if ( $request->filled('sqft-less') ) {
			$where[] = [ 'square_footage', '<=', $request->input('sqft-less') ];
			$where[] = [ 'square_footage', '!=', 0 ];
		}

		if ( $request->filled('acre-greater') ) {
			$where[] = [ 'acres', '>=', $request->input('acre-greater') ];
			$where[] = [ 'acres', '!=', 0 ];
		}

		if ( $request->filled('acre-less') ) {
			$where[] = [ 'acres', '<=', $request->input('acre-less') ];
			$where[] = [ 'acres', '!=', 0 ];
		}

		if ( $request->filled('value-greater') ) {
			$where[] = [ 'fmv_value', '>=', $request->input('value-greater') ];
			$where[] = [ 'fmv_value', '!=', 0 ];
		}

		if ( $request->filled('value-less') ) {
			$where[] = [ 'fmv_value', '<=', $request->input('value-less') ];
			$where[] = [ 'fmv_value', '!=', 0 ];
		}

		return $where;
	}
}
