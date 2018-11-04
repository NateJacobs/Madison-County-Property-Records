<?php
namespace App\Library\Scrapper;

class PropertyDetails {

	public function check_if_section_present( $report, $section ) {
		$report_sections = $report->filter('div.report_section')->extract('id');

		if ( in_array( $section, $report_sections ) ) {
			$result = true;
		} else {
			$result = false;
		}

		return $result;
	}

	public function get_property_description( $report ) {
		$description = $report->filter('span#lblLegalPropertyDesc')->text();

		if ( empty( $description ) ) {
			$description = '';
		}

		return $description;
	}

    public function get_property_class( $report ) {
		$property_class = $report->filter('span#lblBasePropClass')->text();
		$prop_class_array = explode( '-', $property_class );

		if ( isset( $prop_class_array[1] ) ) {
			return [
				'code' => trim( $prop_class_array[0] ),
				'description' => trim( $prop_class_array[1] ),
			];
		}

		return [
			'code' => '',
			'description' => '',
		];
	}

	public function get_acreage( $report ) {
		$acreage = $report->filter('span#lblTotalAcreage')->text();

		if ( '0 x 0' === $acreage ) {
			$acres = 0;
		} else {
			$acres = $acreage;
		}

		return $acres;
	}

	public function get_municipality( $report ) {
		$swis_code = $report->filter('span#lblSwis')->text();
		$city_swis = substr( $swis_code, 0, 4 );
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

		$municipality = array_search( $city_swis, $city_code );

		return [
			'swis_code' => $swis_code,
			'municipality' => $municipality,
		];
	}

	public function get_address( $report ) {
		$title = $report->filter('span#lblReportTitle')->text();
		preg_match("#\:(.*?)\,#", $title, $match);
		$address_string = ltrim($match[1]);

		$address_array = explode( ' ', $address_string, 3 );

		$street_number = null;
		$street_name = $address_string;

		if ( 1 < count( $address_array ) ) {
			if ( '/' === $address_array[1] ) {
				$street_number = null;
				$street_name = str_replace( ' ', '', $address_string );
			}

			if ( ctype_alpha( substr( $address_array[0], -1 ) ) & is_numeric( substr( $address_array[0], 0, -1 ) ) ) {
				$street_number = $address_array[0];
				$street_name = $address_array[1];
			}

			if ( is_numeric( $address_array[0] ) ) {
				if ( ! is_numeric( $address_array[1] ) ) {
					$street_number = $address_array[0];
					$street_name = $address_array[1];
				} elseif ( is_numeric( $address_array[1] ) ) {
					$street_number = $address_array[0].'-'.$address_array[1];
					$street_name = $address_array[2];
				}
			}

			if ( strpos( $address_array[0], '-' ) ) {
				$num_len = strlen( $address_array[0] );
				$name = substr( join( ' ', $address_array ), $num_len );
				$street_number = trim( $address_array[0] );
				$street_name = trim( $name );
			}

			if ( strpos( $address_array[1], '/' ) & is_numeric( $address_array[0] ) ) {
				$num_len_1 = strlen( $address_array[0] );
				$num_len_2 = strlen( $address_array[1] );
				$num_len = ( $num_len_1 + $num_len_2 ) + 2;
				$name = substr( join( ' ', $address_array ), $num_len );
				$street_number = trim( $address_array[0] .' '.$address_array[1] );
				$street_name = trim( $name );
			}
		}

		return [ 'number' => $street_number, 'name' => $address_string ];
	}

	public function get_tax_id( $report ) {
		return $report->filter('span#lblTaxMapNum')->text();
	}

	public function get_neighborhood_code( $report ) {
		return $report->filter('span#lblNeighborhoodCode')->text();
	}

	public function get_market_value( $report ) {
		$fmv_array = $report->filter('span#lblFullMarketValue')->each(function ($node) {
			$fmv_array = explode( '<br>', $node->html() );
			$latest_year_value = array_map( 'trim', explode( '-', $fmv_array[0] ) );

			return [
				'year' => $latest_year_value[0],
				'value' => str_replace( ',', '', ltrim( $latest_year_value[1], '$' ) ),
			];
		});

		if ( ! is_numeric( $fmv_array[0]['value'] ) ) {
			$fmv_array[0]['value'] = 0;
		}

		if ( ! is_numeric( $fmv_array[0]['year'] ) ) {
			$fmv_array[0]['year'] = 0;
		}

		return [
			'year' => $fmv_array[0]['year'],
			'value' => $fmv_array[0]['value'],
		];
	}

	public function get_structure_data( $report ) {
		// check if structure data is present
		$is_present = $this->check_if_section_present( $report, 'pnlStru' );

		if ( true === $is_present ) {
			// get square footage
			$square_footage = str_replace( ',', '', rtrim( $report->filter('span#lblLivingArea')->text(), 'sq. ft.' ) );

			// get stories
			$number_of_stories = $report->filter('span#lblNumberOfStories')->text();
		} else {
			$square_footage = 0;
			$number_of_stories = 0;
		}

		return [
			'square_footage' => (int) $square_footage,
			'stories' => (int) $number_of_stories,
		];
	}

	public function get_area_data( $report ) {
		// check if area data is present
		$is_present = $this->check_if_section_present( $report, 'pnlArea' );

		if ( true === $is_present ) {
			// get year built
			$year_built = $report->filter('span#lblYearBuilt')->text();
			if ( empty( $year_built ) ) {
				$year_built = 0;
			}

			$number_of_bedrooms = $report->filter('span#lblBedrooms')->text();
			if ( empty( $number_of_bedrooms ) ) {
				$number_of_bedrooms = 0;
			}

			$number_of_bathrooms = $report->filter('span#lblBathrooms')->text();
			if ( empty( $number_of_bathrooms ) ) {
				$half_bathrooms = 0;
				$full_bathrooms = 0;
			} else {
				$bathrooms_array = array_map( 'trim', explode( '-', $number_of_bathrooms ) );
				$full_bathrooms = $bathrooms_array[0];
				if ( isset( $bathrooms_array[1] ) ) {
					$half_bathrooms = $bathrooms_array[1];
				} else {
					$half_bathrooms = 0;
				}
			}
		} else {
			$year_built = 0;
			$number_of_bedrooms = 0;
			$number_of_bathrooms = 0;
			$full_bathrooms = 0;
			$half_bathrooms = 0;
		}

		return [
			'year_built' => $year_built,
			'bedrooms' => $number_of_bedrooms,
			'full_baths' => $full_bathrooms,
			'half_baths' => $half_bathrooms,
		];
	}
}
