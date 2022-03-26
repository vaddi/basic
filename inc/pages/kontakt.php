<?php
if( CONTACTNAME != "" ) $name = explode( ' ', CONTACTNAME );
  else $name = array();

$street = explode( ' ', CONTACTSTREET );
$contact = array(
  'name'        =>  isset( $name[0] ) ? $name[0] : '',
  'surename'    =>  isset( $name[1] ) ? $name[1] : '',
  'street'      =>  isset( $street[0] ) ? $street[0] : '',
  'housenumber' =>  isset( $street[1] ) ? $street[1] : '',
  'plz'         =>  CONTACTPOSTCODE,
  'city'        =>  CONTACTCITY,
  'state'       =>  CONTACTCOUNTRY,
  'land'        =>  CONTACTSTATE,
  'phone'       =>  CONTACTPHONE,
  'fax'         =>  CONTACTFAX,
  'mail'        =>  CONTACTMAIL
);
?>
<h1>Kontakt</h1>

<?php
// google maps
if( isset( $contact['street'] ) && isset( $contact['housenumber'] ) ) {
  echo '<h2>Karte</h2>';
  echo '<div class="gmap">';
  require_once( 'inc/class/extensions/Base.php' );
  echo Base::gmaps( $contact );
  echo '</div>';
}

echo '<div>';
echo '<h2>Kontaktformular</h2>';
include('inc/form/formular.php'); 
echo '</div>';

echo '<div>';
echo '<h2>Anschrift</h2>';
// Name
echo isset( $contact['name'] ) && $contact['name'] != "" ? $contact['name'] . ' ' : '';
echo isset( $contact['surename'] ) && $contact['surename'] != "" ? $contact['surename'] : '';
echo ( isset( $contact['name'] ) && $contact['name'] != "" ) || ( isset( $contact['surename'] ) && $contact['surename'] != "" ) ? "<br />\n" : '';
// Street
echo isset( $contact['street'] ) && $contact['street'] != "" ? $contact['street'] . ' ' : '';
echo isset( $contact['housenumber'] ) && $contact['housenumber'] != "" ? $contact['housenumber'] : '';
echo ( isset( $contact['street'] ) && $contact['street'] != "" ) || ( isset( $contact['housenumber'] ) && $contact['housenumber'] != "" ) ? "<br />\n" : '';
// City
echo isset( $contact['plz'] ) && $contact['plz'] != "" ? $contact['plz'] . ' ' : '';
echo isset( $contact['city'] ) && $contact['city'] != "" ? $contact['city'] : '';
echo ( isset( $contact['plz'] ) && $contact['plz'] != "" ) || ( isset( $contact['city'] ) && $contact['city'] != "" ) ? "<br />\n" : '';
// State
echo isset( $contact['state'] ) && $contact['state'] != "" ? $contact['state'] . "<br />\n" : '';
// Land
echo isset( $contact['land'] ) && $contact['land'] != "" ? $contact['land'] . "<br />\n" : '';

echo "<br />\n";
// Phnoe, Fax and Mail
echo isset( $contact['phone'] ) && $contact['phone'] != "" ? '<a href="tel:' . $contact['phone'] . '">' . $contact['phone'] . "</a><br />\n" : '';
echo isset( $contact['fax'] ) && $contact['fax'] != "" ? '<a href="fax:' . $contact['fax'] . '">' . $contact['fax'] . "</a><br />\n" : '';
echo isset( $contact['mail'] ) && $contact['mail'] != "" ? '<a href="mailto:' . $contact['mail'] . '">' . $contact['mail'] . "</a><br />\n" : '';
echo '</div>';


?>