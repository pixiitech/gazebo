<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php require 'config.php'; 
session_start();?>
<html>
<head>
<title>Sea Monarch Photos</title>
<?php if (!isset($cms)){ include 'style-gazebo.php'; } ?>
<?php include "menu-web.php" ?>
<?php include "authcheck-web.php" ?>
<script src="jquery-2.1.1.min.js"></script>
<script>
$(document).ready(function(){
    $('.photos').click(function(){
	$('.viewer').show();
	$('.viewer').attr("src", $(this).attr("src"));
	$('.viewer').animate({height:'720px', width:'1050px',left:'20%', top:'20%'},400);
    });
    $('.viewer').click(function(){
	$('.viewer').animate({height:'1px', width:'1px',left:'50%', top:'50%'},400,'linear',function(){
	    $('.viewer').hide();});
    });
});

</script>
</head><body>
<?php include "web-content/photos-content.html" ?>
</body></html>
