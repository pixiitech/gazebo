<?php
require "config.php";

/* Public page, simply show login if available */

if (isset($_SESSION['Username']))
    echo "<p style='text-align:center'>Logged in: " . $_SESSION['Username'] . "&nbsp;&nbsp;&nbsp;&nbsp;<a href='login.php?logout=yes'><i>Logout</i></a>";

if ($_SESSION['Level'] >= $level_staff)
    echo " - <a href='home.php'><i>Admin Home</i></a> - <a href='index.php'><i>Public/Resident Home</i></a>";
echo "</p>";
?>
