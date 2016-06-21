<?php
require "config.php";

global $pagename, $resourcetype, $resourcelevel;

if ( !isset($cms) ) // Standalone only
{
    /* User is authenticated by login.php, boot unauthorized users */
    if ( !isset($_SESSION['Level']) )
        die ("<p>This page requires a login.</p> <i><a href='login.php'>Return to Login Page</a></i><br />");
    
    if ($_SESSION['Level'] == $level_disabled)
    {
        mysqli_close($con);
        die ("<p>Your account has been deactivated. Contact your system administrator.</p> <i><a href='login.php'>Return to Login Page</a></i><br />");
    }

    if ($_SESSION['Level'] == $level_logout)
    {
        mysqli_close($con);
        die ("<p>Your account has been logged out.</p> <i><a href='login.php'>Return to Login Page</a></i><br />");
    }

    if ($_SESSION['Expiration'] < time())
    {
        mysqli_close($con);
        die ("<p>You session has expired.</p> <i><a href='login.php'>Return to Login Page</a></i><br />");
    }

    if ( !isset($_GET['quiet']) && !isset($silentlogin)) {
        echo "<p style='text-align:center'>Logged in: " . $_SESSION['Username'] . "&nbsp;&nbsp;&nbsp;&nbsp;<a href='login.php?logout=yes'><i>Logout</i></a>";

        if ($_SESSION['Level'] >= $level_staff) {
            echo " - <a href='home.php'><i>Admin Home</i></a> - <a href='index.php'><i>Public/Resident Home</i></a>";
	}
        echo "</p>";
    }
}

/* Check page security level */
$auth = false;
for ( $i = 0; $i < count($modules); $i++ )
    if (( $modules[$i][0] == $pagename ) && ( $_SESSION['Level'] >= $modules[$i][3] ))
	$auth = true;

/* Check resource security level (minimum level provided by public var) */
if ( !isset($_SESSION['Level']) ) {
    $level = 0;
} else {
    $level = $_SESSION['Level'];
}

if ( $resourcetype == 'download' ) {
    if ( $resourcelevel <= $level ) {
	$auth = true;
    }
} 
if (!$auth) 
    die ("You do not have authorization to view this page.<br />");
?>
