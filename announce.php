<?php 
$pagename = "announce";
require 'gazebo-header.php';
?>

<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<script src="jquery-2.1.1.min.js"></script>
<!-- <script src='sorttable.js'></script> -->

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
<script>
function fillInForm(fn, fields) {
    var updateFields = function (FieldArray) {
	for ( var i = 0; i < FieldArray.length; i++ )
	{
	    if (FieldArray[i][0] == 'Icon')
		document.forms['recordinput'].elements[FieldArray[i][1]].checked = true;
	    else if (FieldArray[i][0] == 'Amenity')
	        document.forms['recordinput'].elements['Amenity'].selectedIndex = FieldArray[i][1];
	    else if (FieldArray[i][0] == 'StartHour')
	        document.forms['recordinput'].elements['StartHour'].selectedIndex = FieldArray[i][1];
	    else if (FieldArray[i][0] == 'StartMinute')
	        document.forms['recordinput'].elements['StartMinute'].selectedIndex = (FieldArray[i][1] - FieldArray[i][1] % 15) / 15;
	    else if (FieldArray[i][0] == 'StartAMPM')
	        document.forms['recordinput'].elements['StartAMPM'].selectedIndex = FieldArray[i][1];
	    else if (FieldArray[i][0] == 'EndHour')
	        document.forms['recordinput'].elements['EndHour'].selectedIndex = FieldArray[i][1];
	    else if (FieldArray[i][0] == 'EndMinute')
	        document.forms['recordinput'].elements['EndMinute'].selectedIndex = (FieldArray[i][1] - FieldArray[i][1] % 15) / 15;
	    else if (FieldArray[i][0] == 'EndAMPM')
	        document.forms['recordinput'].elements['EndAMPM'].selectedIndex = FieldArray[i][1];
	    else
	        document.forms['recordinput'].elements[FieldArray[i][0]].value = FieldArray[i][1];
	}
    };
    switch (fn)
    {
	case 1:
  	    document.forms['recordinput'].elements['fnList'].checked = true;
	    fnList();
	    break;
	case 2:
  	    document.forms['recordinput'].elements['fnSearch'].checked = true;
	    fnSearch(function(){updateFields(fields)});
	    break;
	case 3:
  	    document.forms['recordinput'].elements['fnInsert'].checked = true;
	    fnInsert(function(){updateFields(fields)});
	    break;
	case 4:
  	    document.forms['recordinput'].elements['fnUpdate'].checked = true;
	    fnUpdate(function(){updateFields(fields)});
	    break;
	case 5:
  	    document.forms['recordinput'].elements['fnDelete'].checked = true;
	    fnDelete(function(){updateFields(fields)});
	    break;
    }
    return;
}
</script>
<style>
textarea
{
    height: 60px;
    width: 400px;
}
</style>

<?php 
require "authcheck.php";

//Build Icon Lists
echo "<script>var iconlist = [];</script>";
for ( $i = 0; $i < count($iconlist); $i++ )
    echo "<script>iconlist[{$i}] = '{$iconlist[$i]}';</script>";

//Build Amenities Lists
$querystring = "SELECT * FROM Amenities ORDER BY Idx";
$result = mysql_query($querystring, $con);
$amenities = array();
echo "<script>var amenities = [];</script>";
for ( $i = 1; $row = mysql_fetch_array($result); $i++ )
{
    $amenities[$row['Idx']] = $row['Name'];
    echo "<script>amenities[{$i}] = '{$row['Name']}';</script>";
}

//Build Subdivision List
$querystring = "SELECT * FROM Subdivisions ORDER BY Id";
$result = mysql_query($querystring, $con);
$SubdivTypes = array();
echo "<script>var SubdivTypes = [];</script>";
for ( $i = 1; $row = mysql_fetch_array($result); $i++ )
{
    $SubdivTypes[$row['Id']] = $row['Name'];
    echo "<script>SubdivTypes[{$i}] = '{$row['Name']}';</script>";
}
?>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Manage Announcements</h2>

<?php

echo "<br /><form name='recordinput' method='post' action='" . pageLink("announce") . "'><p class='center'>";
echo "<input type='hidden' id='SavedQuery' name='SavedQuery' value=\"{$_POST['SavedQuery']}\" />";
echo "<input id='fnList' type='radio' name='function' value='list' />List&nbsp;&nbsp;";
echo "<input id='fnSearch' type='radio' name='function' value='search' />Search&nbsp;&nbsp;";
echo "<input id='fnInsert' type='radio' name='function' value='insert' />Submit New&nbsp;&nbsp;";
echo "<input id='fnUpdate' type='radio' name='function' value='update' />Update&nbsp;&nbsp;";
echo "<input id='fnDelete' type='radio' name='function' value='delete' />Delete&nbsp;&nbsp;";

echo "
<table class='criteria'><tbody>
<tr class='formfields Search Insert Update Delete'><td><input id='Idx' name='Idx' type='hidden' value=''/>
<span class='formfields Delete' hidden='hidden'>Are you sure you want to delete event: </span>
<span class='formfields Search Insert Update'>Subject </span>
<span class='formfields Search Insert Update Delete'><input id='Text' type='text' size='25' name='Text' /></span>
<span class='formfields Delete' hidden='hidden'>?</span>
</td></tr>
<tr class='formfields Insert Update'><td><strong>Description</strong><br />
<textarea id='Description' name='Description' rows='2' cols='100' style='height:100%; width:100%'></textarea></td></tr>
<tr class='formfields Search Insert Update'><td>From: <input id='StartMonth' type='text' size='2' name='StartMonth' />/
        <input id='StartDay' type='text' size='2' name='StartDay' />/
        <input id='StartYear' type='text' size='2' name='StartYear' />&nbsp;&nbsp;&nbsp;
        <select name='StartHour'>";
        for ( $i = 0; $i < 24; $i++ )
	{
	    if (( $_SESSION['24HrTime'] != 'on' ) && ( $i >= 12 ) ) {
		break;
	    }
	    if (( $_SESSION['24HrTime'] != 'on' ) && ( $i == 0 )) {
		 $j = 12;
	    }
	    else {
		$j = $i;
	    }
            echo "<option value={$i}>{$j}</option>";
	}
        echo "</select>:<select name='StartMinute'>";
        for ( $i = 0; $i < 4; $i++ )
            echo "<option value='" . $i * 15 . "'>" . padInt2($i * 15) . "</option>";
        echo "</select>&nbsp;";
	echo "<select name='StartAMPM'"; 
	if ( $_SESSION['24HrTime'] == 'on' ) {
	    echo "hidden='hidden' ";
	}
	echo ">";
	echo "<option value='AM'>AM</option><option value='PM'>PM</option>";
	echo "</select>";

echo " To: <input id='EndMonth' type='text' size='2' name='EndMonth' />/
        <input id='EndDay' type='text' size='2' name='EndDay' />/
        <input id='EndYear' type='text' size='2' name='EndYear' />&nbsp;&nbsp;&nbsp;
        <select name='EndHour'>";
        for ( $i = 0; $i < 24; $i++ )
	{
	    if (( $_SESSION['24HrTime'] != 'on' ) && ( $i >= 12 ) ) {
		break;
	    }
	    if (( $_SESSION['24HrTime'] != 'on' ) && ( $i == 0 )) {
		 $j = 12;
	    }
	    else {
		$j = $i;
	    }
            echo "<option value={$i}>{$j}</option>";
	}
        echo "</select>:<select name='EndMinute'>";
        for ( $i = 0; $i < 4; $i++ )
            echo "<option value='" . $i * 15 . "'>" . padInt2($i * 15) . "</option>";
        echo "</select>";
	echo "<select name='EndAMPM'"; 
	if ( $_SESSION['24HrTime'] == 'on' ) {
	    echo "hidden='hidden' ";
	}
	echo ">";
	echo "<option value='AM'>AM</option><option value='PM'>PM</option>";
	echo "</select>";
	echo "</td></tr>";
echo "<tr class='formfields Insert Update'><td>Select Icon: ";
        for ( $i = 0; $i < count($iconlist); $i++ )
        {
            echo "<input type='radio' name='Icon' value='{$iconlist[$i]}' id='{$iconlist[$i]}'";
	    echo " /><img src='" . $gazebo_imagedir . $iconlist[$i] . "' style='height:40px; width:40px' />";
        }
        echo "</td></tr>";

	echo "<tr class='formfields Insert Update'><td>Reserved Amenity:<select name='Amenity'><option value='None'>None</option>";
        for ( $i = 1; $i <= count($amenities); $i++ )
        {
            echo "<option value='{$i}'";
            echo ">" . $amenities[$i] . "</option>";
        }
echo "</select></td></tr>";
//Display mailer
echo "<tr class='formfields Insert'><td>Email to: <input id='EmailStaff' name='EmailStaff' type='checkbox' checked=true />Staff  ";
echo "                  <input id='EmailSecurity' name='EmailSecurity' type='checkbox' />Security  ";
echo "                  <input id='EmailBoard' name='EmailBoard' type='checkbox' />Board Members  ";
echo "		   <input id='EmailSubdiv' name='EmailSubdiv' type='checkbox' />  ";
echo "Residents of <select name=\"Subdivision\"><option value=\"All\">All</option>";
for ( $i = 1; $i <= count($SubdivTypes); $i++ ) {
    echo "<option value='{$i}'>{$SubdivTypes[$i]}</option>";
}
echo "</select>";
echo "</td></tr>";
echo "</tbody></table>
<p class='center'><input type=\"submit\" value=\"Post\" id='submitbutton' /> <input type=\"reset\" value=\"Clear\" /></p>";
echo "</p></form>";

if ( !isset($_POST["function"]) ) {
    checkRadio('fnList');
    $_POST['function'] = "list";
}

switch ($_POST["function"])
{
  case "insert":
	//Save SQL Record
	$curtime = getdate();
	$cur_sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];
	if (( $_POST['StartAMPM'] == 'PM' ) && ( $_SESSION['24HrTime'] != 'on' )) {
	    $_POST['StartHour'] += 12;
	}
	if (( $_POST['EndAMPM'] == 'PM' ) && ( $_SESSION['24HrTime'] != 'on' )) {
	    $_POST['EndHour'] += 12;
	}
	$begin_sqltime = assembleDateTime($_POST['StartMonth'], $_POST['StartDay'], $_POST['StartYear'],
					  $_POST['StartHour'], $_POST['StartMinute']);
	$end_sqltime = assembleDateTime($_POST['EndMonth'], $_POST['EndDay'], $_POST['EndYear'],
					  $_POST['EndHour'], $_POST['EndMinute']);
	$_POST['Description'] = replaceDoubleQuotes(mysql_real_escape_string($_POST['Description']));
	$_POST['Subject'] = replaceDoubleQuotes(mysql_real_escape_string($_POST['Subject']));
	$querystring = "INSERT INTO Events (Timecreated, CreatedBy, Text, Icon, Description, Amenity, StartTime, EndTime) VALUES
			('{$sqltime}', '{$_SESSION['Username']}', '{$_POST['Text']}', '{$_POST['Icon']}', '{$_POST['Description']}',
			 '{$_POST['Amenity']}', '{$begin_sqltime}', '{$end_sqltime}')";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Announcement saved.<br />";
	else
	{
		errorMessage("Announcement failed to save.<br />", 3);
	 	break;
        }
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;

  case "update":
	//Save SQL Record
	$curtime = getdate();
	$cur_sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];
	if (( $_POST['StartAMPM'] == 'PM' ) && ( $_SESSION['24HrTime'] != 'on' )) {
	    $_POST['StartHour'] += 12;
	}
	if (( $_POST['EndAMPM'] == 'PM' ) && ( $_SESSION['24HrTime'] != 'on' )) {
	    $_POST['EndHour'] += 12;
	}
	$begin_sqltime = assembleDateTime($_POST['StartMonth'], $_POST['StartDay'], $_POST['StartYear'],
					  $_POST['StartHour'], $_POST['StartMinute']);
	$end_sqltime = assembleDateTime($_POST['EndMonth'], $_POST['EndDay'], $_POST['EndYear'],
					  $_POST['EndHour'], $_POST['EndMinute']);
	$_POST['Description'] = replaceDoubleQuotes(mysql_real_escape_string($_POST['Description']));
	$_POST['Subject'] = replaceDoubleQuotes(mysql_real_escape_string($_POST['Subject']));
	$querystring = "UPDATE Events SET Text='{$_POST['Text']}', Icon='{$_POST['Icon']}', Description='{$_POST['Description']}', Amenity='{$_POST['Amenity']}',
			 StartTime='{$begin_sqltime}', EndTime='{$end_sqltime}' WHERE Idx='{$_POST['Idx']}'";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Announcement saved.<br />";
	else
	{
		errorMessage("Announcement failed to save.<br />", 3);
	 	break;
        }
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;

  case "delete":
	$querystring = "DELETE FROM Events WHERE Idx={$_POST['Idx']}";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Event #{$_POST["Idx"]} deleted.<br />";
	else
		echo "Event #{$_POST["Idx"]} failed to save.<br />";
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;
  default:
	break;
}

if ( $_POST['function'] == 'search' ) {
	echo "<h4 style='text-align:center'>Search Results:</h4>";
}

if (( $_POST['function'] == 'list' ) || ( $_POST['function'] == 'search' )) {
	echo "<table class='result sortable tablesorter' border=4>";
	echo "<thead><tr><th>Graphic</th><th>Subject</th><th>Start Time</th><th>End Time</th><th>Created By</th><th>Amenity</th><th>Delete</th></tr></thead><tbody>";
	$querystring = "SELECT * FROM Events WHERE 1=1";
	if ( $_POST["function"] == "search" )
	{
	    if (( $_POST['StartMonth'] != "MM" ) && ( $_POST['StartDay'] != "DD" )
		 && ( $_POST['StartYear'] != "YY" ) && ( $_POST['StartMonth'] != "" )) {
		$querystring .= " AND StartTime >= '";
		$querystring .= assembleDate( $_POST['StartMonth'], $_POST['StartDay'], $_POST['StartYear'] );
		$querystring .= "'";
	    }
	    if (( $_POST['EndMonth'] != "MM" ) && ( $_POST['EndDay'] != "DD" )
		 && ( $_POST['EndYear'] != "YY" ) && ( $_POST['EndMonth'] != "" )) {
		$querystring .= " AND StartTime <= '";
		$querystring .= assembleDate( $_POST['EndMonth'], $_POST['EndDay'], $_POST['EndYear'] );
		$querystring .= "'";
	    }
	    if ( $_POST['CreatedBy'] != "" ) {
		$lCreatedBy = strtolower($_POST['CreatedBy']);
		$querystring .= " AND LOWER(CreatedBy) LIKE '%{$lCreatedBy}%'";
	    }
	    if ( $_POST['Text'] != "" ) {
		$lText = strtolower($_POST['Text']);
		$querystring .= " AND LOWER(Text) LIKE '%{$lText}%'";
	    }
	}
	$querystring .= " ORDER BY StartTime DESC";
	debugText("Original Query:" . $querystring);
	if ( $useSavedQuery == 'yes' ) {
	    $querystring = stripslashes($_POST['SavedQuery']);
	    debugText("Using Saved Query:" . $querystring);
	}
	echo "<script>document.forms['recordinput'].elements['SavedQuery'].value = \"{$querystring}\";</script>";
	$result = mysql_query($querystring, $con); 
	$results=0;
	while ( $row = mysql_fetch_array( $result ) )
	{
	    $startTime = parseTime($row['StartTime']);
	    $endTime = parseTime($row['EndTime']);
	    if ( $_SESSION['24HrTime'] != 'on' ) {
		$startAMPM = floor($startTime['Hour'] / 12);
		$startTime['Hour'] = $startTime['Hour'] % 12;
		if ( $startTime['Hour'] == 0 ) {
		    $startTime['Hour'] = '12';
		}
		$endAMPM = floor($endTime['Hour'] / 12);
		$endTime['Hour'] = $endTime['Hour'] % 12;
		if ( $endTime['Hour'] == 0 ) {
		    $endTime['Hour'] = '12';
		}

	    }
            echo "<tr><td>";
	    if ( $row['Icon'] != "" )
		echo "<img src='{$gazebo_imagedir}{$row['Icon']}' title='Event #{$row['Idx']}' style='height:40px; width:40px' />";
	    echo "</td>";
            echo "<td><a href='#top' onclick=\"fillInForm(4, [['Idx', '{$row['Idx']}'], ['Text', '{$row['Text']}'],
			['Description', '{$row['Description']}'], 
			['StartYear', '{$startTime['Year']}'],
			['StartMonth', '{$startTime['Month']}'], ['StartDay', '{$startTime['Day']}'],
			['StartHour', '{$startTime['Hour']}'], ['StartMinute', '{$startTime['Minute']}'],
			['EndYear', '{$endTime['Year']}'],
			['EndMonth', '{$endTime['Month']}'], ['EndDay', '{$endTime['Day']}'],

			['EndHour', '{$endTime['Hour']}'], ['EndMinute', '{$endTime['Minute']}'], ";
	    if ( $_SESSION['24HrTime'] != 'on' ) {
		echo "['StartAMPM', '{$startAMPM}'], ['EndAMPM', '{$endAMPM}'], ";
	    }
	    echo "['Icon', '{$row['Icon']}'], ['Amenity', '{$row['Amenity']}']]);\">{$row['Text']}</a></td>";
	    $startTime = parseTime($row['StartTime']);
	    $endTime = parseTime($row['EndTime']);
	    echo "<td>" . $startTime['Month'] . "/" . $startTime['Day'] . "/";
	    echo $startTime['Year'] . " " . displayTime($startTime['Hour'], $startTime['Minute']) . "</td>";
	    echo "<td>" . $endTime['Month'] . "/" . $endTime['Day'] . "/";
	    echo $endTime['Year'] . " " . displayTime($endTime['Hour'], $endTime['Minute']) . "</td>";
            echo "<td>{$row['CreatedBy']}</td>";
            echo "<td>{$amenities[$row['Amenity']]}</td>";
            echo "<td><a href='#top' onclick=\"fillInForm(5, [['Idx', '{$row['Idx']}'], ['Text', '{$row['Text']}']]);\">X</a></tr>";
	    $results++;
	}
	echo "</tbody></table>";
	if ( $results == 0 )
	  echo "<br /><br /><i>No records found.</i><br />";
	if ( $results == 1 )
	  echo "<br /><br /><i>One record found.</i><br />";
	if ( $results > 1 )
	  echo "<br /><br /><i>{$results} records found.</i><br />";
	if ( isset($_POST['Idx']) )
	    echo "<script>document.forms['recordinput'].elements['Idx'].value = '{$_POST['Idx']}';</script>";
	if ( isset($_POST['Unit']) )
	    echo "<script>document.forms['recordinput'].elements['Text'].value = '{$_POST['Text']}';</script>";
	if ( isset($_POST['Tag']) )
	    echo "<script>document.forms['recordinput'].elements['CreatedBy'].value = '{$_POST['CreatedBy']}';</script>";
	if ( isset($_POST['StartMonth']) )
	    echo "<script>document.forms['recordinput'].elements['StartMonth'].value = '{$_POST['StartMonth']}';</script>";
	if ( isset($_POST['StartDay']) )
	    echo "<script>document.forms['recordinput'].elements['StartDay'].value = '{$_POST['StartDay']}';</script>";
	if ( isset($_POST['StartYear']) )
	    echo "<script>document.forms['recordinput'].elements['StartYear'].value = '{$_POST['StartYear']}';</script>";
	if ( isset($_POST['EndMonth']) )
	    echo "<script>document.forms['recordinput'].elements['EndMonth'].value = '{$_POST['EndMonth']}';</script>";
	if ( isset($_POST['EndDay']) )
	    echo "<script>document.forms['recordinput'].elements['EndDay'].value = '{$_POST['EndDay']}';</script>";
	if ( isset($_POST['EndYear']) )
	    echo "<script>document.forms['recordinput'].elements['EndYear'].value = '{$_POST['EndYear']}';</script>";
}

include 'gazebo-footer.php';
?>
