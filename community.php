<?php 
$pagename = "community";
require 'gazebo-header.php';
?>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Community</h2>

<?php

require 'authcheck.php';

echo "<br />
<p><h3 style='text-align:center'><a href='" . pageLink("comminfo") . "'>Community Info</a></h3></p>
<p><h3 style='text-align:center'><a href='" . pageLink("calendar") . "'>Event Calendar</a></h3></p>
<p><h3 style='text-align:center'><a href='" . pageLink("announce") . "'>Manage Announcements</a></h3></p>";
if ( fetchSetting("Type", $con) == "HOA" ) {
    echo "<p><h3 style='text-align:center'><a href='" . pageLink("subdivmgr") . "'>Subdivision Management</a></h3></p>";
}

echo "<p><h3 style='text-align:center'><a href='" . pageLink("amenitymgr") . "'>Amenity Management</a></h3></p>";
if ( ! isset( $cms ) ) {
	echo "<p><h3 style='text-align:center'><a href='" . pageLink("webeditor") . "'>Website Editor</a></h3></p>";
}

include 'gazebo-footer.php'; 
?>
