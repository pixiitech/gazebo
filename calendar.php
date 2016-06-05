<?php 
$pagename = "calendar";
require 'gazebo-header.php'; 
// include 'style-calendar.php';
?>

<?php
if ( isset( $_SESSION['Level'] ) && ( $_SESSION['Level'] >= $editlevel ) )
    include 'menu.php';
else if ( !isset($cms) )
    include 'menu-web.php'; 

if ( $_SESSION['Level'] >= $editlevel )
	echo "<h2 style='text-align:center'>Calendar Management</h2>";
else
	echo "<h2 style='text-align:center'>Event Calendar</h2>";	

require 'authcheck.php';

/* Declarations */

if (!isset($_POST["SelectMonth"]))
{
   $_POST["SelectMonth"] = date("m");
   $_POST["SelectYear"] = date("y");
   $_POST["SelectDay"] = date("d");
}
$dayonestring = strtotime($_POST["SelectMonth"] . '/01/' . $_POST["SelectYear"]);
$datestring = strtotime($_POST["SelectMonth"] . '/'. $_POST["SelectDay"] .'/' . $_POST["SelectYear"]);
$firstdayofthemonth = date("w", $dayonestring);
$day = NULL;
$lastdayofthemonth = daysInMonth($_POST["SelectMonth"], $_POST["SelectYear"]);
$month = date("m", $dayonestring);
$monthname = date("F", $dayonestring);

/* Create, update and delete calendar events */
if (isset($_POST["function"]))
{
    $_POST['Text'] = replaceDoubleQuotes(mysqli_real_escape_string($con, $_POST['Text']));
    $_POST['Description'] = replaceDoubleQuotes(mysqli_real_escape_string($con, $_POST['Description']));
    $curtime = getdate();
    $sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];
    if ( $_SESSION['24HrTime'] != 'on' ) {
	$_POST['StartHour'] = $_POST['StartHour'] + 12 * AMPMBool($_POST['StartAMPM']);
	$_POST['EndHour'] = $_POST['EndHour'] + 12 * AMPMBool($_POST['EndAMPM']);
    }
    $sqlstarttime = assembleDateTime($_POST["SelectMonth"], $_POST["SelectDay"], $_POST["SelectYear"] % 100,
				 $_POST['StartHour'], $_POST['StartMinute']);
    $sqlendtime = assembleDateTime($_POST["SelectMonth"], $_POST["SelectDay"], $_POST["SelectYear"] % 100,
				 $_POST['EndHour'], $_POST['EndMinute']);
    switch ($_POST["function"])
    {
	case 'new':
	    $querystring = "INSERT INTO Events (TimeCreated, CreatedBy, Text, Icon, StartTime, EndTime, Description, Amenity) VALUES ";
	    $querystring .= "('{$sqltime}', '{$_SESSION['Username']}', '{$_POST['Text']}', '{$_POST['Icon']}', ";
	    $querystring .= "'{$sqlstarttime}', '{$sqlendtime}', '{$_POST['Description']}', '{$_POST['Amenity']}')";
	    $result = mysqli_query($con, $querystring);
	    debugText($querystring);
	    if ($result)
	        echo "Event created.";
	    else
		echo "Event failed to create.";
	    break;
	case 'update':
	    $querystring = "UPDATE Events SET Text='{$_POST['Text']}', Description='{$_POST['Description']}', ";
	    $querystring .= "Icon='{$_POST['Icon']}', Amenity='{$_POST['Amenity']}', StartTime='{$sqlstarttime}', ";
	    $querystring .= "EndTime='{$sqlendtime}' WHERE Idx={$_POST['Idx']}";
	    $result = mysqli_query($con, $querystring);
	    debugText($querystring);
	    if ($result)
	        echo "Event updated.";
	    else
		echo "Event failed to update.";
	    break;
	case 'delete':
	    $querystring = "DELETE FROM Events WHERE Idx={$_POST['Idx']}";
	    $result = mysqli_query($con, $querystring);
	    debugText($querystring);
	    if ($result)
	        echo "Event deleted.";
	    else
		echo "Event failed to delete.";
	    break;
    }
}

/* Retrieve Amenities List */
$querystring = "SELECT * FROM Amenities ORDER BY Idx";
$result = mysqli_query($con, $querystring);
$amenities = array();
while ( $row = mysqli_fetch_array($result) )
    $amenities[$row['Idx']] = $row['Name'];

/* Retrieve events for selected month */ 
$querystring = "SELECT * FROM Events WHERE StartTime >= '{$_POST["SelectYear"]}-{$_POST["SelectMonth"]}-01' AND ";
$querystring .= "StartTime <= '{$_POST["SelectYear"]}-{$_POST["SelectMonth"]}-{$lastdayofthemonth} 23:59:59' ";
$querystring .= "ORDER BY StartTime";
debugText($querystring);

/* Display calendar */
echo "<form name='calendar' method='post' action='" . pageLink("calendar.php") . "'>";

echo "<input type='hidden' name='SelectMonth' value='{$_POST["SelectMonth"]}' />";
echo "<input type='hidden' name='SelectYear' value='{$_POST["SelectYear"]}' />";
echo "<input type='hidden' name='SelectDay' value='{$_POST["SelectDay"]}' />";
echo "<input type='hidden' name='function' />";
echo "<input type='hidden' name='Idx' />";
echo "<script>var selectedLink = null;</script>";
echo "<div style='width: 100%; height: 100%;'>";
echo "<table class='criteria'><tbody><tr>";

/* Left column - Calendar controls and event entry */
echo "<td class='calendarleft'>";
echo "<table class='criteria' style='border:0; text-align:center'><tbody><tr><td>";
echo "<button name='prevMonth' onClick=\"document.forms['calendar'].elements['SelectMonth'].value--;
					 document.forms['calendar'].elements['function'].value = '';
					  if (document.forms['calendar'].elements['SelectMonth'].value == 0 )
					  {
						document.forms['calendar'].elements['SelectMonth'].value = 12;
						document.forms['calendar'].elements['SelectYear'].value--;
					  }
					  document.forms['calendar'].submit();\">Prev</button>";
echo "</td><td width='200px'><h3>" . date("F", $dayonestring) . " " . date("Y", $dayonestring) . "</h3></td><td>";
echo "<button name='nextMonth' onClick=\"document.forms['calendar'].elements['SelectMonth'].value++;
					 document.forms['calendar'].elements['function'].value = '';
					  if (document.forms['calendar'].elements['SelectMonth'].value == 13 )
					  {
						document.forms['calendar'].elements['SelectMonth'].value = 1;
						document.forms['calendar'].elements['SelectYear'].value++;
					  }
					  document.forms['calendar'].submit();\">Next</button>";

echo "</td></tr></tbody></table>";
echo "<div style='text-align:center' id='status' class='calendarstatus'></div>";

if ( $_SESSION['Level'] >= $editlevel )
{
    echo "<div style='text-align:center' id='Time' hidden='true'>";
    echo "Start: <select name='StartHour'>";
        for ( $i = 0; $i < 24; $i++ )
	{
	    $j = $i;
	    if ( $_SESSION['24HrTime'] != 'on' ) {
		if ( $i > 11 ) {
		    continue;
		}
	        if ( $i == 0 ) {
		    $j = 12;
		}
	    }
            echo "<option value={$i}>{$j}</option>";
	}
        echo "</select>:<select name='StartMinute'>";
        for ( $i = 0; $i < 4; $i++ )
            echo "<option value='" . $i * 15 . "'>" . padInt2($i * 15) . "</option>";
        echo "</select>&nbsp;";

	echo "<select name='StartAMPM'";
	if ( $_SESSION['24HrTime'] == 'on' ) {
	    echo " hidden='hidden'";
	}
	echo "><option value='AM'>AM</option><option value='PM'>PM</option>";
	echo "</select>";
	
	echo " End: <select name='EndHour'>";
        for ( $i = 0; $i < 24; $i++ )
	{
	    $j = $i;
	    if ( $_SESSION['24HrTime'] != 'on' ) {
		if ( $i > 11 ) {
		    continue;
		}
	        if ( $i == 0 ) {
		    $j = 12;
		}
	    }
            echo "<option value={$i}>{$j}</option>";
	}
        echo "</select>:<select name='EndMinute'>";
        for ( $i = 0; $i < 4; $i++ )
            echo "<option value='" . $i * 15 . "'>" . padInt2($i * 15) . "</option>";
        echo "</select>";
	echo "<select name='EndAMPM'";
	if ( $_SESSION['24HrTime'] == 'on' ) {
	    echo " hidden='hidden'";
	}
	echo "><option value='AM'>AM</option><option value='PM'>PM</option>";
	echo "</select>";

	echo "</div>";

    echo "<div style='text-align:center' id='iconSelectContainer' hidden='true'>Select Icon: <br />";
    echo "<input type='radio' name='Icon' id='noIcon' value='' selected='selected' />None&nbsp;&nbsp;&nbsp;<br />";
    for ( $i = 0; $i < count($iconlist); $i++ )
    {
        if (( $i != 0 ) && ( $i % 4 == 0))
	    echo "<br />";
        echo "<input type='radio' name='Icon' id='{$iconlist[$i]}' value='{$iconlist[$i]}' /><img src='{$gazebo_imagedir}{$iconlist[$i]}'   			style='height:50px; width:50px'/>&nbsp;&nbsp;&nbsp;";
    }
    echo "</div>";
}
else
{
    echo "<div style='text-align:center' id='Time' hidden='true'>";
    echo "</div>";
}
echo "<div style='text-align:center'>";
echo "<br />Reserved Amenity:<br /><select name='Amenity' disabled=true style='font-size:100%'><option value='0'>None</option>";
for ( $i = 1; $i <= count($amenities); $i++ )
    echo "<option value='{$i}'>{$amenities[$i]}</option>";
echo "</select>";
echo "<br /><br />Event Name:<br /><input id='Text' name='Text' size='35' maxlength='35' ";
if ( $_SESSION['Level'] < $editlevel )
    echo "readonly='readonly' ";
echo "style='width:70%; font-size:100%' /><br /><br />";
echo "Description:<br /><textarea id='Description' cols='30' rows='3' ";
if ( $_SESSION['Level'] < $editlevel )
    echo "readonly='readonly' ";
echo "name='Description' style='font-size:140%'></textarea>";
if ( $_SESSION['Level'] >= $editlevel )
{
	echo "<br />";
	echo "<button name='new' class='hidden' onclick=\"document.forms['calendar'].elements['function'].value='new';
					    document.forms['calendar'].submit();\">Create</button>";
	echo "<button name='update' class='hidden' onclick=\"document.forms['calendar'].elements['function'].value='update';
					    document.forms['calendar'].submit();\">Update</button>";
	echo "<button name='delete' class='hidden' onclick=\"document.forms['calendar'].elements['function'].value='delete';
					    document.forms['calendar'].submit();\">Delete</button>";
}
echo "</div></td>";
/* Right Column - Calendar grid display */
echo "<td class='calendarright'>";
echo "<table class='criteria' border='2'><tbody>";

echo "<tr><th class='calendar'>Sun</th><th class='calendar'>Mon</th><th class='calendar'>Tue</th><th class='calendar'>Wed</th><th class='calendar'>Thu</th><th class='calendar'>Fri</th><th class='calendar'>Sat</th></tr>";

for ( $week = 1; $week < 7; $week++ )
{
    echo "<tr class='calendar'>";
    for ( $weekday = 0; $weekday <= 6; $weekday++ )
    {
	echo "<td class='calendar'>";
	if ( !isset($day) && ( $weekday == $firstdayofthemonth ))
	    $day = 1;

	if ( isset($day) )
	{
	    if ( $_SESSION['Level'] >= $editlevel ) {
	        echo "<a class=\"calendar\" onclick=\"document.forms['calendar'].elements['new'].className = '';
				document.forms['calendar'].elements['update'].className = 'hidden';
				document.forms['calendar'].elements['delete'].className = 'hidden';
				document.getElementById('iconSelectContainer').hidden = false;
				document.getElementById('Time').hidden = false;
				document.forms['calendar'].elements['StartHour'].selectedIndex = 0;
				document.forms['calendar'].elements['StartMinute'].selectedIndex = 0;
				document.forms['calendar'].elements['StartAMPM'].selectedIndex = 0;
				document.forms['calendar'].elements['EndHour'].selectedIndex = 0;
				document.forms['calendar'].elements['EndMinute'].selectedIndex = 0;
				document.forms['calendar'].elements['EndAMPM'].selectedIndex = 0;
				document.forms['calendar'].elements['Text'].value = '';
				document.forms['calendar'].elements['Description'].value = '';
				document.forms['calendar'].elements['Text'].disabled=false
				document.forms['calendar'].elements['Description'].disabled=false;
				document.forms['calendar'].elements['function'].value = 'new';
				if ( selectedLink != null )
				    selectedLink.className = 'calendar';
				selectedLink = this;
				document.forms['calendar'].elements['noIcon'].checked = true;
				document.forms['calendar'].elements['SelectDay'].value = '{$day}';
				document.forms['calendar'].elements['Idx'].value = null;
				document.forms['calendar'].elements['Amenity'].selectedIndex = 0;
				document.forms['calendar'].elements['Amenity'].disabled = false;
				document.getElementById('status').innerHTML = 'New event {$monthname} {$day}';
				document.getElementById('status').hidden = false;
				if (this.className == 'calendar-selected')
				    this.className = 'calendar';
				else
				    this.className = 'calendar-selected';\">";
	    }
	    echo "<span class='calendarday'>{$day}</span>";
	    if ( $_SESSION['Level'] >= $editlevel )
		echo "</a>";
	    $result = mysqli_query($con, $querystring);
	    echo "<table border='0' style='text-align:center'><tr>";
	    while ( $row = mysqli_fetch_array($result) )
	    {
		$eventday = intval( substr( $row['StartTime'], 8, 2 ));
	        if ( $eventday == $day )
		{
		    if ( $_SESSION['24HrTime'] != 'on' ) {
		        $startHour = substr($row['StartTime'], 11, 2) % 12;
		        $endHour = substr($row['EndTime'], 11, 2) % 12;
		        $startAMPM = AMPMBool(substr($row['StartTime'], 11, 2));
		        $endAMPM = AMPMBool(substr($row['EndTime'], 11, 2));
		    }
		    else {
			$startHour = substr($row['StartTime'], 11, 2);
		        $endHour = substr($row['EndTime'], 11, 2);
			$startAMPM = 0;
			$endAMPM = 0;
		    }
		    $startMinute = intval(intval(substr($row['StartTime'], 14, 2)) / 15);
		    $endMinute = intval(intval(substr($row['EndTime'], 14, 2)) / 15);

		    echo "<td>";
		    echo "<a class=\"calendar\" onclick=\"";
	    	    if ( $_SESSION['Level'] >= $editlevel ) {
		        echo "document.forms['calendar'].elements['new'].className = 'hidden';
				document.forms['calendar'].elements['update'].className = '';
				document.forms['calendar'].elements['delete'].className = '';
				document.getElementById('iconSelectContainer').hidden = false;
				document.getElementById('Time').hidden = false;
				document.forms['calendar'].elements['StartHour'].selectedIndex = '{$startHour}';
				document.forms['calendar'].elements['StartMinute'].selectedIndex = '{$startMinute}';
				document.forms['calendar'].elements['StartAMPM'].selectedIndex = '{$startAMPM}';
				document.forms['calendar'].elements['EndHour'].selectedIndex = '{$endHour}';
				document.forms['calendar'].elements['EndMinute'].selectedIndex = '{$endMinute}';
				document.forms['calendar'].elements['EndAMPM'].selectedIndex = '{$endAMPM}';
				document.forms['calendar'].elements['Text'].disabled=false;
				document.forms['calendar'].elements['Description'].disabled=false;
				document.forms['calendar'].elements['Amenity'].disabled = false;";
		        if ( $row['Icon'] != null )
			    echo "document.forms['calendar'].elements['{$row['Icon']}'].checked = true;";
			else
			    echo "document.forms['calendar'].elements['noIcon'].checked = true;";
		    }
		    else {
			if ( $_SESSION['24HrTime'] != 'on' ) {
			    if ( $startHour == 0 ) {
			        $startHour = 12;
			    }
			    if ( $endHour == 0 ) {
			        $endHour = 12;
			    }
			}
			else {
			    $startHour = padInt2($startHour);
			    $endHour = padInt2($endHour);
			}
			$timestring = "Time: " . $startHour . ":" . padInt2($startMinute * 15);
			if ( $_SESSION['24HrTime'] != 'on' ) {
			    $timestring .= " " . AMPM(substr($row['StartTime'], 11, 2));
			}
			$timestring .= " to " . $endHour . ":" . padInt2($endMinute * 15);
			if ( $_SESSION['24HrTime'] != 'on' ) {
			    $timestring .= " " . AMPM(substr($row['EndTime'], 11, 2));
			}
			echo "document.getElementById('Time').hidden = false;
				document.getElementById('Time').innerHTML = '{$timestring}';";
		    }
		    $description = mysqli_real_escape_string($con, $row['Description']);
		    $text = mysqli_real_escape_string($con, $row['Text']);
		    echo "document.forms['calendar'].elements['Text'].value = '{$text}';
				document.forms['calendar'].elements['Description'].value = '{$description}';
		    		document.forms['calendar'].elements['SelectDay'].value = '{$eventday}';
		    		document.forms['calendar'].elements['Idx'].value = '{$row['Idx']}';
				document.forms['calendar'].elements['Amenity'].selectedIndex = '{$row['Amenity']}';
		                document.getElementById('status').innerHTML = 'Event {$monthname} {$day}';
				document.getElementById('status').hidden = false;
				if ( selectedLink != null )
				    selectedLink.className = 'calendar';
				selectedLink = this;
				if (this.className == 'calendar-select')
				    this.className = 'calendar';
				else
				    this.className = 'calendar-selected';\">";
		    if ($row['Icon'] != '') {
			echo "<img src='{$gazebo_imagedir}{$row['Icon']}' style='height:50px; width:50px' />";
		    }
		    echo "<br />" . $row['Text'] . "</a></td>";
		}
	    }
	    echo "</tr></table>";
	    echo "</td>";
	    $day++;
	}
	if ( $day > daysInMonth($_POST['SelectMonth'], $_POST['SelectYear']))
	{
	    $weekday = 7;
	    $week = 7;
	}
    }
    echo "</tr>";
}

echo "</tbody></table></td></tr></tbody></table></div><br />";

echo "</form>";

include 'gazebo-footer.php';
?>
