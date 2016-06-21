<?php 
/* securefile.php - module to serve secure files directly from the database
   
   Does not output anything unless there is an error
*/
session_start();
require 'config.php';
require 'library.php';

$con = connect_gazebo_DB("mysqli");

$index = $_GET['Idx'];

if ( !isset($index) ) {
    die("No file index provided.");
}

$querystring = "SELECT * FROM SecureFileMeta WHERE Idx={$index}";
$result = mysqli_query($con, $querystring);
$row = mysqli_fetch_array($result);

if ( !$row ) {
    die("Invalid file ID {$index} specified.");
}

$resourcetype = 'download';
$resourcelevel = $row['Minlevel'];

$silentlogin = true;
require 'authcheck.php';

$mime = $row['MIME'];
$size = $row['Size'];
$behavior = $row['Behavior'];
$filename = rawurlencode($row['Filename']);

header("Content-Type: " . $row['MIME']);

if (( $behavior != 'download' ) && ( $behavior != 'display' )) {
    $behavior = fetchSetting( 'SecureFileDefaultBehavior', $con );
}

switch ( $behavior ) {
    case 'download':
        header("Content-Disposition: download; filename={$filename}");
	break;
    case 'display':
	header("Content-Disposition: inline; filename={$filename}");
	break;
    default:
}

$querystring = "SELECT Data FROM SecureFileData WHERE Idx={$index}";
$result = mysqli_query($con, $querystring);
$row = mysqli_fetch_array($result);
header('Content-Length: ' . strlen($row['Data']));
echo $row['Data'];
?>
