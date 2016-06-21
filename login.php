<?php $pagename = "login";
require 'gazebo-header.php';
$nologo = true;
?>

<?php
if ( isset( $_POST["Username"] ) )
{
	$lUsername = strtolower($_POST["Username"]);
	$_POST['Password'] = mysqli_real_escape_string($con, crypt($_POST['Password'], $encryption_salt));
	$query = "SELECT * FROM Login WHERE LOWER(Username) = '{$lUsername}' AND Password = '{$_POST["Password"]}'";
	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_array($result);
	if ( $row != FALSE )
	{
	    $_SESSION['Username'] = $row['Username'];
   	  $_SESSION['Level'] = $row['Level'];
	    $_SESSION['ColorScheme'] = $row['ColorScheme'];
	    $_SESSION['ResultsPerRow'] = $row['ResultsPerRow'];
	    $_SESSION['Residx'] = $row['Residx'];
	    $_SESSION['Expiration'] = time() + $session_expiration;
	    $_SESSION['TableDisplay'] = 1;
	    $goHome = true;
	}
	else
	    $loginFail = true;
}
if ( $_GET['logout'] == 'yes' )
{
	$_SESSION['ColorScheme'] = false;
	session_destroy();
}
?>

<?php 
if ( $_SESSION['Level'] >= $level_security )
   $homepage = pageLink('home');
else
   $homepage = "/";

if ( $goHome )
{
    echo "Navigating to home screen...<br /><br />";
    echo "<script>window.location.href='{$homepage}';</script>";
}
else if ( $_GET['logout'] == 'yes' )
{
    echo "You have successfully been logged out.<br /><br />";
}
?>

<?php include "menu-web.php" ?>
<br /><br />

<h2 style="text-align:center">Login Page</h2><br />
<?php require 'authcheck-web.php' ?>

<?php echo "<form method='post' action='" . pageLink('login') . "'>";?>
<table class="main"><tbody><tr><td>
Username:<input name="Username" type="text" required="required" />  
</td></tr><tr><td>
Password:<input name="Password" type="password" /><br />
</td></tr><tr><td>
<i><a href='forgotpass.php'>Forgot Password?</a></i><br />
<?php if ($loginFail) echo "<span style='color:#FF0000'><i>Invalid username or password.</i></span><br />"; ?>
<input type="submit" value="Login" />

<input type="reset" value="Cancel" />
</td></tr></tbody></table>
<p class='center'><a href='register.php'>Need to register? Click Here</a></p>
</form>
<?php
include 'gazebo-footer.php';
?>
