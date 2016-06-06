<?php $pagename = "forgotpass";
require 'gazebo-header.php';
$nologo = true;
?>
<?php require_once 'securimage/securimage.php'; ?>

<?php 
if ( ! isset( $cms ) ) {
include "menu-web.php";
}; ?>

<?php
if ( isset( $_POST['Username'] ))
{
	$image = new Securimage();
	if ($image->check($_POST['captcha_code']) == false) 
	{
	  // Captcha code was incorrect
	  echo "<div style='color:#FF0000'>The security code entered was incorrect. You may try again. <br /></div>";
	  echo "Please re-enter your email or username, and try again with a new captcha image</div>.";
	}
	else
	{
		$entry = strtolower($_POST['Username']);
		if ( strstr($entry, '@') )    //Entry is an email
		{	
		    $querystring = "SELECT Residents.Email, Login.Username FROM Login, Residents WHERE LOWER(Residents.Email) = '{$entry}' AND Login.Residx = Residents.Idx";
		    $result = mysqli_query($con, $querystring);
		    $row = mysqli_fetch_array($result);
		    if ( !$row )
		        echo "<div style='color:#FF0000'>Email is not registered in the system.</div>";
		    else
		    {
		        $email = $row['Email'];
			$username = $row['Username'];
		    }
		}
		else    //Entry is a username
		{
		    if ( !userExists($_POST['Username'], $con) )
		        echo "<div style='color:#FF0000'>Username does not exist.</div>";
		    else
		    {
		        $querystring = "SELECT Residents.Email, Login.Username FROM Login, Residents WHERE LOWER(Login.Username) = '{$entry}' AND Login.Residx = Residents.Idx";
		        $result = mysqli_query($con, $querystring);
		        $row = mysqli_fetch_array($result);
			if ( !$row )
		            echo "<div style='color:#FF0000'>Username does not have an email registered - please contact management.</div>";
		        else
			{
		            $email = $row['Email'];
			    $username = $row['Username'];
			}
		    }
		}
	}
	if ( isset( $email ))
	{
	    //Create a hashed token against current date, a random number and salt
	    $token = mysqli_real_escape_string($con, crypt(date() . mt_rand(10,100), $encryption_salt));

	    //Set user's token and expiration date in Login
	
	    $querystring = "UPDATE Login SET ResetToken = '{$token}', ResetTokenExpires = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE Username = '{$username}'";

	    $result = mysqli_query($con, $querystring);
	    if ( $result )
		echo "Reset token saved.<br />";
	    else
		echo "Reset token failed to save.<br />";

	    $commname = fetchSetting('Name', $con);
	    $resetlink = pageLink("resetpass", "token={$token}", null, true);
	    //Send email with a GET link to resetpass.php?token= ...
	    $subject = "{$commname} Website - Password Reset";
	    $message = "You have requested a password reset for the {$commname} website.\n\r\n\r";
	    $message .= "Your username is: " . $username . "\n\r\n\r";
	    $message .= "The following link will allow you to reset your password:\n\r\n\r ";
	    $message .= "<a href='" . $resetlink . "'>" . $resetlink . "</a>\n\r";
	    $mailresult = mail($email, $subject, $message);

	    if ( $mailresult )
		echo "Password reset email sent. Please follow the link in the email to reset your password in the next 24 hours.<br />";
	    else
		echo "Password reset email failed to send.<br />";
	    $success = true;
	}
}
if ( !$success )
{
    echo "
    <table class='main'><tr><td>
    <h2>Forgot username or password</h2>
    <form name='forgot' method='post' action='" . pageLink('forgotpass') . "'>
    Username or Email: <input type='text' name='Username' size='40' required='required' /><br />
    Captcha Image: <div style='text-align:center'>";
    echo Securimage::getCaptchaHtml(array('securimage_path'=>$securimagedir, 'image_attributes'=>array('align'=>'center')));
    echo "&nbsp;</div>";
    /*Enter Captcha text <input type='text' name='captcha_code' size='10' maxlength='6' /><br />*/
    echo "<i>This is to ensure that you are a real human attempting to retrieve a password.</i><br />
    <input type='submit' value='Submit' /><input type='reset' value='Reset' />
    </form></td></tr></table>";
}
include 'gazebo-footer.php';
?>
