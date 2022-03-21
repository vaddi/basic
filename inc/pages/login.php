<?php
  
if( isset( $_REQUEST ) ) { $request = $_REQUEST; } else { $request = null; }

$erg = array();

// Helper functions

function germanDay( $day = null, $format = null ) {
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

function germanMonth( $month = null, $format = null ) {
	if( $month === null ) $month = date( 'm' );
	if( $format === null ) $format = 'm';
	$month = date( "F", mktime( 0, 0, 0, $month, 10 ) );
	if( $format === 's' ) return substr( $month, 0, 2 );
	if( $format === 'm' ) return substr( $month, 0, 3 );
	if( $format === 'l' ) return $month;
}

function expireDate() {
	return  germanDay() . ", " . date( 'd' ) . " " . germanMonth() . " " . date( 'Y' ) . " " . ( ( date( 'H' ) +1 ) < 10 ? '0' . ( date( 'H' ) +1 ) : ( date( 'H' ) +1 ) ) . ":00:00 GMT";
}

// Parsing request data to erg array
if( isset( $request['submit'] ) && $request['submit'] == 'submit' ) {

	if( is_array( $request ) ) {
		foreach ( $request as $id => $tupil ) {
			if( is_array( $tupil ) ) {
				foreach ( $tupil as $key => $value ) {
					$erg[ $key ] = $value;
				}
			} else if( is_numeric( $tupil ) || is_bool( $tupil ) || is_string( $tupil ) ) {	
				$erg[ $id ] = $tupil;
			} 
		}
	} 
	
  // do something with $erg
  if( ! empty( $erg['submit'] ) ) {
    $user = isset( $erg['user'] ) ? $erg['user'] : null;
    $password = isset( $erg['password'] ) ? $erg['password'] : null;

    // validate user
    $result = array();
    if( $user === null ) {
      $result = array(
        'code'  =>  401,
        'message' => 'Empty Username given.'
      );
    } else if( $user ) {
      // create session etc
      if( $password === null ) {
        $result = array(
          'code'  =>  401,
          'message' => 'Empty Password given.'
        );
      } else if( $password ) {
        // do the Login
        $login = Base::login( $user, $password );
        if( $login ) {
          // set cookiedata
          $shavar = sha1( $user . $password, CLIENTTOKEN );
          setcookie( 'cid', CLIENTTOKEN, time() + CLIFETIME );  /* expire in 1 hour */
          setcookie( 'created', time(), time() + CLIFETIME );
          // if( ! isset( $_COOKIE['commitcookie'] ) ) {
          //   setcookie( 'commitcookie', "true", time() + CLIFETIME );
          // }
          // redirect on sucssefull login
          if( isset( $erg['redirect'] )) header( 'Location: ' . $erg['redirect'] );
          $result = array(
            'code' => 0,
            'message' => 'Logged in successfully',
          );
        } else {
          $result = array(
            'code' => 403,
            'message' => 'Failed to Login',
          );
        }
      }
    }

  }

  // error output
  if( isset( $result['code'] ) && $result['code'] != 0 ) {
    echo '<div style="margin-top:20px;">' . $result['code'] . ' ';
    echo '<span>' . $result['message'] . '</span>';
    echo '</div>';
  }
  
  // echo "<pre>";
  // //var_dump( json_encode( $erg ) );
  // print_r( $result );
  // echo "</pre>";
  
}

// simple logged in check, user has valid session
if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
//if( isset( $_COOKIE['cid'] ) && base64_decode( sha1( USER . USERPASS, str_replace( "%3D",'', $_COOKIE['cid'] ) ) ) === SERVERTOKEN ) {
  // do the logout
  if( isset( $request['logout'] ) && $request['logout'] == 'true'  ) {
    setcookie( 'cid', '', 1 );
    if( isset( $_SERVER['HTTP_REFERER'] )) header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
  }
  echo '<div style="margin-top:20px;">You\'re logged in, welcome.</div>';
}

// echo "<pre>";
// print_r( SERVERTOKEN );
// echo "\n";
// print_r( CLIENTTOKEN );
// echo "\n";
// echo "\n";
// print_r( urldecode( base64_decode( substr( CLIENTTOKEN, 0, -6 ) ) ) ); // === SERVERTOKEN
// echo "\n";
// print_r( substr( urldecode( base64_decode( CLIENTTOKEN ) ), 0, -3 )  ); // === SERVERTOKEN
// echo "\n";
// print_r( base64_decode( str_replace( "%3D",'', CLIENTTOKEN ) ) );
// echo "\n";
// echo "\n";
// //print_r(  base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN  ); // logged in
// print_r( base64_decode( sha1( USER . USERPASS, str_replace( "%3D",'', $_COOKIE['cid'] ) ) ) === SERVERTOKEN ); // logged in
// echo "</pre>";

?>

<div style="margin-top:40px;width:100%;text-align:center;">

  <form id="formtest" action="?page=login" method="POST" datatype="json">
    <fieldset>
			<legend>Login</legend>
			<label for="forename">
				Username/E-Mail:<br />
				<input id="user" type="text" name="user" placeholder="You username" autofocus />
			</label><br />
			
			<label for="password">
				Password:<br />
				<input id="password" type="password" name="password" placeholder="12345678" />
			</label><br />
    </fieldset>
    <input type="hidden" name="submit" value="submit" />
    <input type="hidden" name="redirect" value="<?= isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '?page=login'; ?>" />
    <button type="submit" style="margin:15px 0 0;">Absenden</button>
  </form>

</div>
