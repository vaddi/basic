<?php
  
if( isset( $_REQUEST ) ) { $request = $_REQUEST; } else { $request = null; }

$erg = array();

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

    // validate user & password from config.php file
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
        // do the Login in Base.php
        $login = Base::login( $user, $password );
        if( $login ) {
          // set cookiedata
					$expire = time() + CLIFETIME;
          $shavar = sha1( $user . $password, CLIENTTOKEN );
          setcookie( 'cid', CLIENTTOKEN, $expire );  /* expire in 1 hour */
          setcookie( 'created', time(), $expire );
          setcookie( 'username', USER, $expire );
					setcookie( 'lifetime', CLIFETIME, $expire );
          if( ! isset( $_COOKIE['commitcookie'] ) ) {
            setcookie( 'commitcookie', "true", $expire );
          }
          $result = array(
            'code' => 0,
            'message' => 'Logged in successfully',
          );
          // redirect on sucssefull login
          if( isset( $erg['redirect'] ) && $erg['redirect'] != null && $erg['redirect'] != "" ) {
            header( 'Location: ' . $erg['redirect'], false );
						exit;
          }
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
  
}

// simple logged in check, user has valid session
if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
//if( isset( $_COOKIE['cid'] ) && base64_decode( sha1( USER . USERPASS, str_replace( "%3D",'', $_COOKIE['cid'] ) ) ) === SERVERTOKEN ) {
	//
  // logout user
	//
  if( isset( $request['logout'] ) && $request['logout'] == 'true'  ) {
    setcookie( 'cid', '', 1 );
    setcookie( 'created', '', 1 );
		setcookie( 'username', '', 1 );
		setcookie( 'lifetime', '', 1 );
    if( isset( $_SERVER['HTTP_REFERER'] )) {
    	header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
			exit;
    }
  }
  echo '<div style="margin-top:20px;">You\'re logged in, welcome.</div>';
}

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
