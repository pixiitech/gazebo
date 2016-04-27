<html>
<head>
<?php 
require 'wp-content/plugins/config.php';
require 'wp-content/plugins/library.php';
$con = connect_gazebo_DB();
?>
<title>TEST</title>
</head>
<body>
<h4>TEST PAGE</h4>
<?php
echo "DBNAME: {$sql_db}";

$querystring = "SELECT Data FROM SecureFileData WHERE Idx=12";
$result = mysql_query($querystring, $con);
if ( $result ) {
	echo "File found. Display:<br />";
}
$row = mysql_fetch_array($result);
header('Content-type: ' . $_FILES['Pic']['type']);
header('Content-length: ' . $_FILES['Pic']['size']);

echo $row['Data'];
?>
<h4>END TEST</h4>
</body></html>
