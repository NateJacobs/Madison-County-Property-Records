<?php
namespace App\Library\Scrapper;

use App\Library\Scrapper\PropertyDetails;

class PropertySales {

	public function get_prior_sales( $report ) {
		$details = new PropertyDetails();
		$is_present = $details->check_if_section_present( $report, 'pnlSale' );

		if ( true === $is_present && true === $this->check_if_prior_sales( $report ) ) {
			$prior_sales = $report->filter('table#tblSales > tr')->each(function ($node) {
				$sales_array_exploded = array_map( 'trim', explode( '<td>', $node->html() ) );

				// eliminate the header row
				if ( count( $sales_array_exploded ) > 1 ) {
					$prior_owner = $this->get_owner_name( $sales_array_exploded[5] );
					$sales_price = $this->get_sales_price( $sales_array_exploded[2] );
					$sales_date = $this->get_sales_date( $sales_array_exploded[1] );

					$prior_sales = [
						'date' => $sales_date,
						'price' => $sales_price,
						'prior_owner' => html_entity_decode( $prior_owner ),
					];
				} else {
					$prior_sales = [];
				}

				return $prior_sales;
			});
		} else {
			$prior_sales = [];
		}

		return array_filter( $prior_sales );
	}

	private function check_if_prior_sales( $report ) {
		$sales = $report->filter('table#tblSales > tr')->extract('_text');

		if ( empty( $sales ) ) {
			$prior_sales = false;
		} else {
			$prior_sales = true;
		}

		return $prior_sales;
	}

	private function get_owner_name( $input ) {
		$prior_owner = array_map( 'trim', explode( ',', $input ) );

		if ( isset( $prior_owner[1] ) ) {
			$prior_owner_string = rtrim( $prior_owner[1], '</td>' ).' '.$prior_owner[0];
		} else {
			$prior_owner_string = rtrim( $prior_owner[0], '</td>' );
		}

		return $prior_owner_string;
	}

	private function get_sales_price( $input ) {
		return str_replace( ',', '', ltrim( rtrim( $input, '</td>' ), '$' ) );
	}

	private function get_sales_date( $input ) {
		$date = rtrim( $input, '</td>' );

		return date( "Y-m-d", strtotime( $date ) );
	}
}
