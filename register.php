<?php
$pagename = "register";
require 'gazebo-header.php';

if ( !isset($cms)) include "menu-web.php";
require_once 'securimage/securimage.php';

/* Redirect users that are already signed in */
if ( isset( $cms ) )
{
    if ( is_user_logged_in() )
	echo "<script>window.location.href = '/';</script>";
}
else if ( isset( $_SESSION['Level'] ) && ( $_SESSION['Level'] > $level_disabled ) )
    echo "<script>window.location.href = '/';</script>";

$communityName = fetchSetting("Name", $con);

$tenant_expiration = fetchSetting( "TenantExpiration", $con ) * 60 * 60 * 24;

$success = false;

if ( isset( $_POST['Username'] ))
{
	$dualreg = fetchSetting( "DualRegistration", $con );
	//Find Residx in the system
	$success = false;
	$querystring = "SELECT Residx, Tenantidx FROM Properties WHERE Properties.Unit = '{$_POST['Unit']}'";
	$result = mysqli_query($con, $querystring);
	$row = mysqli_fetch_array($result);
	$regcount = 0;
	if ( $_POST['OwnerTenant'] == "Owner" )
	{
	    $residx = $row['Residx'];
	    $newlevel = $level_resident;
	}
	else if ( $_POST['OwnerTenant'] == "Tenant" )
	{
	    $residx = $row['Tenantidx'];
	    $newlevel = $level_tenant;
	}
	//Check to see if resident is already registered

	if ( $cms == "wp" )
	{
    $user_query = new WP_User_Query( array( 'meta_key' => 'gazebo_residx', 'meta_value' => $residx ) );
    if ( ! empty( $user_query->results ) ) {
      foreach ( $user_query->results as $user ) {
        $regcount++;
      }
    }
	}
	else
	{
    $querystring = "SELECT Residx FROM Login WHERE Residx = {$residx}";
    $result = mysqli_query($con, $querystring);
    while ( $row = mysqli_fetch_array($result) ) {
      $regcount++;
    }
	}

  //Determine if email is already registered as email 1 or 2
  if ( trim(strtolower($_POST['Email'])) == trim(strtolower(fetchResidentEmail($residx, $con)))) {
    $emailreg = 1;
  }
  else if ( trim(strtolower($_POST['Email'])) == trim(strtolower(fetchResidentEmail($residx, $con, '2')))) {
    $emailreg = 2;
  }
  else
    $emailreg = 0;

	//Check captcha
  $image = new Securimage();
	if ($image->check($_POST['captcha_code']) == false)
	{
	  // Captcha code was incorrect
	  echo "<div class='registration-error'>The security code entered was incorrect. You may try again. ";
	  echo "Please re-enter your password, confirm the password, and try again with a new captcha image.</div>";
	}
	//Other validations
	else if ( userExists($_POST['Username'], $con) )
	    echo "<div class='registration-error'>Username already exists - please try creating another username.</div>";
	else if ( !validateUnit($_POST['Unit'], $con ) )
	    echo "<div class='registration-error'>Invalid unit number - try again.</div>";
	else if ( ($dualreg == 'true') && ($regcount >= 2) )
	    echo "<div class='registration-error'>Unit already has website logins assigned. Please contact management.</div>";
	else if ( ($dualreg != 'true') && ($regcount >= 1) )
	    echo "<div class='registration-error'>Unit already has a website login assigned. Please contact management.</div>";
	else if ( $_POST['Password'] != $_POST['CPassword'] )
	    echo "<div class='registration-error'>Password and Confirm Password do not match. Please try again.</div>";
	else if ( hasSpaces( $_POST['Username'] ) )
	    echo "<div class='registration-error'>Username cannot contain spaces.</div>";
	else if (( fetchSetting("RegistrationValidationByEmail", $con) == 'true' ) && $emailreg == 0) {
	    echo "<div class='registration-error'>Email {$_POST['Email']} does not match records. Please contact administration.</div>";
	}
	else if ( $cms == "wp" )
	{
	    //Check if email is already registered (only happens if dual registration is enabled,
	    //	otherwise validation would have failed)
	    $user_query = new WP_User_Query( array(
        'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'gazebo_residx',
						'value'   => $residx,
						'type'    => 'numeric',
						'compare' => '='
					),
					array(
						'key'     => 'gazebo_emailreg',
						'value'   => $emailreg,
						'type'    => 'numeric',
						'compare' => '='
					)
        )
      ));

    if ( ! empty( $user_query->results ) ) {
      echo "<div class='registration-error'>Email already registered. Please contact management.</div>";
    }
    else {
      $_POST['Username'] = mysqli_real_escape_string($con, $_POST['Username']);
      $_POST['Password'] = mysqli_real_escape_string($con, $_POST['Password']);
      $userdata = array ('user_login' => $_POST['Username'], 'user_pass' => $_POST['Password'] );
      $newID = wp_insert_user( $userdata );
      add_user_meta( $newID, 'gazebo_level', $newlevel);
      add_user_meta( $newID, 'gazebo_residx', $residx);
      add_user_meta( $newID, 'gazebo_colorscheme', $default_colorscheme);
      add_user_meta( $newID, 'gazebo_emailreg', $emailreg);
      if ( $_POST['OwnerTenant'] == "Tenant" )
      {
        add_user_meta($newID, 'expiration', time() + $tenant_expiration);
        $expirationdate = date("m-d-Y", time() + $tenant_expiration);
      }
      get_user_by('id', $newID)->set_role($wp_roles[$level_resident]);
      $success = true;
    }
	}
	else //non-wordpress
	{
    $querystring = "SELECT EmailReg FROM Login WHERE EmailReg = '{$emailreg}' AND Residx = {$residx}";
    $result = mysqli_query($con, $querystring);
    if ( mysqli_fetch_array($result) ) {
      echo "<div class='registration-error'>Email already registered. Please contact management.</div>";
    } else {
      $_POST['Username'] = mysqli_real_escape_string($con, $_POST['Username']);
      $_POST['Password'] = mysqli_real_escape_string($con, crypt($_POST['Password'], $encryption_salt));
      $querystring = "INSERT INTO Login (Username, Password, Level, Residx, ColorScheme, ResultsPerRow, EmailReg) VALUES
          ('{$_POST['Username']}', '{$_POST['Password']}', {$newlevel}, {$residx}, {$default_colorscheme},
          4, {$emailreg})";

      $result = mysqli_query($con, $querystring);
      if ( $result ) {
        $success = true;
      }
    }
  }
}

echo "<table class='main'><tr><td>";
if ( $success )
{
  echo "<h3>Creating your new user account was successful! Please login with your new username {$_POST['Username']} and password.</h3> \n\r";
  if ( isset( $expirationdate ))
    echo "Account expires on " . $expirationdate . ".<br />";

  /* Send Email Notification */
  $to = fetchSetting("RegisterEmail", $con);
  $headers = "From: " . fetchSetting("Name", $con) . " Website System";
  $mailresult = mail($to, "New user registration - " . $_POST['Username'],
    "Resident " . fetchResname($residx, $con) . " in unit " . fetchUnit($residx, $con) . " has successfully
     registered for the website. " . date("m-d-Y h:i:sa"), $headers);
  if ( $mailresult )
    debugText("Email sent to " . $to . ".<br />");
}
else
{
    echo "
    <h2>Register</h2>
    <h5>Join the {$communityName} web site! Enter your information so an online account can be created. You can:</h5>
    <ul>
    <li>View community events and announcements</li>
    <li>View important documents and download forms</li>
    <li>Submit a work order (service request)</li>
    <li>Update your email address for communication</li>
    </ul>
    <form name='register' method='post' action='" . pageLink("register") . "'>
    <table style='border: none'>
      <tr>
        <td>
          Name:
        </td><td>
          <input type='text' name='Name' size='25' required='required' value='{$_POST['Name']}' />
        </td>
      </tr>
      <tr>
        <td>
          Unit #
        </td><td>
          <input type='text' name='Unit' size='5' required='required' value='{$_POST['Unit']}' />
        </td>
      </tr>
      <tr>
        <td colspan='2'>";
        if ( fetchSetting( "TenantRegistration", $con ) == "true" ) {
          echo "<input type='radio' name='OwnerTenant' required='required' value='Owner'>&nbsp;Owner&nbsp;&nbsp;
        <input type='radio' name='OwnerTenant' required='required' value='Tenant'>&nbsp;Tenant<br />";
        }
        else {
          echo "<input type='hidden' name='OwnerTenant' value='Owner' />";
        }
    echo "
        </td>
      </tr>
      <tr>
        <td>
          New Username
        </td><td>
          <input type='text' name='Username' size='15' required='required' value='{$_POST['Username']}' />
        </td>
      </tr>
      <tr>
        <td>
          New Password
        </td><td>
          <input type='password' name='Password' size='15' required='required' />
        </td>
      </tr>
      <tr>
        <td>
          Confirm Password
        </td><td>
          <input type='password' name='CPassword' required='required' size='15' />
        </td>
      </tr>
      <tr>
        <td>
          E-Mail
        </td><td>
          <input type='email' name='Email' size='25' value='{$_POST['Email']}' />
        </td>
      </tr>
      <tr>
        <td>
          Captcha Image:
        </td><td>";
          echo Securimage::getCaptchaHtml(array('securimage_path'=>$securimagedir, 'show_text_input'=>false, 'image_attributes'=>array('style'=>'captcha-container')));
          echo "&nbsp;<br />Type the text in the picture: <input type='text' name='captcha_code' size='15' required='required' /></div><br />";
          echo "<i>This is to ensure that you are a real human attempting to create an account.</i>
        </td>
      </tr>
      <tr>
        <td colspan='2'>
          <input type='submit' value='Submit' /><input type='reset' value='Reset' />
        </td>
      </tr>
    </table>
    </form>";
}
echo "</td></tr></table>";
?>
