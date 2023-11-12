<?php
if( CONTACTNAME != "" ) $name = explode( ' ', CONTACTNAME );
  else $name = array();

// separate Housnumber and Street
preg_match_all('!\d+!', CONTACTSTREET, $hnumber); // find number in Adressstreet
$housenumber = isset( $hnumber[0][0] ) ? $hnumber[0][0] : '';
$street = str_replace( $housenumber, '', CONTACTSTREET );

// prepare array
$contact = array(
  'name'        =>  isset( $name[0] ) ? $name[0] : '',
  'surename'    =>  isset( $name[1] ) ? $name[1] : '',
  'street'      =>  isset( $street ) ? $street : '',
  'housenumber' =>  isset( $housenumber ) ? $housenumber : '',
  'plz'         =>  CONTACTPOSTCODE,
  'city'        =>  CONTACTCITY,
  'state'       =>  CONTACTCOUNTRY,
  'land'        =>  CONTACTSTATE,
  'phone'       =>  CONTACTPHONE,
  'fax'         =>  CONTACTFAX,
  'mail'        =>  CONTACTMAIL,
	'authorpage'	=>	SOCIALPAGE,
	'twitter'			=>  SOCIALTWITTER,
	'facebook'		=>  SOCIALFACEBOOK,
	'instagram'		=>  SOCIALINSTAGRAM,
	'youtube'			=>  SOCIALYOUTUBE
);
?>
<h1>Contact</h1>

<?php
// google maps
if( CONTACTMAP ) {
	if( isset( $contact['street'] ) && isset( $contact['housenumber'] ) ) {
	  echo '<h2>Map</h2>' . "\n";
	  echo '<div class="gmap">' . "\n";
	  Gmap::gmaps( $contact );
	  echo '</div>' . "\n";
	}
}

// Contact form
if( CONTACTFORM ) {
	echo '<div>' . "\n";
	echo '<h2>Contact form</h2>' . "\n";
	Email::formular();
	echo '</div>' . "\n";
	
}

if( CONTACTADRESS ) {
	echo '<div>' . "\n";
	echo '<h2>Address</h2>' . "\n";
	// Name
	echo isset( $contact['name'] ) && $contact['name'] != "" ? $contact['name'] . ' ' : ''  . "\n";
	echo isset( $contact['surename'] ) && $contact['surename'] != "" ? $contact['surename'] : '' . "\n";
	echo ( isset( $contact['name'] ) && $contact['name'] != "" ) || ( isset( $contact['surename'] ) && $contact['surename'] != "" ) ? "<br />\n" : '' . "\n";
	// Street
	echo isset( $contact['street'] ) && $contact['street'] != "" ? $contact['street'] . ' ' : '' . "\n";
	echo isset( $contact['housenumber'] ) && $contact['housenumber'] != "" ? $contact['housenumber'] : '' . "\n";
	echo ( isset( $contact['street'] ) && $contact['street'] != "" ) || ( isset( $contact['housenumber'] ) && $contact['housenumber'] != "" ) ? "<br />\n" : '' . "\n";
	// City
	echo isset( $contact['plz'] ) && $contact['plz'] != "" ? $contact['plz'] . ' ' : '' . "\n";
	echo isset( $contact['city'] ) && $contact['city'] != "" ? $contact['city'] : '' . "\n";
	echo ( isset( $contact['plz'] ) && $contact['plz'] != "" ) || ( isset( $contact['city'] ) && $contact['city'] != "" ) ? "<br />\n" : '' . "\n";
	// State
	echo isset( $contact['state'] ) && $contact['state'] != "" ? $contact['state'] . "<br />\n" : '' . "\n";
	// Land
	echo isset( $contact['land'] ) && $contact['land'] != "" ? $contact['land'] . "<br />\n" : '' . "\n";

	echo "<br />\n";
	// Phnoe, Fax and Mail
	echo isset( $contact['phone'] ) && $contact['phone'] != "" ? 'Phone: <a href="tel:' . preg_replace('/[^a-zA-Z0-9-+_\.]/','', $contact['phone'] ) . '">' . $contact['phone'] . "</a><br />\n" : '' . "\n";
	echo isset( $contact['fax'] ) && $contact['fax'] != "" ? 'Fax: <a href="fax:' . $contact['fax'] . '">' . $contact['fax'] . "</a><br />\n" : '' . "\n";
	echo isset( $contact['mail'] ) && $contact['mail'] != "" ? 'Mail: <a href="mailto:' . $contact['mail'] . '">' . str_replace( '@', '[at]', $contact['mail'] ) . "</a><br />\n" : '' . "\n";
	echo '</div>' . "\n";
}

if( SOCIALLINKS ) {
	echo '<div>' . "\n";
	echo '<h2>Links</h2>' . "\n";
	echo isset( $contact['authorpage'] ) && $contact['authorpage'] != "" ? 'My Website: <a href="' . $contact['authorpage'] . '">' . $contact['authorpage'] . "</a><br />\n" : '' . "\n";
	echo isset( $contact['twitter'] ) && $contact['twitter'] != "https://twitter.com/" && $contact['twitter'] != "" ? 'Twitter: <a href="' . $contact['twitter'] . '">' . $contact['twitter'] . "</a><br />\n" : '' . "\n";
	echo isset( $contact['facebook'] ) && $contact['facebook'] != "https://facebook.com/" && $contact['facebook'] != "" ? 'Facebook: <a href="' . $contact['facebook'] . '">' . $contact['facebook'] . "</a><br />\n" : '' . "\n";
	echo isset( $contact['instagram'] ) && $contact['instagram'] != "https://www.instagram.com/" && $contact['instagram'] != "" ? 'Instagram: <a href="' . $contact['instagram'] . '">' . $contact['instagram'] . "</a><br />\n" : '' . "\n";
	echo isset( $contact['youtube'] ) && $contact['youtube'] != "https://www.youtube.com/" && $contact['youtube'] != "" ? 'YouTube: <a href="' . $contact['youtube'] . '">' . $contact['youtube'] . "</a><br />\n" : '' . "\n";
	echo '</div>' . "\n";
}

?>