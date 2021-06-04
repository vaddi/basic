<h1>Database</h1>

<?php

$type   = DB; // SQLite or MySQL are supported
$table  = 'users';

$db = new Database( $type );

// Helper function print bool
function boolify( $value ) {
	return (int) $value === 1 ? "✔" : "✘";
}

// helper function show result as html table
function tablify( $data ) {
  $count = 0;
	echo "<table border='1'>";
	echo "<thead>";
	echo "<tr>";
	foreach( $data as $row ) {
		$total = is_object( $row) ? count( get_object_vars( $row ) ) : count($row);
		foreach ( $row as $key => $entry ) {
			if( $count++ >= $total ) break;
			echo "<th>" . $key . "</th>";
		}
	}
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	foreach( $data as $row ) {
		echo "<tr>";
		foreach ( $row as $key => $entry ) {
			echo "<td>";
			echo $entry;
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "<tbody>";
	echo "</table>";
}

$result = null;

?>
<h3>Information</h3>
<?php
  echo "Database Type: " . DB . "<br />\n";
  echo "Connected: " . boolify( $db->connected ) . "<br />\n";
  echo 'Connection: ' . $db->connection() . "<br />\n";
?>

<h3>Drop Table</h3>
<?php
  $query = "DROP TABLE IF EXISTS $table";
  $db->query( $query );
  $result = $db->execute();
  echo "Drop Table $table: " . boolify( $result );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong><br />" );
?>

<h3>Create Table</h3>
<?php
  $result = 0; // reset our result variable
  if( $type === 'SQLite' ) {
  	$query = "CREATE TABLE $table ( 'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 'name' TEXT NULL, 'email' TEXT NULL )";
  } else if( $type === 'MySQL' ){
  	$query = "CREATE TABLE `$table` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` TEXT NULL, `email` TEXT NULL )";
  }
  $db->query( $query );
  $result = $db->execute();
  echo "Create Table $table: " . boolify( $result );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong><br />" );
?>

<h3>Table Exists</h3>
<?php
  $query = "SELECT count(*) as sum FROM sqlite_master WHERE type='table' AND name='" . $table . "';";
  $db->query( $query );
  $db->execute();
  $result = $db->resultset();
  echo "Exsist Table $table: " . boolify( $result );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong><br />" );
  if( $result[0]['sum'] == "0" ) {
    echo "Database Table $table does not exists!";
  } else {
    echo "Found Table $table.";
  }
?>

<h3>Single Insert</h3>
<?php
  echo "Insert Single: ";
  $query = "INSERT INTO $table ( name ) VALUES ( 'Maik' )";
  $db->query( $query );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  echo "<br />";
  echo "LastInsertId: ";
  print_r( $db->lastInsertId( $table ) );
  echo "<br />";
?>

<h3>Multiple Inserts</h3>
<?php
  $result=0;
  echo "Insert 3 Entries: ";
  $query = "INSERT INTO $table ( name ) VALUES ( :name )";
  $db->query( $query );
  $db->bind( ':name', "Hans" );
  $db->execute();
  $db->bind( ':name', "Klaus" );
  $db->execute();
  $db->bind( ':name', "Gabi" );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  echo "<br />";
  print_r( 'LastInsertId: ' . $db->lastInsertId( $table ) . "<br />" );
?>

<h3>Select</h3>
<?php
  $result=0;
  echo "Select: ";
  $query = "SELECT * FROM $table";
  $db->query( $query );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  print_r( '<br />--- <br />Query Result: <br />' );
  echo tablify( $db->resultObj() );
  //print_r( $db->resultset() );
  print_r( 'RowCount: ' . $db->rowCount( $table ) . "<br />" );
?>

<h3>Delete</h3>
<?php
  $result=0;
  echo "Delete 3 Entries: ";
  $query = "DELETE FROM $table WHERE name = ? OR name = ? OR name = ?";
  $db->query( $query );
  $db->bind( 1, "Hans" );
  $db->bind( 2, "Klaus" );
  $db->bind( 3, "Gabi" );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  echo "<br />";
  print_r( 'RowCount: ' . $db->rowCount( $table ) . "<br />" );  
?>

<h3>Select</h3>
<?php
  $result=0;
  echo "Select: ";
  $query = "SELECT * FROM $table";
  $db->query( $query );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  print_r( '<br />--- <br />Query Result: <br />' );
  echo tablify( $db->resultObj() );
  //print_r( $db->resultset() );
  print_r( 'RowCount: ' . $db->rowCount( $table ) . "<br />" );
?>

<h3>Update</h3>
<?php
  $userid = 1;
  echo "Update: ";
  $query = "UPDATE $table SET name = :name  WHERE id = :id";
  $db->query( $query );
  $db->bind( ':name', "Greta" );
  $db->bind( ':id', $userid );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  echo "<br />User id $userid updated";
?>

<h3>Select</h3>
<?php
  $result=0;
  echo "Select: ";
  $query = "SELECT * FROM $table";
  $db->query( $query );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  print_r( '<br />--- <br />Query Result: <br />' );
  echo tablify( $db->resultObj() );
  //print_r( $db->resultset() );
  print_r( 'RowCount: ' . $db->rowCount( $table ) . "<br />" );
?>

<h3>Insert Single</h3>
<?php
  $result=0;
  echo "Insert Single: ";
  $query = "INSERT INTO $table ( name ) VALUES ( 'Tim' )";
  $db->query( $query );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  echo "<br />";
  print_r( 'LastInsertId: ' . $db->lastInsertId( $table ) . "<br />" );
?>

<h3>Select</h3>
<?php
  $result=0;
  echo "Select: ";
  $query = "SELECT * FROM $table";
  $db->query( $query );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  print_r( '<br />--- <br />Query Result: <br />' );
  echo tablify( $db->resultObj() );
  //print_r( $db->resultset() );
  print_r( 'RowCount: ' . $db->rowCount( $table ) . "<br />" );
?>

<h3>Transaction</h3>
<?php
  print_r( "Begin Transaction: " . boolify( $db->beginTransaction() ) . "<br />" );
  print_r( "Insert Single: " );
  $query = "INSERT INTO $table ( name ) VALUES ( 'Rainer' )";
  $db->query( $query );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  echo "<br />";
  print_r( 'LastInsertId: ' . $db->lastInsertId( $table ) . "<br />" );
  if( $result ) {
  	print_r( "End Transaction: " . boolify( $db->endTransaction() ) . "<br />" );
  } else {
  	print_r( "Cancel Transaction: " . boolify( $db->cancelTransaction() ) . "<br />" );
  }
?>

<h3>Select</h3>
<?php
  $result=0;
  echo "Select: ";
  $query = "SELECT * FROM $table";
  $db->query( $query );
  $result = (int) $db->execute();
  print_r( boolify( $result ) );
  if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
  print_r( '<br />--- <br />Query Result: <br />' );
  echo tablify( $db->resultObj() );
  //print_r( $db->resultset() );
  print_r( 'RowCount: ' . $db->rowCount( $table ) . "<br />" );
?>


<h3>Debuging</h3>
<?php
echo "Debug Params: ";
$db->debugDumpParams();
echo "<br />";
print_r( "Query String: " . $db->queryString() . "<br />" );
print_r( "Error Info: " );
print_r( $db->errorInfo() );
print_r( "<br /><br />" );

$db->close();

echo "Connected: " . boolify( $db->connected ) . "<br /><br />";
print_r( 'Connection: ' . (int) $db->connection() . "<br />" );

?>

