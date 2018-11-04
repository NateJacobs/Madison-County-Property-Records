<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Jobs\GetReports;
use App\Jobs\ProcessProperty;

class ReportController extends Controller {
	public $client;

	public function __construct() {}

	public function process( Request $request ) {
		// $records = \App\Models\Detail::where('id', '>', 5)->take(5)->get();
		$records = \App\Models\Detail::all();

		foreach ( $records as $property ) {
			dispatch( new ProcessProperty( $property ) );
		}
	}

	public function get( Request $request ) {
		$city_code = [
			'Oneida' => '2512',
			'Brookfield' => '2520',
			'Cazenovia' => '2522',
			'DeRuyter' => '2524',
			'Eaton' => '2526',
			'Fenner' => '2528',
			'Georgetown' => '2530',
			'Hamilton' => '2532',
			'Lebanon' => '2534',
			'Lenox' => '2536',
			'Lincoln' => '2538',
			'Madison' => '2540',
			'Nelson' => '2542',
			'Smithfield' => '2544',
			'Stockbridge' => '2546',
			'Sullivan' => '2548',
		];
		foreach ( $city_code as $key => $city ) {
			$url = 'https://property.madisoncounty.ny.gov/ImateWeb/viewlist.aspx?sort=printkey&swis='.$city;
			$pass_thru = [ 'url' => $url, 'city' => $key ];
			dispatch( new GetReports( $pass_thru ) );
		}

		return json_encode(['The jobs have been queued']);

	}
}

// $file = Storage::get('brookfield.json');
