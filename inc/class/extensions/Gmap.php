<?php

// Class <strong>Gmap</strong> Generates a simple google map frame

class Gmap {

	/**
	 * Returns a google maps url from a assoc Address array
	 */
	public static function gmaps( $address = null ) { 
		if( $address === null ) return $address;
		$street				=	isset( $address['street'] ) ? $address['street'] : '';
		$housenumber	= isset( $address['housenumber'] ) ? ' ' . $address['housenumber'] : '';
		$city					= isset( $address['city'] ) ? ' ' . $address['city'] : '';
		$plz					=	isset( $address['plz'] ) ? ' ' . $address['plz'] : '';
		// and build our gmaps url
    $zoom = "15"; // zoom level
    $url  = 'https://maps.google.com/maps?f=q&source=s_q&hl=' . CLIENTLANG . '&geocode=&q=';
    $url .= self::mapify( $street . ' ' . $housenumber . ', ' . $plz . ' ' . $city );
    $url .= '&z=' . $zoom . '&output=embed';
		return '<iframe id="gmaps" src="' . $url . '" width="100%" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>';
	}

	/**
	 * Helper to build gmaps url (replace spaces)
	 */
	private static function mapify( $string = null, $delimiter = null ) { 
		if( $string === null ) return;
		if( $delimiter === null ) $delimiter = '+';
		$string = str_replace( ' ', $delimiter, $string );
    return str_replace( ',', '', $string );
	}

}

?>
