<style>
span.formentry
{
    font-family: courier;
    font-weight: bold;
}
</style>
<?php
//Formhandler.php - library for printing forms and handling form post data

function do_processform($formname, $dbconnector) {
    // Load form from database
    include_once "style-gazebo.php";
    $querystring = "SELECT * FROM Forms WHERE Title = '{$formname}'";
    $result = mysqli_query($dbconnector, $querystring);
    $row = mysqli_fetch_array($result);
    $formtext = $row['Text'];
    $formheader = "<form name='gazeboform' method='post' action='" . htmlspecialchars($_SERVER[REQUEST_URI]) . "'>";
    $formheader .= "<input type='hidden' name='submitted' value='yes' />";
    $formheader .= "<input type='hidden' name='recipient' value='{$row['Email']}' />";
    $formheader .= "<input type='hidden' name='title' value='{$row['Title']}' />";
    $output = '';

    // Parse static tags

    $start2 = 0;
    $buffer = '';
    while ( $loc3 = strpos($formtext, "{", $start2) ) {
	$buffer .= substr($formtext, $start2, $loc3 - $start2);
	$loc3++;
	$loc4 = strpos($formtext, ":", $loc3);
	$dbname = substr($formtext, $loc3, $loc4 - $loc3);
	$loc4++;
	$loc5 = strpos($formtext, "}", $loc4);
	$fieldname = substr($formtext, $loc4, $loc5 - $loc4);
	if ( $dbname == 'Residents' ) {
		$querystring2 = "SELECT {$fieldname} FROM Residents WHERE Idx = {$_SESSION['Residx']}";
	}
	else if ($dbname == 'Properties' ) {
		$querystring2 = "SELECT {$fieldname} FROM Properties WHERE Residx = {$_SESSION['Residx']}";
	}
	else if ( $dbname == 'Date' ) {
	    if ( $fieldname == 'MM/DD/YYYY') {
	        $buffer .= "<span class='formentry'>" . date("m/d/Y") . "</span>";
	    }
	    if ( $fieldname == 'MM/DD/YY') {
	        $buffer .= "<span class='formentry'>" . date("m/d/Y") . "</span>";
	    }
	    if ( $fieldname == 'Month DDth, YYYY') {
	        $buffer .= "<span class='formentry'>" . date("F jS, Y") . "</span>";
	    }
	}
	if ( isset($querystring2) ) {
	    $result2 = mysqli_query($dbconnector, $querystring2);
	    $row2 = mysqli_fetch_array($result2);
	    $buffer .= "<span class='formentry'>" . $row2[$fieldname] . "</span>";
	}
	$start2 = $loc5 + 1;
    }
    $buffer .= substr($formtext, $start2);
    $formtext = $buffer;

    $start2 = 0;

    // Parse form submission, prepare email to send
    if ( isset( $_POST['submitted'] ) && ( $_POST['submitted'] == 'yes' ) ) {
        while ( $loc2 = strpos($formtext, "<input", $start2) )
	{
	    // Output form from start location
	    $output .= substr($formtext, $start2, $loc2 - $start2);
	    // Parse input tag
	    $typepos = strpos($formtext, "type=", $start2);
	    $quotechar = substr($formtext, $typepos + 5, 1);
	    $endcharpos = strpos($formtext, $quotechar, $typepos + 6);
	    $fieldtype = substr($formtext, $typepos + 6, $endcharpos - ($typepos + 6));		

	    $namepos = strpos($formtext, "name=", $start2);
	    $quotechar = substr($formtext, $namepos + 5, 1);
	    $endcharpos = strpos($formtext, $quotechar, $namepos + 6);
	    $fieldname = substr($formtext, $namepos + 6, $endcharpos - ($namepos + 6));

	    $valuepos = strpos($formtext, "value=", $start2);
	    $quotechar = substr($formtext, $valuepos + 6, 1);
	    $endcharpos = strpos($formtext, $quotechar, $valuepos + 7);
	    $fieldvalue = substr($formtext, $valuepos + 7, $endcharpos - ($valuepos + 7));

	    if ( $fieldtype == 'text' ) {
	        $output .= " <span class='formentry'>&nbsp;{$_POST[$fieldname]}&nbsp;</span>";
	    }
	    else if ( $fieldtype == 'radio' ) {
		if ( $_POST[$fieldname] == $fieldvalue ) {
		    $output .= "&nbsp;&nbsp;<span class='formentry'> X</span>";
		}
		else {
		    $output .= "&nbsp;&nbsp;o";
		}
	    }
	    else if ( $fieldtype == 'checkbox' ) {
		if ( $_POST[$fieldname] == 'on' ) {
		    $output .= "&nbsp;&nbsp;<span class='formentry'>  X</span>";
		}
	    }
	    $start2 = strpos($formtext, ">", $loc2) + 1;
        }
        // Display remaining form
        $output .= substr($formtext, $start2);
	
	// Send E-mail
	//Build mail message
	$current_site = site_url(null,null,'login');
	$current_site = substr($current_site, strpos($current_site, '//') + 2);
	$from = fetchSetting('Name', $dbconnector); //reply-to will be donotreply@example.com
	$to = $_POST['recipient'];
	$replyto = "donotreply@{$current_site}";
	$subject = $_POST['title'];
	$message = wpautop(apply_filters('the_content', $output), true);
	$message = preg_replace('/[Â]/', ' ', $message);
	$header = "From: {$from}\r\n";
	$header .= "Reply-To: {$replyto}\r\n";  
	$header .= "MIME-Version: 1.0\r\n";
	//$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$header .= "Content-Type: text/html; charset=UTF-8\r\n";
	$mailresult = mail($to, $subject, $message, $header);

	if ( $mailresult ) {
	    echo "Email sent to {$to}.<br />";
	}
	else {
	    echo "Email to {$to} failed to send.<br />";
	}
    }
    else {
	$output .= $formtext;
	$output .= "<p class='center'><input type='submit' value='Submit Form' /></p>";
    }
    $formfooter = "</form>";
    return $formheader . $output . $formfooter;
}

?>
