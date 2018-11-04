<?php

namespace App\Http\Middleware;

use Closure;

class Admin {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

		if ( ! $request->has('auth') ) {
			return abort('404');
		}

		if (
			$request->has('auth') &&
			sha1( env('ADMIN_PASS') ) !== $request->input('auth')
		) {

			return abort('404');
		}

        return $next($request);
    }
}
