<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;

class GoutteController extends Controller {
	public $client;

	public function __construct() {
		$session_cookie = new Cookie('LastSessID', env('PROPERTY_SESSION_ID'), strtotime('+1 year'));
		$asp_cookie = new Cookie('ASP.NET_SessionId', env('PROPERTY_SESSION_ID'), strtotime('+1 year'));
		$cookieJar = new CookieJar();
		$cookieJar->set($session_cookie);
		$cookieJar->set($asp_cookie);
		$this->client = new Client( array(), null, $cookieJar );

		return $this;
	}
}
