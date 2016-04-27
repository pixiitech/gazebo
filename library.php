<?php
/*******Page link and security functions*******/

/* pageLink - Page link generator
   Returns a URL link to a specified Gazebo module in the form of: 
			    <WordPress> http://www.sitename.com/?page_id=<container page>&page=<pagename>&<args>
			    <standalone> http://www.sitename.com/<pagename>.php?<args>
   Accepts three arguments: $pagename - Pagename set by $pagename in the module that a link is being requested for
			    $args - Any GET arguments to be added into the URL
			    $container - Optional. The Wordpress page ID of the container page being referenced.
*/

function pageLink($pagename, $args = "", $container = NULL)
{
    global $modules, $cms, $wp_container_pagename;
    global $wp_binary_container, $wp_popup_container;

    for ( $i = 0; $i < count($modules); $i++ )
        if ( $modules[$i][0] == $pagename )
	{
            if ( $cms == "wp" ) {
		if ( $container != NULL ) {
		    $thelink = get_page_link(get_page_by_title($container)->ID);
		}
		else if ( $pagename == 'securefile' ) {
		    $thelink = get_page_link(get_page_by_title($wp_binary_container)->ID);
		}
		else if ( $pagename == 'help' ) {
		    $thelink = get_page_link(get_page_by_title($wp_popup_container)->ID);
		}
		else if ( $pagename == 'resname' ) {
		    $thelink = get_page_link(get_page_by_title($wp_popup_container)->ID);
		}
		else if ( $pagename == 'eblaster' ) {
		    $thelink = get_page_link(get_page_by_title($wp_popup_container)->ID);
		}
		else {
		    $thelink = get_page_link(get_page_by_title($wp_container_pagename)->ID);
		}
		if ( $pagename == 'securefile' ) {

		}
		else if ( $args != "" ) {
		    $args = "page={$pagename}&" . $args;
		}
		else {
		    $args = "page={$pagename}";
		}
	    }
	    else
	       $thelink = $modules[$i][0] . ".php";

	    if (( $cms == "wp" ) && ( $args != "" ))
	        return $thelink . "&" . $args;
	    else if ( $args != "" )
		return $thelink . "?" . $args;
	    else 
		return $thelink;
	}
    return NULL;
}

/* pageID - Get page ID
   Returns a page ID
 */

function pageID($pagename)
{
/*    global $modules;
    global $cms;
    for ( $i = 0; $i < count($modules); $i++ )
        if ( $modules[$i][0] == $pagename )
	{
            if ( $cms == "wp" )
	       return get_page_by_title($modules[$i][2])->ID;
	    else
	       return NULL;
	}
*/
    global $modules, $cms, $wp_container_pagename;
    global $wp_binary_container, $wp_popup_container;

    for ( $i = 0; $i < count($modules); $i++ )
        if ( $modules[$i][0] == $pagename )
	{
		if ( $pagename == 'securefile' ) {
		    return get_page_by_title($wp_binary_container)->ID;
		}
		else if ( $pagename == 'help' ) {
		    return get_page_by_title($wp_popup_container)->ID;
		}
		else if ( $pagename == 'resname' ) {
		    return get_page_by_title($wp_popup_container)->ID;
		}
		else if ( $pagename == 'eblaster' ) {
		    return get_page_by_title($wp_popup_container)->ID;
		}
		else {
		    return get_page_by_title($wp_container_pagename)->ID;
		}
	}
    return NULL;
}

/* Get page security level */
/* Returns the requested module's security level
/* Arguments: $pagename - Pagename as referenced by $modules
              $edit - If $edit is true, return the security level for editing
			Otherwise, return the security level for viewing */

function pageSecurity($pagename, $edit = false)
{
    global $modules;
    global $cms;
    for ( $i = 0; $i < count($modules); $i++ )
        if ( $modules[$i][0] == $pagename )
	    if ( $edit )
		return $modules[$i][4];
	    else
	        return $modules[$i][3];
    return -1;
}

/* Get page full title */
/* Returns the requested module's page title */
/* Arguments: $pagename - Pagename as referenced by $modules */
function pageTitle($pagename)
{
    global $modules;
    global $cms;
    for ( $i = 0; $i < count($modules); $i++ )
        if ( $modules[$i][0] == $pagename )
	        return $modules[$i][2];
    return -1;
}

/*******Database connectors*******/
/* Functions take one argument, type to allow for mysqli connections
   Return the object reference for either the Gazebo DB or Wordpress DB */

function connect_gazebo_DB($type = "") {
// Connect to SQL Server
    global $sql_server, $sql_user, $sql_pass, $sql_db;
    if ($type == "mysqli") {
        $con_gazebo = mysqli_connect($sql_server, $sql_user, $sql_pass, $sql_db);
        if (!$con_gazebo)
	    die ("Could not connect to SQL Server: " . mysqli_error() . "<br />");
        return $con_gazebo;
    }
    else {
        // Connect to SQL Server 
        $con = mysql_connect($sql_server, $sql_user, $sql_pass, true);
        if (!$con)
	    die ("Could not connect to SQL Server: " . mysql_error() . "<br />");
        $db_selected = mysql_select_db($sql_db, $con);
        if (!$db_selected)
	    die ("Could not find database.<br />");
        return $con;
    }
}

function connect_WP_DB($type = "") {
// WP DB connector
    global $sql_server, $sql_user, $sql_pass, $wp_db;
    if ($type == "mysqli") {
        $con_wp = mysqli_connect($sql_server, $sql_user, $sql_pass, $wp_db);
        if (!$con_wp)
	    die ("Could not connect to SQL Server: " . mysqli_error() . "<br />");
        return $con_wp;
    }
    else {
        $con = mysql_connect($sql_server, $sql_user, $sql_pass, true);
        if (!$con)
	    die ("Could not connect to SQL Server: " . mysql_error() . "<br />");
        $db_selected = mysql_select_db($wp_db, $con);
        if (!$db_selected)
	    die ("Could not find database. <br />");
        return $con;
    }
}


/******* Retrieve Settings *******/
function fetchSetting($setting, $con)
{
    $result = mysql_query("SELECT Value FROM Settings WHERE Name = '{$setting}'", $con);
    $value = NULL;
    if ($result)
    {
	$row = mysql_fetch_array($result);
	$value = $row['Value'];
    }
    return $value;
}
/******* Resident and Properties General Access Functions *******/
function displayName($first, $last, $ucase, $lastfirst) {
	$first = stripcslashes($first);
	$last = stripcslashes($last);
	if ( $ucase == 'true' ) {
	    $first = strtoupper($first);
	    $last = strtoupper($last);
	}
	if ( $lastfirst == 'true' ) {
	    if ( $last == '' ) {
		return $first;
	    }
	    else if ( $first == '' ) {
		return $last;
	    }
	    return $last . ", " . $first;
	}
	else {
	    return $first . " " . $last;
	}
}

function fetchResname($residx, $con = NULL, $ucase = 'false', $lastfirst = 'false')
{	
	$closeit = false;
	if ( $con == NULL )
	{
	    $con = connect_gazebo_DB();
	    $closeit = true;
	}
	$result = mysql_query("SELECT FirstName, LastName from Residents WHERE Idx = {$residx}", $con);
	if ($result)
	{
	    $row = mysql_fetch_array($result);
	    $name = displayName($row['FirstName'], $row['LastName'], $ucase, $lastfirst);
	}
	else
	    $name = NULL;
	if ( $closeit )
	    mysql_close($con);
	return $name;
}

function fetchResidxFromUnit($unit, $con)
{
	$querystring = "SELECT Residents.Idx FROM Residents, Properties WHERE Properties.Unit = '{$unit}' AND Properties.Residx = Residents.Idx";
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	if (!$row)
		return false;
	else
		return $row['Idx'];
}

function fetchAddress($unit, $con)
{
	$querystring = "SELECT Address FROM Residents WHERE Unit = {$unit}";
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	if (!$row)
		return false;
	else
		return $row['Address'];
}
function fetchResidentEmail($residx, $con, $num = 1)
{
	if ( $num == 1 ) {
	    $field = "Email";
	}
	else {
	    $field = "Email" . $num;
	}
	$querystring = "SELECT {$field} FROM Residents WHERE Idx = {$residx}";
	$result = mysql_query($querystring, $con);
	if (!$result)
	    return false;
	$row = mysql_fetch_array($result);
	if (!$row)
		return false;
	else
		return $row[$field];
}
function fetchSubdivision($subidx, $con)
{	
	$result = mysql_query("SELECT Name from Subdivisions WHERE Id = {$subidx}", $con);
	$row = mysql_fetch_array($result);
	if (!$row)
	    return false;
	else
	    return $row['Name'];
}
function fetchSubidx($subname, $con)
{
	$querystring = "SELECT Id from Subdivisions WHERE Name = '{$subname}'";
	$result = mysql_query($querystring, $con);
	if ($_SESSION['Level'] >= $level_developer)
	    echo $querystring . "<br />";
	$row = mysql_fetch_array($result);
	if (!$row)
	    return false;
	else
	    return $row['Id'];
}
function validateUnit($unit, $con)
{
    $lUnit = strtolower($unit);
    $result = mysql_query("SELECT Unit FROM Properties WHERE LOWER(Unit) = '{$lUnit}'", $con);
    $row = mysql_fetch_array($result);
    if (!$row)
	return false;
    else
	return true;
}
function residentExists($i, $con)
{
	$querystring = "SELECT Idx FROM Residents WHERE Idx = {$i}";
	if ($_SESSION["Level"] == $level_developer)
	    echo $querystring . "<br />";
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	if (!$row)
	    return false;
	else
	    return true;
}

function fetchUnit($residx, $con, $type = NULL)
{
	switch ($type) {
	    case 'owner':
	        $querystring = "SELECT Unit FROM Properties WHERE Residx = '{$residx}'";
		break;
	    case 'tenant':
	        $querystring = "SELECT Unit FROM Properties WHERE Tenantidx = '{$residx}'";
		break;
	    default:
	        $querystring = "SELECT Unit FROM Properties WHERE Residx = '{$residx}' OR Tenantidx = '{$residx}'";
		break;
	}
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	$units = array();
	for ( $i = 0; $row != NULL; $row = mysql_fetch_array($result) )
	{
		$units[$i] = $row['Unit'];
		$i++;
	}
	return $units;
}

// Function to format a phone number US style, adding a one-letter prefix if a type is specified
// X: (xxx) xxx-xxxx
// If $type argument is 'international' simply return the phone number as is
function formatPhone($number, $type) {
    $prefix = "";
    if ($number == NULL) {
	return "";
    }
    if ($type != "international") {
        $number = "(" . substr($number, 0, 3) . ") " . substr($number, 3, 3) . "-" . substr($number, 6, 4);
    }
    $prefix .= "<span title='" . ucfirst($type) . " " . $number . "'>";
    if (($type != NULL) && ($type != "") && ($type != "other") && ($type != "international")) {
	$prefix .= strtoupper(substr($type, 0, 1));
    }
    $suffix .= "</span>";
    return $prefix . $number . $suffix;
}

/******* Secure File functions *******/
function secureFileExists($fname, $con){
	$fname = mysql_real_escape_string(strtolower($fname));
	$querystring = "SELECT Idx FROM SecureFileMeta WHERE LOWER(Filename) = '{$fname}'";
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	if ( $row != NULL ) {
	    return true;
	}
	else {
	    return false;
	}
}

/******* Date and Time functions *******/
function padInt2($int)
{
     if ($int < 0)
	return false;
     if ($int < 10)
	return "0" . strval($int);
     if ($int < 100)
	return strval($int);
     return false;
}

/* padInt - formats a number to a certain length by adding zeroes in front
   arguments: $int - integer or string value of the original number
	      $targetlen - desired length of number with zeroes in front if necessary*/

function padInt($int, $targetlen) {
	if ($int < 0)
		return false;

	while ( strlen(strval($int)) < $targetlen ) {
		$int = '0' . $int;
	}
	return $int;
}

function assembleDate($month,$day,$year)
{
	$month = padInt2(intval($month));
	$day = padInt2(intval($day));
	$year = padInt2(intval($year));
	return "20" . $year . "-" . $month . "-" . $day;
}

function assembleDateTime($month,$day,$year,$hour,$minute)
{
	$month = padInt2(intval($month));
	$day = padInt2(intval($day));
	$year = padInt2(intval($year));
	$hour = padInt2(intval($hour));
	$minute = padInt2(intval($minute));
	return "20" . $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":00";
}

function displayDate($month, $day, $year) {
	$month = intval($month);
	$day = intval($day);
	$year = intval($year);
	echo $month . "/" . $day . "/" . $year;
}

function displayTime($hour, $minute)
{
	$buffer = '';
	if (( $hour == 0 ) && ( $_SESSION['24HrTime'] != 'on' )) {
	    $buffer .= 12;
	}
	else if (($hour > 12) && ( $_SESSION['24HrTime'] != 'on' )) {
	    $buffer .= $hour % 12;
	}
	else {
	    $buffer .= $hour;
	}
	$buffer .= ":" . $minute;
	if ( $_SESSION['24HrTime'] != 'on' ) {
	    if ( $hour < 12 ) {
	        $buffer .= ' AM';
	    }
	    else {
	        $buffer .= ' PM';
	    }
	}
	return $buffer;
}

function parseTime($time)
{
     return array("Year"=>substr($time,2,2), "Month"=>substr($time,5,2), "Day"=>substr($time,8,2),
		  "Hour"=>substr($time,11,2), "Minute"=>substr($time,14,2));
}

function daysInMonth($month, $year)
{
    $dayonestring = strtotime($month . '/01/' . $year);
    $leapyear = date("L", $dayonestring);
    switch ( $month )
    {
	case 2:
	    if ( !$leapyear )
		return 28;
	    else
		return 29;
	    break;
	case 4:
	case 6:
	case 9:
	case 11:
	    return 30;
	    break;
	default:
	    return 31;
	    break;
    }
}

function AMPM($hour)
{
    if (( $hour < 0 ) || ( $hour > 23 )) {
	return false;
    }
    else if ( $hour < 12 ) {
	return 'AM';
    }
    else {
	return 'PM';
    }
}

function AMPMBool($hour)
{
    if ( $hour == 'AM' ) {
	return 0;
    }
    if ( $hour == 'PM' ) {
	return 1;
    }
    if (( $hour < 0 ) || ( $hour > 23 )) {
	return false;
    }
    else if ( $hour < 12 ) {
	return 0;
    }
    else {
	return 1;
    }
}
/******* Login related functions *******/
function userExists($user, $con)
{
	global $cms;
	$exists = false;
	$user = strtolower($user);
	if ( $cms == 'wp' ) {
	    if ( username_exists($user) ) {
		$exists = true;
	    }
	}
	else {
	    $querystring = "SELECT Username FROM Login WHERE LOWER(Username) = '{$user}'";
	    $result = mysql_query($querystring, $con);
	    $exists = mysql_fetch_array($result);
	}
	if (!$exists)
	    return false;
	else
	    return true;
}
/******* Work Order functions *******/
function workorderExists($i, $con)
{
	$querystring = "SELECT Idx FROM WorkOrders WHERE Idx = {$i}";
	if ($_SESSION["Level"] == $level_developer)
	    echo $querystring . "<br />";
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	if (!$row)
	    return false;
	else
	    return true;
}

function statusText($status)
{
    require 'config.php';
    switch ($status)
    {
	case $status_submitted:
	    return "Submitted";
	    break;
	case $status_approved:
	    return "In Process";
	    break;
	case $status_denied:
	    return "Denied";
	    break;
	case $status_completed:
	    return "Completed";
	    break;
    }
}
/******* Packages functions *******/
function carrierPic($type)
{
	switch ($type)
	{
	    case "USPS":
		return "usps-logo.png";
	    case "UPS":
		return "ups-logo.png";
	    case "DHL":
		return "dhl-logo.png";
	    case "FedEx":
		return "fedex-logo.png";
	    default:
		return "globe.png";
	}
}
function packageExists($i, $con)
{
	$querystring = "SELECT Idx FROM Packages WHERE Idx = {$i}";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	if (!$row)
	    return false;
	else
	    return true;
}

/******* Violations Functions *******/
function violationExists($i, $con)
{
	$querystring = "SELECT Idx FROM Violations WHERE Idx = {$i}";
	if ($_SESSION["Level"] >= $level_developer)
	    echo $querystring . "<br />";
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	if (!$row)
	    return false;
	else
	    return true;
}
//Of active (not expired) parking violations per unit, return which violation (1st, 2nd, 3rd...)
function getOrdinalViolation($violationidx, $unit, $con, $type )
{
	$pkviolation_expiration = fetchSetting("ViolationExpiration", $con);
	$querystring = "SELECT * FROM Violations WHERE Type={$type} AND Unit='{$unit}' AND Time > DATE_SUB(CURDATE(), INTERVAL {$pkviolation_expiration} DAY) ORDER BY Time";
	$result = mysql_query($querystring, $con);
	$i = 0;
	while ( $row = mysql_fetch_array($result) )
	{
		$i++;
		if ( $row['Idx'] == $violationidx )
		    break;
	}
	return $i;
}

function lookupViolationLetter($type, $actionstatus, $con)
{
    $querystring = "SELECT Action{$actionstatus} FROM ViolationTypes WHERE Idx={$type}";
    $result = mysql_query($querystring, $con);
    $row = mysql_fetch_array($result);
    return $row['Action' . $actionstatus];
}
function expired($time, $con)
{
	$pkviolation_expiration = fetchSetting("ViolationExpiration", $con);
	$timediff = time() - strtotime($time);
	$datediff = $timediff / (60*60*24);
	if ( $datediff > $pkviolation_expiration )
	    return true;
	else
	    return false;
}
/******* Generalized macros *******/
function checkRadio($ID, $val = NULL)
{
    if ( $val == NULL ) {
	$val = 'checked';
    }
    echo "<script>document.forms['recordinput'].elements['{$ID}'].checked = '{$val}';</script>";
}
function setField($ID, $val)
{
    echo "<script>document.forms['recordinput'].elements['{$ID}'].value = '{$val}';</script>";
}

function hasSpaces($txt)
{
    if ( strpos($txt, ' ') != false )
	return true;
    if ( strpos($txt, '\n') != false )
	return true;
    if ( strpos($txt, '\r') != false )
	return true;
    return false;
}

function replaceDoubleQuotes($txt)
{
    return str_replace(chr(34),chr(39),$txt);
}

//You say yes, I say no, you say stop, I say go, ho ho...
function contradict($input) {
    switch ( $input ) {
	case 'true':
	    return 'false';
	case 'false':
	    return 'true';
	case '1':
	    return '0';
	case '0':
	    return '1';
	case 'on':
	    return 'off';
	case 'off':
	    return 'on';
	case 'yes':
	    return 'no';
	case 'no':
	    return 'yes';
	case 'hello':
	    return 'goodbye';
	case 'goodbye':
	    return 'hello';
	case true: 
	    return false;
	case false:
	    return true;
	case 1:
	    return 0;
	case 0:
	    return 1;
	case '':
	    return true;
	case null:
	    return true;
	default:
	    return false;
    }
}

/******* Debug Text for Developers *******/
function debugText($text)
{
    global $level_developer;
    global $_SESSION;
    if (( $_SESSION['Level'] == $level_developer ) && ( $_SESSION['DebugMode'] == 'on' ) )
	echo $text . "<br />";
}

function customError($errno, $errstr) {
  echo "<b>Error:</b> [$errno] $errstr<br>";
  echo "Ending Script";
  die();
} 
?>
