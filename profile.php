<?php $pagename = "profile";
require 'gazebo-header.php';
?>
<?php
if ( isset( $_SESSION['Level'] ) && ( $_SESSION['Level'] >= $level_security ) )
    include 'menu.php';
else if ( !isset($cms) )
    include 'menu-web.php'; 
?>
<h2 style="text-align:center">User Profile</h2>

<?php
require 'authcheck.php';

$lUsername = strtolower($_SESSION['Username']);
if (isset($cms) && ($cms == "wp"))
{
    $user = wp_get_current_user();
    $residx = get_user_meta($user->ID, "gazebo_residx", true);
}
else
{
    $querystring = "SELECT * FROM Login WHERE LOWER(Username) = '{$lUsername}'";
    debugText($querystring);
    $result1 = mysqli_query($con, $querystring);
    $row_login = mysqli_fetch_array($result1);
    $residx = $row_login['Residx'];
}

/* Pull resident info */
$querystring = "SELECT * FROM Residents WHERE Idx = '{$residx}'";
debugText($querystring);
$result2 = mysqli_query($con, $querystring);
$row_res = mysqli_fetch_array($result2);
$unitlist = fetchUnit($residx, $con);

/* Update general user info, publish settings */
$invertPublishSettings = fetchSetting( "InvertPublishSettings", $con );
if ( isset($_POST['submitted']) )
{
    $PersInfo = array("Name", "MailingAddress", "MailingAddress2", "City", "State", "ZIP", "Country", "Phone1", "Phone2", "Phone3", "Phone4", "Phone1Type", "Phone2Type", "Phone3Type", "Phone4Type", "Email", "Email2", "GuestInfo");
    $title = "User " . $_SESSION['Username'] . " (" . $row_res['Name'] . " - Unit " . $unitlist[0] . ") has changed personal info";
    $notifytext = $title . ": \n\r";
    $_POST['Phone1'] = $_POST['Phone1-1'] . $_POST['Phone1-2'] . $_POST['Phone1-3'];
    $_POST['Phone2'] = $_POST['Phone2-1'] . $_POST['Phone2-2'] . $_POST['Phone2-3'];
    $_POST['Phone3'] = $_POST['Phone3-1'] . $_POST['Phone3-2'] . $_POST['Phone3-3'];
    $_POST['Phone4'] = $_POST['Phone4-1'] . $_POST['Phone4-2'] . $_POST['Phone4-3'];
    foreach ( $PersInfo as $key )
    {
	   //Publish info
        if (isset($row_res['Publish' . $key]) &&
	   ((($invertPublishSettings == 'false') && isset($_POST['Publish' . $key]) && ($row_res['Publish' . $key] == 0)) 	|| (($invertPublishSettings == 'true') && !isset($_POST['Publish' . $key]) && ($row_res['Publish' . $key] == 0))) && ( fetchSetting( "Publish" . $key . "Visibility", $con ) == 'enabled' ))
        {
            $querystring = "UPDATE Residents SET Publish{$key} = 1 WHERE Idx = {$row_res['Idx']}";
            debugText($querystring);
            $result = mysqli_query($con, $querystring);
            if ( $result ) {
                echo $key . " is now published.<br />";
        		$notifytext .= $key . " is now published in the roster.\n\r";
        		$notify = true;
    	    }
            else {
    	        echo $key . " publish update failed.<br />";
    		    $notifytext .= $key . " - publish update failed. Database Error.\n\r";
    		    $notify = true;
    	    }
        }
	   //Unpublish
        else if (isset($row_res['Publish' . $key]) &&
		((($invertPublishSettings == 'false') && !isset($_POST['Publish' . $key]) && ($row_res['Publish' . $key] == 1)) || (($invertPublishSettings == 'true') && isset($_POST['Publish' . $key]) && ($row_res['Publish' . $key] == 1))) && ( fetchSetting( "Publish" . $key . "Visibility", $con ) == 'enabled' ))
        {
            $querystring = "UPDATE Residents SET Publish{$key} = 0 WHERE Idx = {$row_res['Idx']}";
            debugText($querystring);
            $result = mysqli_query($con, $querystring);
            if ( $result ) {
                echo $key . " is now unpublished.<br />";
    		$notifytext .= $key . " is now unpublished in the roster.\n\r";
    		$notify = true;
    	    }
            else {
    	        echo $key . " unpublish update failed.<br />";
    		    $notifytext .= $key . " - unpublish update failed. Database Error.\n\r";
    		    $notify = true;
    	    }
        }
    	//Update user fields
    	if ( isset($_POST[$key]) && ($row_res[$key] != $_POST[$key]) && ($key != "Name"))
    	{
        	$querystring = "UPDATE Residents SET {$key} = '{$_POST[$key]}'";
    	    $querystring .= " WHERE Idx = {$row_res['Idx']}";
            debugText($querystring);
            $result = mysqli_query($con, $querystring);
            if ( $result ) {
                echo $key . " is now changed.<br />";
        		$notifytext .= $key . " has changed to {$_POST[$key]}.\n\r";
        		$notify = true;
    	    }
            else {
    	        echo $key . " update failed.<br />";
        		$notifytext .= $key . " - personal info update failed. Database Error. Text = {$_POST[$key]}\n\r";
        		$notify = true;
        	}
        } 
    }
    if ( $notify ) { 
    	$to = fetchSetting("ResidentInfoChangeEmail", $con);
    	$headers = "From: " . fetchSetting("Name", $con) . " Website System";
    	if ( $to != "" )
    	    mail($to, $title, $notifytext, $headers);
    	debugText("Mail sent to {$to} <br /> Subject: {$title}<br />  Text: {$notifytext}<br /> ");
    }

    //Reload user's info as it may have changed.
    $querystring = "SELECT * FROM Residents WHERE Idx = '{$residx}'";
    debugText($querystring);
    $result2 = mysqli_query($con, $querystring);
    $row_res = mysqli_fetch_array($result2);
}
/* Update password */
if ( isset($_POST['oldPass']) && isset($_POST['newPass']) && isset ($_POST['confPass'])
 && ($_POST['oldPass'] != '') && ($_POST['newPass'] != ''))
{
    $lUsername = strtolower($_SESSION['Username']);

    //Error checking
    if ($_POST['oldPass'] == $_POST['newPass']) {
	echo "<span style='color:red'>New password must be different from old password.<br /></span>";
    }
    else if ( $_POST['newPass'] != $_POST['confPass'] ) {
	echo "<span style='color:red'>Please confirm password again.<br /></span>";
    } 
    else if ( !isset( $cms ) ) //Update database (standalone)
    {
        $querystring = "SELECT * FROM Login WHERE LOWER(Username) = '{$lUsername}'";
        debugText($querystring);
        $result = mysqli_query($con, $querystring);
        $row = mysqli_fetch_array($result);
        if ( $row['Password'] == mysqli_real_escape_string($con,crypt($_POST['oldPass'], $encryption_salt ))) {
        	debugText($querystring);
        	$_POST['newPass'] = mysqli_real_escape_string($con,crypt($_POST['newPass'], $encryption_salt)); //encrypt password
        	$querystring = "UPDATE Login SET Password='{$_POST['newPass']}' WHERE Username='{$_SESSION['Username']}'";
        	debugText($querystring);
        	$result = mysqli_query($con, $querystring);
    	}
        else {
    	    echo "<span style='color:red'>Incorrect password.</span><br />";
    	}
    }
    else if ( $cms == "wp" ) //Update database (wordpress)
    {
    	$creds = array();
    	$creds['user_login'] = wp_get_current_user()->user_login;
    	$creds['user_password'] = $_POST['oldPass'];
    	$creds['remember'] = false;
    	$user = wp_signon( $creds, false );
    	if ( !is_wp_error($user) ) {
    	    wp_set_password($_POST['newPass'], wp_get_current_user()->ID);
    	    $result = true;
    	}
    	else {
    	    echo "<span style='color:red'>Incorrect password.</span><br />";
    	}
    }
    if ( $result )
	echo "Password changed successfully! <a onclick=\"window.location.href = '/';\">Please click here to return to login page.</a><br />";
    else
	echo "Password change failed.<br />";
}

/* Update Color Scheme */
if ( isset($_POST['updateColor']) && ($_POST['updateColor'] == 'yes') )
{
    if ( $cms == 'wp' ) {
	update_user_meta($user->ID, 'gazebo_colorscheme', $_POST['colorSelect']);
	echo "Color scheme changed successfully!<br />";
    }
    else {
        $querystring = "UPDATE Login SET ColorScheme={$_POST['colorSelect']} WHERE Username='{$_SESSION['Username']}'";
        debugText($querystring);
        $result = mysqli_query($con, $querystring);
        if ( $result )
	    echo "Color scheme changed successfully!<br />";
        else
	    echo "Color scheme change failed.<br />";
    }
}

/* Update Time Settings */
if ( isset($_POST['update24HrTime']) && ($_POST['update24HrTime'] == 'yes') ) {
    if ( $cms == 'wp' ) {
	update_user_meta($user->ID, 'gazebo_24hrtime', $_POST['24HrTime']);
	echo "Time display setting changed successfully!<br />";
    }
    else {
	$querystring = "UPDATE Login SET 24HrTime='{$_POST['24HrTime']}' WHERE Username='{$_SESSION['Username']}'";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
		echo "Time display setting changed successfully!<br />";
	else
		echo "Time display setting change failed.<br />";
    }
}

/* Update Debug Settings */
if ( isset($_POST['updateDebugMode']) && ($_POST['updateDebugMode'] == 'yes') ) {
    if ( $cms == 'wp' ) {
	update_user_meta($user->ID, 'gazebo_debugmode', $_POST['debugMode']);
	echo "Debug Mode setting changed successfully!<br />";
    }
    else {
	$querystring = "UPDATE Login SET DebugMode='{$_POST['debugMode']}' WHERE Username='{$_SESSION['Username']}'";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
		echo "Debug Mode setting changed successfully!<br />";
	else
		echo "Debug Mode setting change failed.<br />";
    }
}

/* Display Profile Editor Form */
echo "
<form style='text-align:center' name='profile' method='post' action='" . pageLink("profile") . "' >
<table class='criteria' border='2'><tbody>
<tr><td><h4 style='text-align:center'>Display</h4>
<input type='hidden' name='updateColor' value='' />
<input type='hidden' name='update24HrTime' value='' />
<input type='hidden' name='updateDebugMode' value='' />
Color Scheme:&nbsp;<select id='colorSelect' name='colorSelect' onchange=\"document.forms['profile'].elements['updateColor'].value = 'yes'\" >
<option value='0'>Aqua (default)</option>
<option value='1'>White on Blue</option>
<option value='2'>Pink</option>
<option value='3'>Monochrome</option>
<option value='4'>Green on Black</option>
<option value='5'>Honeycomb</option>
<option value='6'>Ultraviolet</option>
<option value='7'>No Style</option>
</select><br />
Time Display:&nbsp;&nbsp;
<input id='12HrOption' type='radio' name='24HrTime' value='off' checked=true onchange=\"document.forms['profile'].elements['update24HrTime'].value = 'yes'\" />&nbsp;12-hour&nbsp;&nbsp;
<input id='24HrOption' type='radio' name='24HrTime' value='on' onchange=\"document.forms['profile'].elements['update24HrTime'].value = 'yes'\" />&nbsp;24-hour&nbsp;&nbsp;";


if ( $_SESSION['Level'] >= $level_developer ) {
    echo "<br /><input id='debugMode' name='debugMode' onchange=\"document.forms['profile'].elements['updateDebugMode'].value = 'yes'\" 
type='checkbox' />&nbsp;Debug Mode";
}
echo "</td>";
echo "<td colspan='2'>
<h4 style='text-align:center'>Change Password</h4>
<input type='hidden' name='oldPass' />
<input type='hidden' name='newPass' />
<input type='hidden' name='confPass' />
Old Password: &nbsp;<input type='password' name='oldPassBox' id='oldPassBox' autocomplete='off' /><br />
New Password: <input type='password' name='newPassBox' id='newPassBox' autocomplete='off' /><br />
Confirm New Password: <input type='password' name='confPassBox' id='confPassBox' autocomplete='off' /><br />
</td></tr>";

if (($row_res != false) && ($row_res['Type'] == 0))
{
    if ($row_res['Phone1Type'] == 'international') {
	$phone11 = $row_res['Phone1'];
    }
    else {
	$phone11 = substr($row_res['Phone1'], 0, 3);
	$phone12 = substr($row_res['Phone1'], 3, 3);
	$phone13 = substr($row_res['Phone1'], 6, 4);
    }
    if ($row_res['Phone2Type'] == 'international') {
	$phone21 = $row_res['Phone2'];
    }
    else {
	$phone21 = substr($row_res['Phone2'], 0, 3);
	$phone22 = substr($row_res['Phone2'], 3, 3);
	$phone23 = substr($row_res['Phone2'], 6, 4);
    }
    if ($row_res['Phone3Type'] == 'international') {
	$phone31 = $row_res['Phone3'];
    }
    else {
	$phone31 = substr($row_res['Phone3'], 0, 3);
	$phone32 = substr($row_res['Phone3'], 3, 3);
	$phone33 = substr($row_res['Phone3'], 6, 4);
    }
    if ($row_res['Phone4Type'] == 'international') {
	$phone41 = $row_res['Phone4'];
    }
    else {
	$phone41 = substr($row_res['Phone4'], 0, 3);
	$phone42 = substr($row_res['Phone4'], 3, 3);
	$phone43 = substr($row_res['Phone4'], 6, 4);
    }
    echo "<input type='hidden' name='submitted' value='yes' />";
    echo "<tr><th colspan='2'><h4>Resident Information</h4></th>";
    echo "<th><strong>";
    if ( $invertPublishSettings == "true" ) {
	echo "DO NOT Publish:";
    }
    else {
	echo "Publish?";
    }
    echo "</strong></th></tr>";
    echo "<tr><td>Name</td><td><span title='If you are requesting a name change, please contact management.'>{$row_res['FirstName']} {$row_res['LastName']}";
    if ( $row_res['LastName2'] != "" ) {
	echo "<br />" . $row_res['FirstName2'] . " " . $row_res['LastName2'];
    }
    echo "</span>";
    $namechangeform = fetchSetting("NameChangeForm", $con);
    if (( $namechangeform != NULL ) && ( $namechangeform != "" )) {
	echo "<br /><i>To change a name on the account, please fill out the <a href='";
	echo pageLink("securefile", "Idx={$namechangeform}") . "'>form.</a></i>";
    }

    echo "</td>";
    echo "<td>";
    switch ( fetchSetting( "PublishNameVisibility", $con )) {
        case "enabled":
          echo "<input type='checkbox' name='PublishName' />";
          break;
        case "locked":
          echo "<input type='checkbox' name='PublishName' disabled='disabled' />";
    }
    echo "</td></tr>";
    echo "<tr><td>Mailing Address: </td><td>
Address 1:&nbsp;<input type='text' name='MailingAddress' size='40' value='{$row_res['MailingAddress']}' /><br />
Address 2:&nbsp;<input type='text' name='MailingAddress2' size='40' value='{$row_res['MailingAddress2']}' /><br />
City:&nbsp;<input type='text' name='City' size='20' value='{$row_res['City']}' />&nbsp;&nbsp;
State:&nbsp;<input type='text' name='State' size='3' value='{$row_res['State']}' />&nbsp;&nbsp;
ZIP:&nbsp;<input type='text' name='ZIP' size='5' value='{$row_res['ZIP']}' />&nbsp;&nbsp;
Country:&nbsp;<input type='text' name='Country' size='10' value='{$row_res['Country']}' />&nbsp;&nbsp;
</td>
<td>";
    switch ( fetchSetting( "PublishMailingAddressVisibility", $con )) {
        case "enabled":
          echo "<input type='checkbox' name='PublishMailingAddress' />";
          break;
        case "locked":
          echo "<input type='checkbox' name='PublishMailingAddress' disabled='disabled' />";
    }
echo "</td></tr>
<tr><td>Phone #1: </td><td>
<select name='Phone1Type' id='Phone1Type' class='PhoneType'></select>&nbsp;
<span class='Phone1StdFormatting'>(</span>
<input type='text' name='Phone1-1' id='Phone1-1' size='3' value='{$phone11}' class='telEntrySec3' />
<span class='Phone1StdFormatting'>)&nbsp;</span>
    <input type='text' name='Phone1-2' id='Phone1-2' size='3' value='{$phone12}' class='telEntrySec3' />
<span class='Phone1StdFormatting'>-</span>
    <input type='text' name='Phone1-3' id='Phone1-3' size='4' value='{$phone13}' class='telEntrySec4' />
    </td><td>";
    switch ( fetchSetting( "PublishPhone1Visibility", $con )) {
        case "enabled":
          echo "<input type='checkbox' name='PublishPhone1' />";
          break;
        case "locked":
          echo "<input type='checkbox' name='PublishPhone1' disabled='disabled' />";
    }
echo "</td></tr>

    <tr><td>Phone #2: </td><td>
    <select name='Phone2Type' id='Phone2Type' class='PhoneType'></select>&nbsp;
<span class='Phone2StdFormatting'>(</span>
<input type='text' name='Phone2-1' id='Phone2-1' size='3' value='{$phone21}' class='telEntrySec3' />
<span class='Phone2StdFormatting'>)&nbsp;</span>
    <input type='text' name='Phone2-2' id='Phone2-2' size='3' value='{$phone22}' class='telEntrySec3' />
<span class='Phone2StdFormatting'>-</span>
    <input type='text' name='Phone2-3' id='Phone2-3' size='4' value='{$phone23}' class='telEntrySec4' />
    </td><td>";
    switch ( fetchSetting( "PublishPhone2Visibility", $con )) {
        case "enabled":
          echo "<input type='checkbox' name='PublishPhone2' />";
          break;
        case "locked":
          echo "<input type='checkbox' name='PublishPhone2' disabled='disabled' />";
    }
echo "</td></tr>

    <tr><td>Phone #3: </td><td>
    <select name='Phone3Type' id='Phone3Type' class='PhoneType'></select>&nbsp;
<span class='Phone3StdFormatting'>(</span>
<input type='text' name='Phone3-1' id='Phone3-1' size='3' value='{$phone31}' class='telEntrySec3' />
<span class='Phone3StdFormatting'>)&nbsp;</span>
    <input type='text' name='Phone3-2' id='Phone3-2' size='3' value='{$phone32}' class='telEntrySec3' />
<span class='Phone3StdFormatting'>-</span>
    <input type='text' name='Phone3-3' id='Phone3-3' size='4' value='{$phone33}' class='telEntrySec4' />
    </td><td></td></tr>

    <tr><td>Phone #4: </td><td>
    <select name='Phone4Type' id='Phone4Type' class='PhoneType'></select>&nbsp;
<span class='Phone4StdFormatting'>(</span>
<input type='text' name='Phone4-1' id='Phone4-1' size='3' value='{$phone41}' class='telEntrySec3' />
<span class='Phone4StdFormatting'>)&nbsp;</span>
    <input type='text' name='Phone4-2' id='Phone4-2' size='3' value='{$phone42}' class='telEntrySec3' />
<span class='Phone4StdFormatting'>-</span>
    <input type='text' name='Phone4-3' id='Phone4-3' size='4' value='{$phone43}' />
    </td><td></td></tr>

    <tr><td>Email: </td><td><input type='text' name='Email' size='40' value='{$row_res['Email']}' /></td>
    <td>";
    switch ( fetchSetting( "PublishEmailVisibility", $con )) {
        case "enabled":
          echo "<input type='checkbox' name='PublishEmail' />";
          break;
        case "locked":
          echo "<input type='checkbox' name='PublishEmail' disabled='disabled' />";
    }
echo "</td></tr>

    <tr><td>Email 2: </td><td><input type='text' name='Email2' size='40' value='{$row_res['Email2']}' /></td>
    <td></td></tr>

    <tr><td colspan='3'></td></tr>";
    if ( fetchSetting( "ShowGuestInfo", $con ) == 'true' ) {
	echo "<tr><td colspan='3'><h3>Guest Information</h3><textarea cols='80' rows='3' style='height:80px; width:400px' name='GuestInfo'>{$row_res['GuestInfo']}</textarea><br /><br /></td></tr>";
    }
} //Submit forms, circumvent remember password box from browser with jQuery
echo "<tr><td colspan='3'><input type='submit' value='Save Changes' onclick='
 document.forms[\"profile\"].elements[\"oldPass\"].value = document.forms[\"profile\"].elements[\"oldPassBox\"].value;
 document.forms[\"profile\"].elements[\"newPass\"].value = document.forms[\"profile\"].elements[\"newPassBox\"].value;
 document.forms[\"profile\"].elements[\"confPass\"].value = document.forms[\"profile\"].elements[\"confPassBox\"].value;
 document.forms[\"profile\"].elements[\"oldPassBox\"].value = \"\";
 $(\"#oldPassBox\").attr(\"type\", \"text\");
 document.forms[\"profile\"].elements[\"newPassBox\"].value = \"\";
 $(\"#newPassBox\").attr(\"type\", \"text\");
 document.forms[\"profile\"].elements[\"confPassBox\"].value = \"\";
 $(\"#confPassBox\").attr(\"type\", \"text\");' /></td></tr>";
echo "</tbody></table></form>";
debugText("<br />Current Time: " . time() .
	  "<br />Gazebo Session Length: " . $session_expiration);
if ( isset( $cms ) && ( $cms == "wp" )) {
debugText("<br />Current WP User ID: " . get_current_user_id() . 
	  "<br />WP Session Expiration: " . get_user_meta(get_current_user_id(), 'expiration', true));
}
if ((($invertPublishSettings == 'false') && ($row_res['PublishName'] == 1)) ||
    (($invertPublishSettings == 'true') && ($row_res['PublishName'] == 0)))
    echo "<script>document.forms['profile'].elements['PublishName'].checked='true';</script>";
if ((($invertPublishSettings == 'false') && ($row_res['PublishMailingAddress'] == 1)) ||
    (($invertPublishSettings == 'true') && ($row_res['PublishMailingAddress'] == 0)))
     echo "<script>document.forms['profile'].elements['PublishMailingAddress'].checked='true';</script>";
if ((($invertPublishSettings == 'false') && ($row_res['PublishPhone1'] == 1)) ||
    (($invertPublishSettings == 'true') && ($row_res['PublishPhone1'] == 0)))
    echo "<script>document.forms['profile'].elements['PublishPhone1'].checked='true';</script>";
if ((($invertPublishSettings == 'false') && ($row_res['PublishPhone2'] == 1)) ||
    (($invertPublishSettings == 'true') && ($row_res['PublishPhone2'] == 0)))
    echo "<script>document.forms['profile'].elements['PublishPhone2'].checked='true';</script>";
if ((($invertPublishSettings == 'false') && ($row_res['PublishEmail'] == 1)) ||
    (($invertPublishSettings == 'true') && ($row_res['PublishEmail'] == 0)))
    echo "<script>document.forms['profile'].elements['PublishEmail'].checked='true';</script>";



echo "<script>
	function profilePhoneType() {";
	    for ( $i = 1; $i <= 4; $i++ ) {
		echo "
		    var selectObj = document.forms['profile'].elements['Phone{$i}Type'];
		    if (('{$row_res['Phone' . $i . 'Type']}' == '' ) || ('{$row_res['Phone' . $i . 'Type']}' == null )) {
		        selectObj.options[0].selected = true;
		    }
		    else {
    		        for (var j = 0; j < selectObj.options.length; j++) {
        		    if (selectObj.options[j].value == '{$row_res['Phone' . $i . 'Type']}') {
            		        selectObj.options[j].selected = true;
			        break;
        	            }
			}	
    		    }
		    if ('{$row_res['Phone' . $i . 'Type']}' == 'international') {
			convertPhoneFieldIntl('{$i}');
		    }";
	    }

echo	"}
</script>";

echo "<script>document.forms['profile'].elements['colorSelect'].selectedIndex = {$_SESSION['ColorScheme']};</script>";
if ( $_SESSION['24HrTime'] == 'on' ) {
    echo "<script>document.forms['profile'].elements['24HrOption'].checked = true;</script>";
}
if ( $_SESSION['DebugMode'] == 'on' ) {
    echo "<script>document.forms['profile'].elements['debugMode'].checked = 'checked';</script>";
}
include 'gazebo-footer.php';
?>