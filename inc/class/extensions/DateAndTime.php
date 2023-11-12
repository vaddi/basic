<?php

// Class <strong>DateAndTime</strong> Helper functions to parse Date and Time

class DateAndTime {

	/**
	 * Get a Day as numeric Value and returns the Day as german String
	 * format s = Short, two Character
	 * format m = Medium, three Character
	 * format l = Long, fully advertised
	 * @param		$day		Integer
	 * @param		$format	String (s,m,l)
	 * @return	String
	 */
	public static function germanDay( $day = null, $format = null ) {
		if( $day === null ) $day = date( "w" );
		if( $day < 0 || $day > 6 ) return;
		if( $format === null ) $format = 's'; // s = Short, l = Long, m = Middle
	
		switch( $day ) {
			case 'Su' || 'Sun' || '0':
				$short = 'So';
				$midd = 'Son';
				$long = 'Sonntag';
			break;
			case 'Mo' || 'Mon' || '1':
				$short = 'Mo';
				$midd = 'Mon';
				$long = 'Montag';
			break;
			case 'Tu' || 'Tue' || '2':
				$short = 'Di';
				$midd = 'Die';
				$long = 'Dienstag';
			break;
			case 'We' || 'Wed' || '3':
				$short = 'Mi';
				$midd = 'Mit';
				$long = 'Mittwoch';
			break;
			case 'Th' || 'Thu' || '4':
				$short = 'Do';
				$midd = 'Don';
				$long = 'Donnerstag';
			break;
			case 'Fr' || 'Fri' || '5':
				$short = 'Fr';
				$midd = 'Fre';
				$long = 'Freitag';
			break;
			case 'Sa' || 'Sat' || '6':
				$short = 'Sa';
				$midd = 'Sam';
				$long = 'Samstag';
			break;
			default :
				$short = 'So';
				$midd = 'Son';
				$long = 'Sonntag';
			break;		
		}
		if( $format === 's' ) return $short;
		if( $format === 'm' ) return $midd;
		if( $format === 'l' ) return $long;
	}

	/**
	 * Get a month as Numeric Value and returns the Month as german String
	 * format s = Short, two Character
	 * format m = Medium, three Character
	 * format l = Long, fully advertised
	 * @param		$month	Integer
	 * @param		$format	String (s,m,l)
	 * @return	String
	 */
	public static function germanMonth( $month = null, $format = null ) {
		if( $month === null ) $month = date( 'm' );
		if( $format === null ) $format = 'm';
		$month = date( "F", mktime( 0, 0, 0, $month, 10 ) );
		if( $format === 's' ) return substr( $month, 0, 2 );
		if( $format === 'm' ) return substr( $month, 0, 3 );
		if( $format === 'l' ) return $month;
	}

	/**
	 * Returns an expire Date 
	 * Format: 
	 * @return	String
	 */
	public static function expireDate() {
		return  self::germanDay() . ", " . date( 'd' ) . " " . self::germanMonth() . " " . date( 'Y' ) . " " . ( ( date( 'H' ) +1 ) < 10 ? '0' . ( date( 'H' ) +1 ) : ( date( 'H' ) +1 ) ) . ":00:00 GMT";
	}

}

?>
