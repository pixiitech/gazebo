<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php require 'config.php'; 
require 'library.php';
session_start();?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="author" content="Gregory Hedrick" />
<?php $pagename = "formdocs"; ?>
<?php if (!isset($cms)){ include 'style-gazebo.php'; } ?>
<?php
//Generate image list
    $file=fopen("images/image_list.js", "w+") or die("Unable to open file <i>image_list.js</i> for writing.<br />");
    fwrite($file, "var tinyMCEImageList = new Array(\n\r");
    $imgs = glob($community_imagedir . "*");
    for ( $i = 0; $i < count($imgs); $i++ )
    {
	$extpos = strpos($imgs[$i], ".");
	$nopath = substr($imgs[$i], strlen($community_imagedir)); 
	fwrite($file, "[\"{$nopath}\", \"{$imgs[$i]}\"]");
	if ( $i < count($imgs) - 1 )
	    fwrite($file, ",");
	fwrite($file, "\n\r");
    }
    fwrite($file, ");\n\r");
    fclose($file);


?>
<title>
Print Document
</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- TinyMCE -->
<script type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,autosave",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,print,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "hr,removeformat,visualaid,|,charmap,iespell,media,advhr,|,ltr,rtl,|,nonbreaking",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "tinymce/examples/css/word.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "tinymce/examples/lists/template_list.js",
		external_link_list_url : "tinymce/examples/lists/link_list.js",
		external_image_list_url : "images/image_list.js",
		media_external_list_url : "tinymce/examples/lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});

</script>
<!-- /TinyMCE -->

</head>
<body>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Print Document</h2>

<?php
require 'authcheck.php';

/* Connect to SQL Server */
$con = connect_gazebo_DB();

echo "<br /><h4 style='text-align:center'>Select Violation Letter";
if ( isset($_GET['violationidx']) )
    echo " - Violation #" . $_GET['violationidx'];
echo "</h4>";
echo "<div style='text-align:center'>";
if ( isset($_GET['updateStatus']) && ($_GET['updateStatus'] == 'Yes'))
{
    $querystring = "SELECT ActionLog, ActionStatus FROM Violations WHERE Idx = {$_GET['violationidx']}";
    debugText($querystring);
    $result = mysql_query($querystring, $con);
    $row = mysql_fetch_array($result);
    $newstatus = $row['ActionStatus'] + 1;
    $datetime = date('m/d/y G:i');
    $newlog = $row['ActionLog'] . "User " . $_SESSION['Username'] . " printed letter: " . $_GET['letter'] . " on " . $datetime . "\n\r";
    $newlog = mysql_real_escape_string($newlog);
    $querystring = "UPDATE Violations SET ActionStatus = {$newstatus}, ActionLog = '{$newlog}' WHERE Idx={$_GET['violationidx']}";
    debugText($querystring);
    $result = mysql_query($querystring, $con);
    if ($result)
	echo "Violation #" . $_GET['violationidx'] . " action status updated.<br />";
    else
	echo "Violation #" . $_GET['violationidx'] . " action status failed to update.<br />";
}
echo "<form name='documentSelect' id='documentSelect' method='get' action='" . pageLink("formdocs") . "'>
<input name='violationidx' type='hidden' value='{$_GET['violationidx']}' />
<input name='letter' type='hidden' value='{$_GET['letter']}' />
<input name='unitidx' type='hidden' value='{$_GET['unitidx']}' />
<input name='updateStatus' type='hidden' value='' />
<table><tbody><tr><td>
<select name='filelist' size='12'>";
$files = glob($formdir . "*.html");

for ( $i = 0; $i < count($files); $i++ )
{
    $extpos = strpos($files[$i], ".html");
    $noext = substr($files[$i], strlen($formdir), $extpos - strlen($formdir)); 
    echo "<option value='{$noext}'>{$noext}</option>";
}

echo "</select><br /><input type='submit' name='create' value='Create Letter' />
<br /></td><td>";

if ( isset($_GET['filelist']) )
    $_GET['letter'] = $_GET['filelist'];
if ( isset($_GET['letter']) )
{
	//Retrieve Unit number and Resident index
	$querystring = "SELECT Residx FROM Properties WHERE PIUnit={$_GET['unitidx']}";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	$row = mysql_fetch_array($result);
	$residx = $row['Residx'];

	$file=fopen($formdir . $_GET['letter'] . ".html", "r") or die("Unable to open file " . $_GET['letter'] . "html");
	$buffer = "";

	for ( $i=0; !feof($file); $i++ )
	{
	    $text = fgets($file);
	    while ( strpos($text, '{') != false )
	    {
		$pos = strpos($text, '{');
		if ( strpos($text, 'Date:MM/DD/YYYY') == $pos + 1 )
		{
		    $endpos = strpos($text, '}');
		    $pretext = substr($text, 0, $pos);
		    $posttext = substr($text, $endpos + 1);
		    $text = $pretext . date("m/d/Y") . $posttext;
		}
		if ( strpos($text, 'Date:MM/DD/YY') == $pos + 1 )
		{
		    $endpos = strpos($text, '}');
		    $pretext = substr($text, 0, $pos);
		    $posttext = substr($text, $endpos + 1);
		    $text = $pretext . date("m/d/y") . $posttext;
		}
		if ( strpos($text, 'Date:Month DDth, YYYY') == $pos + 1 )
		{
		    $endpos = strpos($text, '}');
		    $pretext = substr($text, 0, $pos);
		    $posttext = substr($text, $endpos + 1);
		    $text = $pretext . date("F jS, Y") . $posttext;
		}
		if ( strpos($text, 'Properties') == $pos + 1 )
		{
		    $querystring = "SELECT * FROM Properties WHERE PIUnit='{$_GET['unitidx']}'";
		    debugText($querystring);
		    $result = mysql_query($querystring, $con);
		    $row = mysql_fetch_array($result);
		    $endpos = strpos($text, '}');
		    $field = substr($text, $pos + 12, $endpos - $pos - 12);
		    if ( $field == 'Subdivision' )
			$val = fetchSubdivision($row['Subdivision'], $con);
		    else
		        $val = $row[$field];
		    $pretext = substr($text, 0, $pos);
		    $posttext = substr($text, $endpos + 1);
		    $text = $pretext . $val . $posttext;
		}
		else if ( strpos($text, 'Residents') == $pos + 1 )
		{
		    $querystring = "SELECT * FROM Residents WHERE Idx='{$residx}'";
		    debugText($querystring);
		    $result = mysql_query($querystring, $con);
		    $row = mysql_fetch_array($result);
		    $endpos = strpos($text, '}');
		    $field = substr($text, $pos + 11, $endpos - $pos - 11);
		    $val = $row[$field];
		    $pretext = substr($text, 0, $pos);
		    $posttext = substr($text, $endpos + 1);
		    $text = $pretext . $val . $posttext;
		} 
		else if ( strpos($text, 'Violations') == $pos + 1 )
		{
		    $querystring = "SELECT * FROM Violations WHERE Idx='{$_GET['violationidx']}'";
		    debugText($querystring);
		    $result = mysql_query($querystring, $con);
		    $row = mysql_fetch_array($result);
		    $endpos = strpos($text, '}');
		    $field = substr($text, $pos + 12, $endpos - $pos - 12);
		    $val = $row[$field];
		    $pretext = substr($text, 0, $pos);
		    $posttext = substr($text, $endpos + 1);
		    $text = $pretext . $val . $posttext;
		} 
		else if ( strpos($text, 'CommunityInfo') == $pos + 1 )
		{
		    $querystring = "SELECT * FROM CommunityInfo WHERE Idx=0";
		    debugText($querystring);
		    $result = mysql_query($querystring, $con);
		    $row = mysql_fetch_array($result);
		    $endpos = strpos($text, '}');
		    $field = substr($text, $pos + 15, $endpos - $pos - 15);
		    $val = $row[$field];
		    $pretext = substr($text, 0, $pos);
		    $posttext = substr($text, $endpos + 1);
		    $text = $pretext . $val . $posttext;
		} 

	    }
	    $buffer .= $text;
	}
	fclose($file);
}

echo "";
echo "<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
	<textarea id='elm1' name='elm1' rows='15' cols='80' style='width: 80%' style='text-align:center'>
	{$buffer}
	</textarea></td></tr><tr><td></td><td><input type='submit' value='Update Action Status' 
				onClick='document.forms[\"documentSelect\"].elements[\"updateStatus\"].value = \"Yes\";' />
				 <input type='submit' value='Print Only' /></td></tr></tbody></table>
	<br /></form></div>";

?>


<?php include 'footer.php'; ?>

</body>
</html>

