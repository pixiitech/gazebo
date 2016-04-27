<?php $pagename = 'resetpass.php'; 
$nologo = true;
require 'gazebo-header.php';
?>

<?php require_once 'securimage/securimage.php'; ?>

<?php if ( !isset($cms)) include "menu-web.php" ?>

<?php
$querystring = "SELECT Username, ResetToken, ResetTokenExpires FROM Login WHERE ResetToken = '{$_GET['token']}' AND ResetTokenExpires >= CURDATE()";
$result = mysql_query($querystring, $con);
$row = mysql_fetch_array($result);
if ( !$row )
    echo "Invalid Password Token. Please <a href='" . pageLink("forgotpass") . "'>try again.</a><br />";
else if ( isset($_POST['newPass']) && isset($_POST['confPass']))
{
    echo "<p class='main'>";
    $lUsername = strtolower($row['Username']);
    if ( $_POST['newPass'] != $_POST['confPass'] )
	 echo "Please confirm password again.<br />";
    debugText($querystring);
    $_POST['newPass'] = mysql_real_escape_string(crypt($_POST['newPass'], $encryption_salt)); //encrypt password
    $querystring = "UPDATE Login SET Password='{$_POST['newPass']}', ResetToken=NULL, ResetTokenExpires=NULL WHERE LOWER(Username)='{$lUsername}'";
    $result = mysql_query($querystring, $con);
    if ( $result )
	echo "Password changed successfully! Please <a href='" . pageLink("login") . "'>login</a> again.<br />";
    else
	echo "Password change failed.<br />";
    echo "</p>";
}
else
{
    echo "Password Reset Form.<br />";
    echo "<form class='criteria' method='POST' target='" . pageLink("resetpass") . "'>";
    echo "<input type='hidden' name='token' value='{$_GET['token']}' />";
    echo "<h4 style='text-align:center'>Change Password</h4>";
    echo "New Password: <input type='password' name='newPass' /><br />";
    echo "Confirm New Password: <input type='password' name='confPass' /><br />";
    echo "<input type='submit' /></form>";
}

include 'gazebo-footer.php';
?>
