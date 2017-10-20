<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php require 'config.php';
session_start();?>
<html>
<head>
<title><?php echo $commName ?></title>
<?php include "style-gazebo.php" ?>
<script src="jquery-2.1.1.min.js"></script>

</head>
<body>

<?php include "menu-web.php" ?>
<?php include "authcheck-web.php" ?>
<?php include "web-content/index-content.html" ?>
<?php echo "
</body>
</html>";
?>
