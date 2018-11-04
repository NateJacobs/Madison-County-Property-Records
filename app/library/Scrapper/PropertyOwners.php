<?php
namespace App\Library\Scrapper;

use App\Library\Scrapper\PropertyDetails;

class PropertyOwners {
	public function get_owners( $report ) {
		$details = new PropertyDetails();
		$is_present = $details->check_if_section_present( $report, 'pnlOwne' );

		if ( true === $is_present ) {
			$owner_array = $report->filter('div#pnlOwne > div.owner_info')->each(function ($node) {
				$owner_array_exploded = array_map( 'trim', explode( '<br>', $node->html() ) );
				$parts = count( $owner_array_exploded );

				$street = $this->get_street_address( $owner_array_exploded, $parts );
				$city = $this->get_city( $owner_array_exploded[$parts-1] );
				$state = $this->get_state( $owner_array_exploded[$parts-1] );
				$zip_code = $this->get_zipcode( $owner_array_exploded[$parts-1] );

				$secondary_name = $this->get_secondary_name( $owner_array_exploded, $parts );


				return [
					'name' => html_entity_decode( $owner_array_exploded[0] ),
					'secondary_name' => html_entity_decode( $secondary_name ),
					'street' => $street,
					'city' => $city,
					'state' => $state,
					'zipcode' => $zip_code,
				];
			});

			$owners = $owner_array;
		} else {
			$owners = false;
		}

		return $owners;
	}

	private function check_if_expanded_zip( $zip ) {
		return ( '-' === substr( $zip, -5, 1 ) ) ? true : false;
	}

	private function get_secondary_name( $input, $parts ) {
		if ( 4 === $parts ) {
			$secondary_name = $input[1];
		} else {
			$secondary_name = '';
		}

		return $secondary_name;
	}

	private function get_street_address( $input, $parts ) {
		if ( 2 === $parts ) {
			$street = '';
		} else {
			$street = $input[$parts-2];
		}

		return $street;
	}

	private function get_zipcode( $input ) {
		$expanded = $this->check_if_expanded_zip( $input );

		if ( true === $expanded ) {
			$zip_code = substr( $input, -10, 11 );
		} else {
			$zip_code = substr( $input, -5, 6 );
		}

		return $zip_code;
	}

	private function get_city( $input ) {
		$expanded = $this->check_if_expanded_zip( $input );

		if ( true === $expanded ) {
			$city = substr( $input, 0, -14 );
		} else {
			$city = substr( $input, 0, -9 );
		}

		return $city;
	}

	private function get_state( $input ) {
		$expanded = $this->check_if_expanded_zip( $input );

		if ( true === $expanded ) {
			$state = substr( $input, -13, 2 );
		} else {
			$state = substr( $input, -8, 2 );
		}

		return $state;
	}
}
