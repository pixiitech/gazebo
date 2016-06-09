<?php 
$pagename = "residents";
$printable = true;
require 'gazebo-header.php'; 

foreach(['Idx', 'SavedQuery', 'Unit'] as $key) {
	$_POST[$key] = "";
}
$useSavedQuery = false;
$type = 0;
//Resident fields here are 
// DB fieldname => [0 - Associated publish boolean field name (null=not shown in roster),
//					1 - searchability (null or an array of field names that are searched when processed),
//					2 - publish (true=field is a publish boolean field)]
$fields = [
	"Idx" => ["PublishName", null, false],
	"FirstName" => ["PublishName", ["FirstName", "FirstName2"], false],
	"LastName" => ["PublishName", ["LastName", "LastName2"], false],
	"FirstName2" => ["PublishName", null, false],
	"LastName2" => ["PublishName", null, false],
	"Phone1" => ["PublishPhone1", ["Phone1", "Phone2", "Phone3", "Phone4"], false],
	"Phone2" => ["PublishPhone2", null, false],
	"Phone3" => [null, null, false],
	"Phone4" => [null, null, false],
	"Phone1Type" => ["PublishPhone1", null, false],
	"Phone2Type" => ["PublishPhone2", null, false],
	"Phone3Type" => [null, null, false],
	"Phone4Type" => [null, null, false],	
	"MailingAddress" => ["PublishMailingAddress", ["MailingAddress", "MailingAddress2"], false], 
	"MailingAddress2" => ["PublishMailingAddress", null, false],
	"City" => ["PublishMailingAddress", ["City"], false],
	"State" => ["PublishMailingAddress", ["State"], false],
	"ZIP" => ["PublishMailingAddress", ["ZIP"], false],
	"Country" => ["PublishMailingAddress", ["Country"], false],
	"Email" => ["PublishEmail", ["Email", "Email2"], false],
	"Email2" => ["PublishEmail", null, false],
	"Comments" => [null, null, false],
	"GuestInfo" => [null, null, false],
	"Type" => ["PublishName", null, false],
	"PublishName" => [null, null, true],
	"PublishPhone1" => [null, null, true],
	"PublishPhone2" => [null, null, true],
	"PublishMailingAddress" => [null, null, true],
	"PublishEmail" => [null, null, true]
];
foreach($fields as $key => $value) {
	if (!isset($_POST[$key])) {
		$_POST[$key] = "";
	}
	if (($key == "Idx") && ($_POST[$key] == "0")) {
		$_POST[$key] = "";
	} 
}
?>

<script>
// Load Settings for JS
var publishNameLock = <?php echo fetchSetting( 'PublishNameLock', $con ); ?>;
var defaultPublishName = <?php echo fetchSetting( 'PublishNameDefault', $con ); ?>;
var defaultPublishPhone1 = <?php echo fetchSetting( 'PublishPhone1Default', $con ); ?>;
var defaultPublishPhone2 = <?php echo fetchSetting( 'PublishPhone2Default', $con ); ?>;
var defaultPublishMailingAddress = <?php echo fetchSetting( 'PublishMailingAddressDefault', $con ); ?>;
var defaultPublishEmail = <?php echo fetchSetting( 'PublishEmailDefault', $con ); ?>;
var invertPublishSettings = <?php echo fetchSetting( 'InvertPublishSettings', $con ); ?>;

// Insert JS callback function - called when insert option is selected
var insertCallback = function() {
<?php
			//loop through each publish setting, generate JS to set the default
		if (fetchSetting('InvertPublishSettings', $con) == 'true') {
		  foreach ($fields as $key => $value) {
		  	if ($value[2]) {
			    echo "document.forms['recordinput'].elements['{$key}'].checked = contradict(default{$key});";
			  }
		  }
		} 
		else {
		  foreach ($fields as $key => $value) {
		  	if ($value[2]) {
			    echo "document.forms['recordinput'].elements['{$key}'].checked = default{$key};";
			  }
		  }			
		}
?>
}

// Search JS callback function - called when search option is selected
var searchCallback = function() {
    document.forms['recordinput'].elements['Idx'].value = '';
}

// fillInForm - called from various locations, fills in search/edit fields with record info or defaults
// 		Arguments: fn - Number of form function to switch to (1 - 7)
//			   args - Array of data to fill in each field. 
//				  Format: [[FirstName, "first"], [LastName, "last"], ... ]
function fillInForm(fn, args)
{
    var updateFields = function (FieldArray) {
	for ( var i = 0; i < FieldArray.length; i++ ) {
	    if (FieldArray[i][0] == 'Type')
	    {
		if (FieldArray[i][1] == 0)
  	    	    document.forms['recordinput'].elements['typeOwner'].checked = true;
		else if (FieldArray[i][1] == 1)
  	    	    document.forms['recordinput'].elements['typeTenant'].checked = true;
	    }
	    else if (((FieldArray[i][0] == 'Phone1') ||
			(FieldArray[i][0] == 'Phone2') ||
			(FieldArray[i][0] == 'Phone3') ||
			(FieldArray[i][0] == 'Phone4')) &&
			(document.forms['recordinput'].elements[FieldArray[i][0] + '-1'] != null)) {
		if (document.forms['recordinput'].elements[FieldArray[i][0] + '-1'].size >= 10) {
		    document.forms['recordinput'].elements[FieldArray[i][0] + '-1'].value = FieldArray[i][1];
		}
		else {	    
		    document.forms['recordinput'].elements[FieldArray[i][0] + '-1'].value = FieldArray[i][1].substr(0, 3);
		    document.forms['recordinput'].elements[FieldArray[i][0] + '-2'].value = FieldArray[i][1].substr(3, 3);
		    document.forms['recordinput'].elements[FieldArray[i][0] + '-3'].value = FieldArray[i][1].substr(6, 4);
		}
	    }

	    else if ((FieldArray[i][0] == 'Phone1Type') ||
			(FieldArray[i][0] == 'Phone2Type') ||
			(FieldArray[i][0] == 'Phone3Type') ||
			(FieldArray[i][0] == 'Phone4Type'))
	    {
		if (FieldArray[i][1] == 'international') {
		    convertPhoneFieldIntl(FieldArray[i][0].substr(5, 1));
		}
		else {
		    convertPhoneFieldStd(FieldArray[i][0].substr(5, 1));
		}

		var selectObj = document.forms['recordinput'].elements[FieldArray[i][0]];
		if ((FieldArray[i][1] == '' ) || (FieldArray[i][1] == null )) {
		    selectObj.options[0].selected = true;
		}
		else {
    		    for (var j = 0; j < selectObj.options.length; j++) {
        		if (selectObj.options[j].value == FieldArray[i][1]) {
            		    selectObj.options[j].selected = true;
			    break;
        		}	
    		    }
		}
	    }

	    else if (document.forms['recordinput'].elements[FieldArray[i][0]] == null) {
		continue;
	    }
	    else if (FieldArray[i][0].substr(0, 7) == 'Publish')
	    {
		if (!invertPublishSettings) {
		    if (FieldArray[i][1] == 1)
		        document.forms['recordinput'].elements[FieldArray[i][0]].checked = true;
		    else
		        document.forms['recordinput'].elements[FieldArray[i][0]].checked = false;
		}
		else {
		    if (FieldArray[i][1] == 1)
		        document.forms['recordinput'].elements[FieldArray[i][0]].checked = false;
		    else
		        document.forms['recordinput'].elements[FieldArray[i][0]].checked = true;
		}
	    }
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
	    fnSearch(function(){updateFields(args)});
	    break;
	case 3:
  	    document.forms['recordinput'].elements['fnInsert'].checked = true;
	    fnInsert(function(){updateFields(args)});
	    break;
	case 4:
  	    document.forms['recordinput'].elements['fnUpdate'].checked = true;
	    fnUpdate(function(){updateFields(args)});
	    break;
	case 5:
  	    document.forms['recordinput'].elements['fnDelete'].checked = true;
	    fnDelete(function(){updateFields(args)});
	    break;
	case 7:
  	    document.forms['recordinput'].elements['fnView'].checked = true;
	    fnView(function(){updateFields(args)});
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

if ( isset( $_SESSION['Level'] ) && ( $_SESSION['Level'] >= $level_security ) )
    include 'menu.php';
else if ( !isset($cms) )
    include 'menu-web.php'; 

if ( $_SESSION['Level'] >= $editlevel )
	echo "<h2 style='text-align:center'>Resident Management</h2>";
else
	echo "<h2 style='text-align:center'>Resident Directory</h2>";	

require 'authcheck.php';

$lastfirst = fetchSetting("DisplayLastFirst", $con);
$ucase = fetchSetting("DisplayUppercaseNames", $con);

if ( isset( $_POST['Phone1-1'] ) ) {
	$_POST['Phone1'] = $_POST['Phone1-1'] . $_POST['Phone1-2'] . $_POST['Phone1-3'];
}
if ( isset( $_POST['Phone2-1'] ) ) {
	$_POST['Phone2'] = $_POST['Phone2-1'] . $_POST['Phone2-2'] . $_POST['Phone2-3'];
}
if ( isset( $_POST['Phone3-1'] ) ) {
	$_POST['Phone3'] = $_POST['Phone3-1'] . $_POST['Phone3-2'] . $_POST['Phone3-3'];
}
if ( isset( $_POST['Phone4-1'] ) ) {
	$_POST['Phone4'] = $_POST['Phone4-1'] . $_POST['Phone4-2'] . $_POST['Phone4-3'];
}

if ( $_POST['Idx'] == $_SESSION['Residx'] ) {
	$selfrecord = true;
}
else {
	$selfrecord = false;
}

function errorMessage($msg, $fn)
{
	  global $fields;
    echo "<script>";
    echo "document.getElementById('flasherror').innerHTML = '{$msg}';";
    echo "fillInForm({$fn}, [";
    foreach($fields as $key => $value) {
    	echo "['{$key}', '{$_POST[$key]}'],";
    }
		echo "]);</script>";
    echo "<br />";
}
echo "<span name='flasherror' id='flasherror' class='flasherror'></span>";
echo "<form name='recordinput' method='post' action='" . pageLink("residents") . "' enctype='multipart/form-data' ><p class='center'>
<input type='hidden' name='MAX_FILE_SIZE' value='{$max_upload_size}' />
<input type='hidden' id='Idx' size='2' name='Idx' hidden='hidden' />
<input type='hidden' id='SavedQuery' name='SavedQuery' value=\"{$_POST['SavedQuery']}\" />
<input id='fnList' type='radio' name='function' value='list' />List&nbsp;&nbsp;
<input id='fnSearch' type='radio' name='function' value='search' />Search&nbsp;&nbsp;";
if ($_SESSION['Level'] >= $editlevel)
{
    echo "
<input id='fnInsert' type='radio' name='function' value='insert' onclick='defaultPublishSet();' />Submit New&nbsp;&nbsp;
<input id='fnUpdate' type='radio' name='function' value='update' />Update&nbsp;&nbsp;
<input id='fnDelete' type='radio' name='function' value='delete' />Delete&nbsp;&nbsp;";
}
else
    echo "<input id='fnView' type='radio' name='function' value='view' />View";

echo "
<table class='criteria'><tbody>
<tr class='formfields Search Insert Update Delete'>
<td>
<span class='formfields Delete'>Are you sure you want to delete resident </span>
<span class='formfields Search Insert Update'>Name:&nbsp;</span> 
<span class='formfields Search Insert Update Delete'>
<span class='formfields Search Insert Update'>First&nbsp;</span> 
<input id='FirstName' type='text' size='15' name='FirstName' />
<span class='formfields Search Insert Update'>&nbsp;&nbsp;Last&nbsp; 
<input id='LastName' type='text' size='15' name='LastName' />
</span></span>
<span class='formfields Delete'> ?</span>
<span class='formfields Search'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Unit#&nbsp;<input id='Unit' type='text' size='6' name='Unit' /></span>
</td>
<td>
<span class='formfields Insert Update'>Name #2:&nbsp;&nbsp;First&nbsp;
<input id='FirstName2' type='text' size='15' name='FirstName2' />
&nbsp;&nbsp;Last&nbsp;
<input id='LastName2' type='text' size='15' name='LastName2' />
</span>
</td>
</tr>
<tr class='formfields Search Insert Update'>
<td>";

echo "Phone:<br />
<span class='formfields Insert Update'>
<select id='Phone1Type' name='Phone1Type' class='PhoneType'>
</select>
</span>
<span class='Phone1StdFormatting'>(</span>
<input class='telEntrySec3' id='Phone1-1' type='text' size='3' name='Phone1-1' />
<span class='Phone1StdFormatting'>)&nbsp;</span>
<input class='telEntrySec3' id='Phone1-2' type='text' size='3' name='Phone1-2' />
<span class='Phone1StdFormatting'>-</span>
<input class='telEntrySec4' id='Phone1-3' type='text' size='4' name='Phone1-3' />
<span class='formfields Insert Update'>
&nbsp;
<select id='Phone2Type' name='Phone2Type' class='PhoneType'></select>
<span class='Phone2StdFormatting'>(</span>
<input class='telEntrySec3' id='Phone2-1' type='text' size='3' name='Phone2-1' />
<span class='Phone2StdFormatting'>)&nbsp;</span>
<input class='telEntrySec3' id='Phone2-2' type='text' size='3' name='Phone2-2' />
<span class='Phone2StdFormatting'>-</span>
<input class='telEntrySec4' id='Phone2-3' type='text' size='4' name='Phone2-3' />";
echo "</span>";
echo "</td>
<td>
<span class='formfields Insert Update'>";
echo "Phone:<br />";
if ( $_SESSION['Level'] >= $level_security ) {
    echo "<select id='Phone3Type' name='Phone3Type' class='PhoneType'></select>
<span class='Phone3StdFormatting'>(</span>
<input class='telEntrySec3' id='Phone3-1' type='text' size='3' name='Phone3-1' />
<span class='Phone3StdFormatting'>)&nbsp;</span>
<input class='telEntrySec3' id='Phone3-2' type='text' size='3' name='Phone3-2' />
<span class='Phone3StdFormatting'>-</span>
<input class='telEntrySec4' id='Phone3-3' type='text' size='4' name='Phone3-3' />
&nbsp;
<select id='Phone4Type' name='Phone4Type' class='PhoneType'></select>
<span class='Phone4StdFormatting'>(</span>
<input class='telEntrySec3' id='Phone4-1' type='text' size='3' name='Phone4-1' />
<span class='Phone4StdFormatting'>)&nbsp;</span>
<input class='telEntrySec3' id='Phone4-2' type='text' size='3' name='Phone4-2' />
<span class='Phone4StdFormatting'>-</span>
<input id='Phone4-3' type='text' size='4' name='Phone4-3' />";
}
echo "</span></td>
</tr>";

echo "<tr class='formfields Search Insert Update'>
<td>Email  <input id='Email' type='text' size='22' name='Email' />
</td>
<td>
<span class='formfields Insert Update'>
Email 2  <input id='Email2' type='text' size='22' name='Email2' />
</span>
</td></tr>";
echo "
<tr>
<td colspan='2'>
<span class='formfields Search Insert Update'>
Mailing Address <input id='MailingAddress' type='text' size='30' name='MailingAddress' />
City <input id='City' type='text' size='15' name='City' />&nbsp;&nbsp;
State/Province <input id='State' type='text' size='3' name='State' />&nbsp;&nbsp;
ZIP <input id='ZIP' type='text' size='10' name='ZIP' />
Country <input id='Country' type='text' size='15' name='Country' />

<br />
</span>
<span class='formfields Insert Update'>
Mailing Address <input id='MailingAddress2' type='text' size='30' name='MailingAddress2' />
<span class='formfields Search'>
<input id='typeAll' type='radio' name='Type' value='All' checked='checked' />All&nbsp;&nbsp;
</span>
<input id='typeOwner' type='radio' name='Type' value='Owner' required=true />Owner&nbsp;&nbsp;
<input id='typeTenant' type='radio' name='Type' value='Tenant' required=true />Tenant&nbsp;&nbsp;
</span>
</td>
</tr>";

$publishNameLock = fetchSetting( "PublishNameLock", $con );
$invertPublishSettings = fetchSetting( "InvertPublishSettings", $con );

if ( $_SESSION['Level'] >= $editlevel ) {
	echo "<tr class='formfields Insert Update'><td>Comments<br />
	<textarea id='Comments' name='Comments' cols='50' rows='2'></textarea></td>";
	echo "<td>";
	if ( $publishNameLock == 'true' ) {
	    echo "<span hidden='hidden'>";
	}
	echo "<input type='checkbox' name='PublishName' id='PublishName' />&nbsp; ";
	if ( $invertPublishSettings == 'true' ) {
	    echo "DO NOT ";
	}
	echo "Publish Name and Unit # in Directory<br />";
	if ( $publishNameLock == 'true' ) {
	    echo "</span>";
	}
	echo "
&nbsp;&nbsp;&nbsp;&nbsp;<input id='PublishPhone1' name='PublishPhone1' type='checkbox' />&nbsp; ";
if ( $invertPublishSettings == 'true' ) {
    echo "DO NOT ";
}
echo "Publish Phone 1
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input id='PublishPhone2' name='PublishPhone2' type='checkbox' />&nbsp; ";
if ( $invertPublishSettings == 'true' ) {
    echo "DO NOT ";
}
echo "Publish Phone 2 &nbsp;<br />
&nbsp;&nbsp;&nbsp;&nbsp;<input id='PublishMailingAddress' name='PublishMailingAddress' type='checkbox' />&nbsp; ";
if ( $invertPublishSettings == 'true' ) {
    echo "DO NOT ";
}
echo "Publish Mailing Address &nbsp;
	<input id='PublishEmail' name='PublishEmail' type='checkbox' />&nbsp; ";
if ( $invertPublishSettings == 'true' ) {
    echo "DO NOT ";
}
echo "Publish Email &nbsp;";
}
echo "</td></tr>";
if (( $_SESSION['Level'] >= $editlevel ) && ( fetchSetting("ShowGuestInfo", $con) == 'true' )) {
	echo "<tr class='formfields Insert Update'";
	echo "><td>Guest Info<br />
	<textarea id='GuestInfo' name='GuestInfo' cols='50' rows='2'></textarea></td>";
	echo "<td></td></tr>";
}
if ( $_SESSION['Level'] <= $level_resident ) {
	echo "<tr class='formfields Update SelfRecord'><td colspan='2'><i>To modify your roster info and publish settings, visit your <a href='";
	echo pageLink('profile') . "'>Profile</a></i></td></tr>";
}

echo "</tbody></table>";

echo "<p class='center formfields List Search Insert Update Delete Buttons'><input type='submit' value='Search' id='submitbutton' /> <input type='reset' value='Clear' /></p>";

echo "</form>";

if ( isset( $_GET['Idx'] ) )
{
    checkRadio('fnSearch');
    $_POST['Idx'] = $_GET['Idx'];
    $_POST['function'] = "search";
}

if ( !isset($_POST["function"]) ) {
    checkRadio('fnList');
    $_POST['function'] = "list";
}

//Prepare data
debugText("Preparing Data...");
foreach($fields as $key => $value) {
	if (isset($value[2]) && $value[2]) { //Publish fields
		if ( !isset($_POST[$key]) ) {
			$_POST[$key] = '0';
		}
		else {
			$_POST[$key] = '1';
		}
		if ( $invertPublishSettings == 'true' ) {
    		$_POST[$key] = contradict($_POST[$key]);
		}
	}
	if ($key == "Idx") {
		$_POST[$key] = intval($_POST[$key]);
	}
	// Mysql escape all fields
	$_POST[$key] = mysqli_real_escape_string($con, $_POST[$key]);
}
debugText("Finished preparing data");
if ( $_POST['Type'] == "Owner" )
    $type = 0;
else if ( $_POST['Type'] == "Tenant" )
    $type = 1;
debugText("Begin switch...");
//Update, Insert or Delete
switch ( $_POST['function']) {
  case 'update':
    debugText("POST IDX={$_POST["Idx"]}");
		if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )) )
		{
		  errorMessage( "Please specify a valid numeric index.", 4);
		  break;
		}
		if (( trim($_POST["FirstName"]) == "" ) && ( trim($_POST["LastName"]) == "" ))
		{
		  errorMessage("Please specify a resident name.", 4);
		  break;
		}

		//Save SQL Record
		debugText("Assembling querystring");
		$querystring = "UPDATE Residents SET ";
		foreach($fields as $key => $value) {
			if ($key == 'Type') {
				$querystring .= "Type={$type}, ";
			}
			else
			  $querystring .= "{$key}='{$_POST[$key]}', ";
		}
		$querystring = substr($querystring, 0, strlen($querystring) - 2); //Chop off last comma space
		$querystring .= "	WHERE Idx={$_POST['Idx']}";
		debugText($querystring);
		$result = mysqli_query($con, $querystring);
		if ( $result )
			echo "Resident {$_POST['FirstName']} {$_POST['LastName']} updated.<br />";
		else
		{
			errorMessage("Resident {$_POST['FirstName']} {$_POST['LastName']} failed to update.<br />", 4);
		  	break;
		}
		$_GET['Idx'] = $_POST['Idx'];
		$_POST['function'] = 'search';
		$useSavedQuery = 'yes';
		break;

  case "insert":
		if (( trim($_POST["FirstName"]) == "" ) && ( trim($_POST["LastName"]) == "" ))
		{
		  errorMessage("Please specify a resident name.", 3);
		  break;
		}

		//Save SQL Record
		$querystring = "INSERT INTO Residents (";
		foreach($fields as $key => $value) {
			if ($key == "Idx") {
				continue;
			}
			$querystring .= $key . ", ";
		}
		$querystring = substr($querystring, 0, strlen($querystring) - 2); //Chop off last comma space
		$querystring .= ") VALUES (";
		foreach($fields as $key => $value) {
			if ($key == "Idx") {
				continue;
			}
			else if ($key == "Type") {
			  $querystring .= "'{$type}', ";
			}
			else
			  $querystring .= "'{$_POST[$key]}', ";
		}	
		$querystring = substr($querystring, 0, strlen($querystring) - 2); //Chop off last comma space
		$querystring .= ")";
		debugText($querystring);
		$result = mysqli_query($con, $querystring);
		if ( !$result )
		{
			errorMessage("Resident {$_POST['FirstName']} {$_POST['LastName']} failed to save.<br />", 3);
		 	break;
	        }
		else
		{
		    $querystring = "SELECT Idx FROM Residents WHERE FirstName='{$_POST['FirstName']}' AND LastName='{$_POST['LastName']}'";
		    debugText($querystring);
		    $result = mysqli_query($con, $querystring);
		    $row = mysqli_fetch_array($result);
		    echo "Resident {$_POST['FirstName']} {$_POST['LastName']} saved. Idx={$row['Idx']}<br />";
		}
		$_POST['function'] = 'search';
		$useSavedQuery = 'yes';
		break;

  case "delete":
		if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )))
		{
		  errorMessage("Please specify a valid resident index.", 5);
		  break;
		}
		if ( !(fetchResname($_POST["Idx"],$con)))
		{
		  errorMessage("Specified resident number does not exist.<br />", 5);
		  break;
		}
		$querystring = "DELETE FROM Residents WHERE Idx={$_POST['Idx']}";
		debugText($querystring);
		$result = mysqli_query($con, $querystring);
		if ( $result )
			echo "Resident #{$_POST["Idx"]} deleted.<br />";
		else
			echo "Resident #{$_POST["Idx"]} failed to save.<br />";

		$_POST['function'] = 'search';
		$useSavedQuery = 'yes';
		break;

  default:
	  break;
}

//Search or List
if ( $_POST['function'] == 'search' ) {
	echo "<h4 style='text-align:center'>Search Results:</h4>";
}

if (( $_POST['function'] == 'list' ) || ( $_POST['function'] == 'search' )) {
	//Assemble Query
	$querystring = "SELECT * FROM Residents" ;
	if ( $_POST['Unit'] != "" ) {
	    $querystring .= ", Properties";
	}
	$querystring .= " WHERE 1=1";
	if (( $_POST['Idx'] != "" ) && ( $_POST['Idx'] != 0 )) {
		$querystring .= " AND Residents.Idx = " . intval($_POST['Idx']);
	}
	if ( $_POST['Type'] == "Owner" ) {
		$querystring .= " AND Residents.Type = 0";
	}
	if ( $_POST['Type'] == "Tenant" ) {
		$querystring .= " AND Residents.Type = 1";
	}
	if ( $_POST['Unit'] != "" ) {
		$querystring .= " AND Properties.Unit = " . intval($_POST['Unit']);
		$querystring .= " AND Properties.Residx = Residents.Idx";
	}
	foreach($fields as $key => $value) {
		if ($value[1]) { //If searchability is not null, search provided fields
			if ($_POST[$key] != "") {
				$lKey = strtolower($_POST[$key]);
				$querystring .= " AND (";
				for($t = 0; $t < count($value[1]); $t++) {
					if ($t > 0) $querystring .= " OR ";
					$querystring .= "LOWER({$key}) LIKE '%{$lKey}%'";
				}
				$querystring .= ")";
			}
		}
	}

	//Perform query
	debugText("Original Query:" . $querystring);
	if ( $useSavedQuery == 'yes' ) {
	    $querystring = stripslashes($_POST['SavedQuery']);
	    debugText("Using Saved Query:" . $querystring);
	}
	echo "<script>document.forms['recordinput'].elements['SavedQuery'].value = \"{$querystring}\";</script>";
	$result = mysqli_query($con, $querystring); 
	//Display table
	$k=0;
	$results=0;
	echo "<div id='printarea'><table class='result sortable tablesorter' border=4>";
	echo "<thead><tr><th>Name</th><th>Unit #</th><th>Phone 1</th><th>Phone 2</th><th>Mailing Address</th><th>Email</th>";
	if ( $_SESSION['Level'] >= $editlevel )
	    echo "<th>Del</th>";
	echo "</tr></thead><tbody>";
	//Loop through data
	while ( $row = mysqli_fetch_array($result) )
	{
		if ( !($row['PublishName']) && ( $_SESSION['Level'] < $level_security ) )
		    continue;

		if ( $_SESSION['Level'] >= $editlevel )
		    $selectOpt = 4;
		else
		    $selectOpt = 7;

		// Prepare Data
		$fullAddress = $row['MailingAddress'];
		if ( ( $row['MailingAddress2'] != NULL ) && ( $row['MailingAddress2'] != '' ) ) {
		    $fullAddress .= "<br />" . $row['MailingAddress2'];
		}
		if ( ( $row['City'] != NULL ) && ( $row['City'] != '' ) ) {
		    $fullAddress .= "<br />" . $row['City'] . ", " . $row['State'] . " " . $row['ZIP'] . "  " . $row['Country'];
		}
		$guestinfo = mysqli_real_escape_string($con, $row['GuestInfo']);
		$comments = mysqli_real_escape_string($con, $row['Comments']);
		$phone1 = formatPhone($row['Phone1'], $row['Phone1Type']);
		$phone2 = formatPhone($row['Phone2'], $row['Phone2Type']);
		$phone3 = formatPhone($row['Phone3'], $row['Phone3Type']);
		$phone4 = formatPhone($row['Phone4'], $row['Phone4Type']);
		if ( $row['Type'] == 1 ) {
		    $type = 'tenant';
		}
		else {
		    $type = 'owner';
		}  
		$anchor = "";

		//Prepare link (roster view)
        $anchor .= "<a href=\"#top\" onclick=\"fillInForm({$selectOpt}, [['Idx', '{$row['Idx']}'], ";
        foreach($fields as $key => $value) {
        	//Non-admin login, publish field is specified, and publish field = 1
        	if ( $_SESSION['Level'] < $level_security ) {
        	  if ($value[0] && $row[$value[0]]) { 
        	    $anchor .= "['{$key}', '{$row[$key]}'], ";
        	  }
        	}
        	else { //Admin login
        	  $anchor .= "['{$key}', '{$row[$key]}'], ";
        	}
        }
		$anchor = substr($anchor, 0, strlen($anchor) - 2) . "]);\">";

		//Begin record
		echo "<tr>";

		//Display name (admin and resident view)
		echo "<td>";
		echo $anchor;
		echo displayName($row['FirstName'], $row['LastName'], $ucase, $lastfirst);
		if (( $row['LastName2'] != NULL) && ( $row['LastName2'] != "" ) ) {
		    echo "<hr>" . displayName($row['FirstName2'], $row['LastName2'], $ucase, $lastfirst);
		}
		echo "</a>";
		echo "</td>";
		//Display unit# and owner/tenant
		echo "<td>";
		$units = fetchUnit($row['Idx'], $con, $type);
		for ( $i = 0; $i < count($units); $i++ )
		{
		    echo "<span hidden='hidden'>" . padInt($units[$i], 5) . "</span>";  //hidden sort parameter
		    if ( $_SESSION['Level'] >= $level_security ) {
		        echo "Unit <a href='" . pageLink("properties", "Unit={$units[$i]}") . "'>" . $units[$i] . "</a> - " . ucfirst($type) . "<br />";
		    }
		    else {
		         echo "Unit " . $units[$i] . " - " . ucfirst($type) . "<br />";
		    }
		}

		echo "</td>";
		//Display home phone, cell phone, mailing address
		//Display Phone 1
		echo "<td>";
		if ( $_SESSION['Level'] >= $level_security )
		{
		    echo $phone1;
		}
		else if ( $row['PublishPhone1'] ) {
		    echo $phone1;
		}
		//Display Phone 3
		if (( $row['LastName2'] != NULL) && ( $row['LastName2'] != "" ) ) {
		    echo "<hr>";
		    if ( $_SESSION['Level'] >= $level_security )
		    {
		        echo $phone3;
		    }
		    else if ( $row['PublishPhone1'] ) {
		        echo $phone3;
		    }
		}
		echo "</td>";

		//Display Phone 2
		if ( $row['LastName2'] == "" ) {
		    echo "<td>";
		}
		else {
		    echo "<td class='thinbottom'>";
		}
		if ( $_SESSION['Level'] >= $level_security )
		{
		    echo $phone2;
		}
		else if ( $row['PublishPhone2'] ) {
		    echo $phone2;
		}
		//Display Phone 4
		if (( $row['LastName2'] != NULL) && ( $row['LastName2'] != "" ) ) {
		    echo "<hr>";
		    if ( $_SESSION['Level'] >= $level_security )
		    {
		        echo $phone4;
		    }
		    else if ( $row['PublishPhone2'] ) {
		        echo $phone4;
		    }
		}
		echo "</td>";
		//Display Address
		echo "<td>";
		if ( $_SESSION['Level'] >= $level_security )
		{
		    if ( trim($row['MailingAddress']) != "" )
		        echo $fullAddress;
		}
		else {
		    if (( $row['PublishMailingAddress'] ) && ( trim($row['MailingAddress']) != "" ))
		        echo $fullAddress;
		}
		echo "</td>";

		//Display email
		echo "<td>";
		if (( trim($row['Email']) != "" ) && 
		    (( $_SESSION['Level'] >= $level_security ) || ( $row['PublishEmail'] ))) {
		    echo "<a href='mailto:{$row['Email']}'>" . $row['Email'] . "</a>";
		}
		if (( $row['LastName2'] != NULL) && ( $row['LastName2'] != "" ) ) {
		    echo "<hr>";
		    if (( trim($row['Email2']) != "" ) && 
		        (( $_SESSION['Level'] >= $level_security ) || ( $row['PublishEmail'] ))) {
		        echo "<a href='mailto:{$row['Email2']}'>" . $row['Email2'] . "</a>";
		    }
		}
		echo "</td>";

		//Delete link
		if ( $_SESSION['Level'] >= $editlevel )
		{ 
		    echo "<td>";	
    		    echo "<a href=\"#top\" onclick=\"fillInForm(5, [['Idx', '{$row['Idx']}'], ['FirstName', '{$row['FirstName']} {$row['LastName']}']]);\" >X</a>"; 
		    echo "</td>";
		}
		echo "</tr>";
		$results++;
	}

	echo "</tbody></table>";
	if ( $results == 0 )
	  echo "<br /><i>No records found.</i><br />";
	if ( $results == 1 )
	  echo "<br /><i>One record found.</i><br />";
	if ( $results > 1 )
	  echo "<br /><i>{$results} records found.</i><br />";
	echo "</div>";
}

//Autoload resident
if ( isset( $_GET['Idx'] ) && ( $_SESSION['Level'] >= $level_security ))
{
	$querystring = "SELECT * FROM Residents WHERE Idx={$_GET['Idx']}";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	$row = mysqli_fetch_array($result);
	if ( $row == NULL ) {
		echo "Resident not found.<br />";
	}
	else {
    	$autoscript = "<script>fillInForm(4, [";
    	foreach($fields as $key => $value) {
    		$autoscript .= "['{$key}', '{$row[$key]}'], ";
    	}
		$autoscript = substr($autoscript, 0, strlen($autoscript) - 2) . "]);</script>";
		echo $autoscript;
	}
}

include 'gazebo-footer.php';
?>