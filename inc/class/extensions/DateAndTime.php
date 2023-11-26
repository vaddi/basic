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
	 * A Simple Expiredate
	 * @return	String	an expire Date
	 */
	public static function expireDate() {
		return  self::germanDay() . ", " . date( 'd' ) . " " . self::germanMonth() . " " . date( 'Y' ) . " " . ( ( date( 'H' ) +1 ) < 10 ? '0' . ( date( 'H' ) +1 ) : ( date( 'H' ) +1 ) ) . ":00:00 GMT";
	}

	/**
	 * Converts a unixtimestamp to a date
	 * @param unix time stamp
	 * @return a Date from a Timestamp
	 */
	public static function time2date( $time ) {
		// is valid timestamp
		if( ! self::isTimestamp( $time ) ) return false;
		// our date data
		$weekday = date( 'w', $time ); // Weekday
		$day =  date( 'd', $time ); // Day
		$month = date( 'm', $time );  // Month
		$year = date( 'Y', $time ); // Year
		$hour = date( 'H', $time ); // hour
		$minute = date( 'i', $time ); // minutes
		$seconds = date( 's', $time ); // seconds
		$timezone = date( 'T', $time ); // timezone

		// Formating the Output
		$date = '';
		$date .= self::germanDay( $weekday );
		$date .= ', ';
		$date .= $day;
		$date .= ' ';
		$date .= self::germanMonth( $month );
		$date .= ' ';
		$date .= $year;
		$date .= ' ';
		$date .= $hour;
		$date .= ':';
		$date .= $minute;
		$date .= ':';
		$date .= $seconds;
		$date .= ' ';
		$date .= $timezone;
		return $date;
	}
	
	/**
	 * COnverts a date to a unix time stamp
	 * @param date
	 * @return a Date from a Timestamp
	 */
	public static function date2time( $date ) {
		
		return date( 'U', $date );
	}
	
	/**
	 * @param string $string
	 * @return bool
	 */
	public static function isTimestamp( $string )
	{
	    try {
	        new DateTime( '@' . $string );
	    } catch( Exception $e ) {
	        return false;
	    }
	    return true;
	}

}

?>
