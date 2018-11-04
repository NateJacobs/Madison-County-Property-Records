<?php

namespace App\Jobs;

use App\Http\Controllers\GoutteController as Scrapper;

class GetReports extends Job {
	protected $url;
	protected $city;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $pass_thru ) {
		$this->url = $pass_thru['url'];
		$this->city = $pass_thru['city'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
		$scrapper = new Scrapper();
		$main_crawler = $scrapper->client->request( 'GET', $this->url );
		$page_url = explode( 'ImateWeb', $main_crawler->getUri() );

		$total_pages = $main_crawler->filter('span#lblPageCount')->text();
		$x = 1;

		while ( $x <= $total_pages ) {
			$paged_url = $main_crawler->getUri().'&page='.$x;

			$sub_crawler = $scrapper->client->request('GET', $paged_url);

			$uri_array = $sub_crawler->filter('table.reportTable > tr > td.gray0TD > a')->each(function ($node) {
				// get the href for the property
			    $property_detail_uri_parsed = parse_url( $node->attr('href') );
				parse_str($property_detail_uri_parsed['query'], $url_path);
				$full_uri = explode( 'ImateWeb', $node->getUri() );
				$built_uri = $full_uri[0].'/ImateWeb/'.$node->attr('href');

				// return the full uri for each property
				$return = [ 'uri' => $built_uri, 'query_path' => $url_path ];

				return $return;
			});

			// now loop through each property
			foreach ( $uri_array as $uri ) {
				$query_params = http_build_query(
					[
						'swiscode' => $uri['query_path']['swis'],
						'printkey' => $uri['query_path']['printkey'],
						'sitetype' => 'res',
						'siteNum' => '1'
					]
				);

				$detail = \App\Models\Detail::updateOrCreate(
					[ 'report_url' => $page_url[0].'ImateWeb/report.aspx?'.$query_params, 'printkey' => $uri['query_path']['printkey'] ],
					[ 'municipality' => $this->city ]
				);
			}

			$x++;
		}
    }
}
