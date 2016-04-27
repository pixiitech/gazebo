<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php require 'config.php'; 
require 'library.php';
session_start();?>
<html>
<head>
<title>template</title>
<?php $pagename = "template"; ?>
<?php if (!isset($cms)){ include 'style-gazebo.php'; } ?>
</head>
<body>

<?php $con = connect_mysql_DB(); ?>

<?php include "menu-web.php" ?>
<?php include "authcheck-web.php" ?>
<?php include "template-content.html" ?>

</body>
</html>

