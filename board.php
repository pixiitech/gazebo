<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
$pagename = 'board';
require 'config.php';
session_start();
?>
<html>
<head>
<title>Board</title>
<?php include "style-gazebo.php" ?>
</head>
<body>

<?php include "menu-web.php" ?>
<?php include "authcheck.php" ?>
<?php include "web-content/board-content.html" ?>

</body>
</html>

