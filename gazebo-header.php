<?php

/* Common header file for all modules */

/* Standalone - display DTD */
if ( ! isset($cms) ) {
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
    'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
}

/* Include configuration and function library */
require_once 'config.php'; 
require_once 'library.php';

/* Page name and title */
if ( ! isset($pagename) ) {
	$pagename = "Gazebo";
}
$pagetitle = pageTitle($pagename);
$editlevel = pageSecurity($pagename, true);

/* Standalone - display HTML head and standalone-only includes */
if ( ! isset($cms) ) { 
	session_start();
	echo "<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
			<title>{$pagetitle}</title>
			<meta name='author' content='PiXii Computing' />
			<script src='sorttable.js'></script>";
}

//Manually update SESSION so that new color schemes are shown immediately
if ( isset($_POST['updateColor']) && ($_POST['updateColor'] == 'yes') ) {
    $_SESSION['ColorScheme'] = $_POST['colorSelect'];
}
if ( isset($_POST['update24HrTime']) && ($_POST['update24HrTime'] == 'yes') ) {
    $_SESSION['24HrTime'] = $_POST['24HrTime'];
}
if ( isset($_POST['updateDebugMode']) && ($_POST['updateDebugMode'] == 'yes') ) {
    $_SESSION['DebugMode'] = $_POST['debugMode'];
}

/* Include dynamic stylesheet */
include 'style-gazebo.php'; 

/* jQuery include */
if ( ! isset($cms) ) {
    echo "<script src='jquery-2.1.1.min.js'></script>";
}
/* Gazebo javascript header */
if ( ! isset($customjs) ) {
	echo "<script src='{$maindir}header.js'></script>";
}
/* Set page title from config */
echo "<script>document.title = '{$pagetitle}';</script>";

/* Standalone - display HTML closing tags */
if ( ! isset($cms) ) {
	echo "</head><body>";
}
else { /* CMS - display div so that entire page responds to color schemes */
	echo "<div class='module'>";
}

/* Connect MySQL DB */
$con = connect_gazebo_DB("mysqli");

/* Supress undefined errors */
error_reporting( error_reporting() & ~E_NOTICE );
?>
