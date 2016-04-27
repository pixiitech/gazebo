<?php $pagename = "eblaster";
$customjs = true;
require "gazebo-header.php";
require $phpmailerdir . "PHPMailerAutoload.php";
?>
<script>
$(document).ready(function(){
	$('#selectAll').click(function(){
	    if (this.checked == true) {
	        $('.recipient').prop('checked', true);
	    }
	    else {
	        $('.recipient').prop('checked', false);
	    }
	});
});
</script>
<?php
/* Show help icon */
echo "<img style='position:absolute' src='{$gazebo_imagedir}help.png' onclick=\"window.open('";
echo pageLink("help", "module={$pagename}");
echo "','help','width=800, height=550, status=yes'); return false;\">";

echo "<h4 style=\"text-align:center\" >Send Email Blast</h4>";

require 'authcheck.php';

//Set up variables
$editingpost = get_post($_GET['postid']);
$commName = fetchSetting('Name', $con);
$lastfirst = fetchSetting("DisplayLastFirst", $con);
$ucase = fetchSetting("DisplayUppercaseNames", $con);
$current_site = site_url(null,null,'login');
$current_site = substr($current_site, strpos($current_site, '//') + 2);
debugText('SITE URL ' . $current_site);


debugText("postid={$_GET['postid']}");
debugText("Tally={$_POST['tally']}");

//Build mail message
$from = fetchSetting('Name', $con); //reply-to will be donotreply@example.com
$replyto = "donotreply@{$current_site}";
$subject = $editingpost->post_title;
$message = wpautop(apply_filters('the_content', $editingpost->post_content), true);
$message = preg_replace('/[Â]/', ' ', $message);
$header = "From: {$from}\r\n";
$header .= "Reply-To: {$replyto}\r\n";  
$header .= "MIME-Version: 1.0\r\n";
//$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$header .= "Content-Type: text/html; charset=UTF-8\r\n";
$host = fetchSetting( 'SMTPHost', $con );
$auth = fetchSetting( 'SMTPAuth', $con );
$port = fetchSetting( 'SMTPPort', $con );
$username = fetchSetting( 'SMTPUsername', $con );
$password = fetchSetting( 'SMTPPassword', $con );
// Additional settings…
//$phpmailer->SMTPSecure = "tls"; // Choose SSL or TLS, if necessary for your server
$fromname = fetchSetting( 'FromName', $con );
$fromaddress = fetchSetting( 'FromAddress', $con );

//Eblast loop - mailer
for ( $i = 1; $i <= $_POST['tally']; $i++ ) {
	if ( !isset( $_POST['selected-' . $i] ) ) {
	    continue;
	}

	$to = $_POST['email-' . $i];

	$mail = new PHPMailer();
	if ( $_SESSION['DebugMode'] == 'on' ) {
	    $mail->SMTPDebug = 2;
	}
	$mail->addAddress($to);
	$mail->isHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $message;

	if ( $host != '' ) {
	    $mail->isSMTP();
    	    $mail->Host = $host;
	    $mail->Port = $port;
	}

	if ( $auth != 'none' ) {
     	    $mail->SMTPAuth = true; // Force it to use Username and Password to authenticate
	    if ( $auth == 'ssl' ) {
		$mail->SMTPSecure = 'ssl';
	    }
	    else if ( $auth == 'tls' ) {
		$mail->SMTPSecure = 'tls';
	    }
	    $mail->Username = $username;
	    $mail->Password = $password;
	}

	if ( $fromname == '' ) {
	    $fromname = $commName;
	}

	if ( $fromaddress == '' ) {
	    $fromaddress = 'donotreply@' . preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);
	}

	$mail->setFrom($fromaddress, $fromname);

	if ( !$mail->send()) {
	    echo "Email to {$to} failed to send. Mailer Error: {$mail->ErrorInfo}<br />";
	}
	else {
	    echo "Email to {$to} sent.<br />";
	}

	debugText( "Host: {$mail->Host}<br />Username: {$mail->Username}<br />Password: {$mail->Password}<br />" );
	debugText( "Secure: {$mail->SMTPSecure}<br />Auth: {$mail->SMTPAuth}<br />Port: {$mail->Port}<br />");

	//Old method using mail()
/*
	$mailresult = mail($to, $subject, $message, $header);

	debugText("Email to {$to}: <br />Subject: {$subject}<br />");
	if ( $mailresult ) {
	    echo "Email sent to {$to}.<br />";
	}
	else {
	    echo "Email to {$to} failed to send.<br />";
	}
*/
}

//Do not display email blast form if mail is already sent
if ( $_POST['submitted'] == 'yes' ) {
	include 'gazebo-footer.php';
	die("<br />");
}

//Build query
$querystring = "SELECT Residents.FirstName, Residents.LastName, Residents.FirstName2, Residents.LastName2, Residents.Email, Residents.Email2, Properties.Unit FROM Residents, Properties WHERE ((Residents.Email <> '') OR (Residents.Email2 <> '')) AND Properties.Residx = Residents.Idx";

//Perform query and display list
debugText( $querystring );
$result = mysql_query($querystring, $con);

echo "<form name='mailer' method='post' action='" . pageLink($pagename, "postid={$_GET['postid']}") . "'>";
if ( $cms == "wp" ) {
    echo "<input type='hidden' name='page_id' value='" . pageID($pagename) . "' />";
    echo "<input type='hidden' name='page' value='{$pagename}' />";
}
echo "<input type='hidden' name='tally' value='0' />";
echo "<input type='hidden' name='submitted' value='yes' />";
echo "<table>";
echo "<thead><tr><th>";
echo "<input type='checkbox' name='selectAll' id='selectAll' checked='checked' />";
echo "</th><th><b>Name</b></th><th><b>Unit #</b></th><th><b>Email</b></th></tr></thead>";
echo "<tbody>";
$tally = 1;
//Pull resident email addresses
while ( $row = mysql_fetch_array($result) ) {
    if (( $row['Email'] != "" ) && ( $row['Email'] != NULL )) {
        echo "<tr><td><input type='checkbox' class='recipient' name='selected-{$tally}' checked='checked' /></td>";
	$name = displayName($row['FirstName'], $row['LastName'], $ucase, $lastfirst);
	echo "<td>{$name}</td>";
	echo "<td>" . $row['Unit'] . "</td>";
	echo "<td><input type='text' size='25' name='email-{$tally}' value='{$row['Email']}' /></td></tr>";
	$tally++;
    }
    if (( $row['Email2'] != "" ) && ( $row['Email2'] != NULL )) {
        echo "<tr><td><input type='checkbox' class='recipient' name='selected-{$tally}' checked='checked' /></td>";
	$name = displayName($row['FirstName2'], $row['LastName2'], $ucase, $lastfirst);
	echo "<td>{$name}</td>";
	echo "<td>" . $row['Unit'] . "</td>";
	echo "<td><input type='text' size='25' name='email-{$tally}' value='{$row['Email2']}' /></td></tr>";
	$tally++;
    }
}
//Pull default recipients from Notification Settings
$defaultemails = fetchSetting('EBlastDefaultRecipients', $con);
while ( strlen($defaultemails) > 0 ) {
    $semipos = strpos($defaultemails, ';');
    $email = substr($defaultemails, 0, $semipos);
    if ($email == false) {
	$email = trim($defaultemails);
	$defaultemails = "";
    }
    else {
	$defaultemails = trim(substr($defaultemails, $semipos + 1));
    }
    echo "<tr><td><input type='checkbox' class='recipient' name='selected-{$tally}' checked='checked' /></td>";
    echo "<td></td><td></td>";
    echo "<td><input type='text' size='25' name='email-{$tally}' value='{$email}' /></td></tr>";
    $tally++;
}
echo "</tbody><tfoot><tr><tf colspan='3'><i>{$tally} total record(s).</i></tf></tr></tfoot></table>";
//Email preview
echo "<table><thead><tr><th>Email preview:</th></tr></thead>";
echo "<tbody>";
echo "<tr><td>From: {$from} ({$replyto})</td></tr>";
echo "<tr><td>Subject: {$subject}</td></tr>";
echo "<tr><td>Message: {$message}</td></tr></table><br />";
echo "<input type='submit' value='Send E-mail Blast' />";
echo "</form>";
echo "<script>document.forms['mailer'].elements['tally'].value = '{$tally}';</script>";

include 'gazebo-footer.php';
?>
