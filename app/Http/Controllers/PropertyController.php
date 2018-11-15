<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Detail as DetailResource;
use App\Http\Resources\DetailCollection;
use App\Models\Detail;

class PropertyController extends Controller {
	/**
	 *	Return a single property record by ID.
	 *
	*/
	public function show( Request $request ) {
		return new DetailCollection(
			Detail::with( [ 'sales', 'owners' ] )->where( 'id', $request->id )->orderBy('street_number')->simplePaginate()
		);
	}

	/**
	 *	Return the property records that match the filter query.
	 *
	*/
	public function index( Request $request ) {
		$number_of_results = 50;

		$where = $this->build_search_where( $request );

		if ( empty( $where ) && false === $request->filled('owner-name') ) {
			return response()->json([
				'error_msg' => 'You must pass at least one search query'
			]);
		}

		if ( $request->filled('owner-name') ) {
			$owners = \DB::table('owners')->where( 'name', 'like', '%'.$request->input('owner-name').'%' )->get();
			$owner_array = array_unique( $owners->pluck('detail_id')->toArray() );

			$property_record = Detail::whereIn( 'id', $owner_array )->orderBy('street_number')->paginate( $number_of_results );
		} else {
			$property_record = Detail::where( $where )->orderBy('street_number')->paginate( $number_of_results );
		}

		$property_record->appends( $request->input() )->links();

		if ( $request->filled('include') ) {
			$property_record = $this->add_lazy_load( $property_record, $request->input('include') );
		}

		return new DetailCollection( $property_record );
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
			$where[] = [ 'class_code', $request->input('class') ];
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

		if ( $request->filled('bedrooms') ) {
			$where[] = [ 'bedrooms', $request->input('bedrooms') ];
		}

		if ( $request->filled('full-baths') ) {
			$where[] = [ 'full_baths', $request->input('full-baths') ];
		}

		if ( $request->filled('half-baths') ) {
			$where[] = [ 'half_baths', $request->input('half-baths') ];
		}

		return $where;
	}
}
