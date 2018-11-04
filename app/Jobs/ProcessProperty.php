<?php

namespace App\Jobs;

use App\Models\Detail;
use App\Http\Controllers\GoutteController as Scrapper;
use App\Library\Scrapper\PropertyDetails;
use App\Library\Scrapper\PropertyOwners;
use App\Library\Scrapper\PropertySales;
use Illuminate\Support\Facades\Log;

class ProcessProperty extends Job {
	protected $detail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Detail $detail ) {
        $this->detail = $detail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $scrapper = new Scrapper();
		$helper = new PropertyDetails();
		$owners_class = new PropertyOwners();
		$sales_class = new PropertySales();

		$report_crawler = $scrapper->client->request( 'GET', $this->detail->report_url );

		$acreage = $helper->get_acreage( $report_crawler );
		$property_class = $helper->get_property_class( $report_crawler );
		$municipality = $helper->get_municipality( $report_crawler );
		$street = $helper->get_address( $report_crawler );
		$tax_id = $helper->get_tax_id( $report_crawler );
		$neighborhood_code = $helper->get_neighborhood_code( $report_crawler );
		$full_market_value = $helper->get_market_value( $report_crawler );
		$structure = $helper->get_structure_data( $report_crawler );
		$area = $helper->get_area_data( $report_crawler );
		$description = $helper->get_property_description( $report_crawler );
		$owners = $owners_class->get_owners( $report_crawler );
		$sales = $sales_class->get_prior_sales( $report_crawler );

		$this->detail->fill([
			'street_number' => $street['number'],
			'street_name' => $street['name'],
			'municipality' => $municipality['municipality'],
			'swis_code' => $municipality['swis_code'],
			'class_code' => $property_class['code'],
			'class_description' => $property_class['description'],
			'acres' => $acreage,
			'tax_id' => $tax_id,
			'neighborhood_code' => $neighborhood_code,
			'fmv_year' => $full_market_value['year'],
			'fmv_value' => $full_market_value['value'],
			'square_footage' => $structure['square_footage'],
			'stories' => $structure['stories'],
			'bedrooms' => $area['bedrooms'],
			'full_baths' => $area['full_baths'],
			'half_baths' => $area['half_baths'],
			'year_built' => $area['year_built'],
			'description' => $description,
		]);

		if ( ! empty( $owners ) ) {
			foreach ( $owners as $owner ) {
				$this->detail->owners()->updateOrCreate(
					[
						'detail_id' => $this->detail->id,
						'name' => $owner['name'],
						'secondary_name' => $owner['secondary_name'],
						'street' => $owner['street'],
						'city' => $owner['city'],
						'state' => $owner['state'],
						'zipcode' => $owner['zipcode'],
					]
				);
			}
		}

		if ( ! empty( $sales ) ) {
			foreach ( $sales as $sale ) {
				$this->detail->sales()->updateOrCreate(
					[
						'detail_id' => $this->detail->id,
						'date' => $sale['date'],
						'price' => $sale['price'],
						'prior_owner' => $sale['prior_owner']
					]
				);
			}
		}

		$this->detail->save();
    }
}
