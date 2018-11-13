<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;

class LogRoutes {

	public function handle( $request, Closure  $next ) {
		Log::info(
			'app.request',
			[
				'request' => $request->all(),
				'header' => $request->headers->all(),
				'path' => $request->path(),
				'method' => $request->method(),
				'ip' => $request->ip(),
			]
		);

        return $next($request);
	}

	public function terminate( $request, $response ) {
		$response_array = json_decode( $response->getContent() );

		if ( isset( $response_array->meta->total ) ) {
			Log::info(
				'app.response',
				[ 'status' => $response->status(), 'results_count' => $response_array->meta->total ]
			);
		} else {
			Log::info(
				'app.response',
				[ 'status' => $response->status(), 'results' => $response->getContent() ]
			);
		}

	}

}
