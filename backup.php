<?php 
global $pagename;
$pagename = "backup";
require 'gazebo-header.php'; 
?>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Mass Data Utility</h2>

<?php
require 'authcheck.php';
/*set_error_handler("customError"); 
function customError($errno, $errstr, $errfile, $errline) {
  echo "<b>Error - </b> <i>{$errfile}, {$errline}:</i> [{$errno}] {$errstr}<br>";
  echo "Ending Script";
  die();
} */

if ( !isset($_POST['format'])) {
    $_POST['format'] = '';
}
if ( !isset($_POST['data'])) {
    $_POST['data'] = '';
}

if ( !isset($_POST['separator'])) {
    $_POST['separator'] = "\t";
}
if ( !isset($_POST['upload_db'])) {
    $_POST['upload_db'] = "";
}
if ( !isset($_POST['fsseparator'])) {
    $_POST['fsseparator'] = "restore";
}

/* Connect to SQL Server */

$con_gazebo = connect_gazebo_DB("mysqli");

/* WP DB connector */
if ( isset($cms) && ($cms == "wp"))
{
    $con_wp = connect_WP_DB("mysqli");
}

$querystring = "SHOW TABLES";
$result = mysqli_query($con_gazebo, $querystring);
$tables = array();
$n = 0;

while ( $row = mysqli_fetch_array($result) )
{
    $tables[$n] = $row[0];
    $n++;
}

if ( isset($cms) && ($cms == "wp"))
{
    $querystring = "SHOW TABLES";
    $result = mysqli_query($con_wp, $querystring);
    $wp_index = $n;
    $w = 0;
    while ( $row = mysqli_fetch_array($result) )
    {
        $tables[$n] = $row[0];
        $w++;
	$n++;
    }
}

$outputbuffer = "";

if (isset($_POST['bak']) && ($_POST['bak'] == 'backup'))
{
    $con = $con_gazebo;
    //Generic Backup
    for ( $n = 0; $tables[$n] != null; $n++ )
    {
	if ( isset($cms) && ($cms == "wp") && ($n == $wp_index))
	    $con = $con_wp;
	if ( isset( $_POST['db_' . $tables[$n]] ))
	{
	    /* Backup Table Format */
	    $querystring = "SHOW COLUMNS FROM ". $tables[$n];
	    debugText($querystring);
	    $result = mysqli_query($con, $querystring);
  	    $file = fopen($backupdir . $tables[$n] . "-" . date("m-d-y") . ".frm","w+");
	    while ( $row = mysqli_fetch_array( $result ) )
	    {
		fwrite($file, $row['Field'] . "	");
		fwrite($file, $row['Type'] . "	");
		fwrite($file, $row['Null'] . "	");
		fwrite($file, $row['Key'] . "	");
		fwrite($file, $row['Default'] . "	");
		fwrite($file, $row['Extra'] . "	");
		fwrite($file, "\n");
	    }	
	    fclose($file);
	    $outputbuffer .= $tables[$n] . " format structure saved as " . $tables[$n] . "-" . date("m-d-y") . ".frm\n";	

	    /* Backup Data */
	    $querystring = "SELECT * FROM " . $tables[$n];
	    debugText($querystring);
	    $result = mysqli_query($con, $querystring);
  	    $file = fopen($backupdir . $tables[$n] . "-" . date("m-d-y") . ".bak","w+");
	    $rows = 0;
	    while ( $row = mysqli_fetch_array($result) )
	    {
		$rows++;
		for ( $i=0; $i<count($row); $i++ )
		    fwrite($file, mysqli_real_escape_string($con, str_replace("	", "", $row[$i])) . "	");
		fwrite($file, "\n");
	    }
	    fclose($file);
	    $outputbuffer .= $tables[$n] . " backup saved as " . $tables[$n] . "-" . date("m-d-y") . ".bak\n{$rows} records processed.\n";
	}
    }
}
else if (isset($_POST['res']) && ($_POST['res'] == 'restore')) 
{
    //Read format file
    $file=fopen($backupdir . $_POST['restore_db'] . ".frm", "r");
    if ( !$file )
	echo "Unable to open file " . $_POST['restore_db'] . ".frm<br />";
    else
    {
	$buffer = "";

	for ( $i=0; !feof($file); $i++ )
	{
	    $text = fgets($file);
	    $buffer .= $text;
	}
	fclose($file);
	$_POST['format'] = $buffer;
    }
    //Read data backup file
    $file=fopen($backupdir . $_POST['restore_db'] . ".bak", "r");
    if ( !$file )
	$outputbuffer .= "Unable to open file " . $_POST['restore_db'] . ".bak";
    else
    {
	$buffer = "";

	for ( $i=0; !feof($file); $i++ )
	{
	    $text = fgets($file);
	    $buffer .= $text;
	}
	fclose($file);
	$_POST['data'] = $buffer;
    }
    $_POST['upload_db'] = substr($_POST['restore_db'], 0, strpos( $_POST['restore_db'], "-" ));
}

if (isset($_POST['data']))
{
	$dataparse = $_POST['data'];
	$format = $_POST['format'];
	$fieldlist = array();
	$typelist = array();
	$nulllist = array();
	$prikeylist = array();
	$defaultlist = array();
	$extralist = array();

	if (!isset($_POST['newline'])) {
	    $newline = "\n";
	    $nllength = 1;
	}
	else if ($_POST['newline'] == 'Windows') {
	    $newline = "\r\n";
	    $nllength = 2;
	}
	else if ($_POST['newline'] == 'Linux') {
	    $newline = "\n";
	    $nllength = 1;
	}
	else if ($_POST['newline'] == 'Mac') {
	    $newline = "\r";
	    $nllength = 1;
	}

	if ( $_POST['fsseparator'] == 'specify' )
	{
	    $separator = $_POST['fsseparatorspecify'];
	}
	else {
	    $separator = "\t";
	}
	$dellength = strlen($separator);

	//Parse format string
	for ($fieldcount = 0; $format != ""; $fieldcount++)
	{
	    $nlpos = strpos($format, $newline);
	    $next = substr($format, $nlpos + $nllength);

	    $delpos = strpos($format, $separator);
	    $Name = substr($format, 0, $delpos);
	    $Name = trim($Name);
	    if (($Name == "") || ($Name == NULL) || ($nlpos === false))
		   break;
	    $fieldlist[$fieldcount] = $Name;
	    $format = substr($format, $delpos + $dellength);

	    $delpos = strpos($format, $separator);
	    $typelist[$fieldcount] = substr($format, 0, $delpos);
	    $format = substr($format, $delpos + $dellength);
	    $delpos = strpos($format, $separator);
	    $nulllist[$fieldcount] = substr($format, 0, $delpos);
	    $format = substr($format, $delpos + $dellength);
	    $delpos = strpos($format, $separator);
	    if (substr($format, 0, $delpos) == "PRI") {
		array_push($prikeylist, $Name);
	    }
	    $format = substr($format, $delpos + $dellength);
	    $delpos = strpos($format, $separator);
	    $defaultlist[$fieldcount] = substr($format, 0, $delpos);
	    $format = substr($format, $delpos + $dellength);
	    $nlpos = strpos($format, $newline);
	    $extralist[$fieldcount] = trim(substr($format, 0, $nlpos));
	    $format = $next;
	}

	//Select DB
	if ( isset($cms) && ($cms == "wp") && (substr($_POST['upload_db'], 0, 3) == "wp_"))
		$con = $con_wp;
	else
	        $con = $con_gazebo;

	//Drop and recreate table if option is checked
	if ( isset( $_POST['recreate_table'] ) ) {
	    $querystring = "DROP TABLE " . $_POST['upload_db'];
	    $result = mysqli_query($con, $querystring);
	    $outputbuffer .= $querystring . "\n\n";
	    if ( $result ) {
		$outputbuffer .= "Table " . $_POST['upload_db'] . " dropped.\n\n";
	    }
	    else {
		$outputbuffer .= "Table " . $_POST['upload_db'] . " failed to drop.\n\n";
	    }
	    $querystring = "CREATE TABLE " . $_POST['upload_db'] . "(";
	    for ( $i = 0; $i < count($fieldlist); $i++ ) {
	        $querystring .= $fieldlist[$i] . " ";
		$querystring .= $typelist[$i] . " ";
		if (($defaultlist[$i] != NULL) && ($defaultlist[$i] != "")) {
		    if ( strtolower(substr($typelist[$i], 0, 3)) == 'int' ) {
		        $querystring .= "DEFAULT " . $defaultlist[$i] . " ";
		    } else {
		        $querystring .= "DEFAULT '" . $defaultlist[$i] . "' ";
		    }
		}
		if ($nulllist[$i] == "NO") {
		    $querystring .= "NOT NULL ";
		}
		$querystring .= $extralist[$i];
		if ( $i + 1 < count($fieldlist) ) {
		    $querystring .= ", ";
		}
	    }
	    if ( count($prikeylist) == 1 ) {
		$querystring .= ", PRIMARY KEY ({$prikeylist[0]})";
	    }
	    else if ( count($prikeylist > 1) ) {
		$querystring .= ", CONSTRAINT composite_key PRIMARY KEY (";
	        for ( $i = 0; $i < count($prikeylist); $i++ ) {
		    if ( $i > 0 ) {
			$querystring .= ", ";
		    }
		    $querystring .= $prikeylist[$i];
	        }
		$querystring .= ")";
	    }
	    $querystring .= ")";
	    $result = mysqli_query($con, $querystring);
	    $outputbuffer .= $querystring . "\n\n";
	    if ( $result ) {
		$outputbuffer .= "Table " . $_POST['upload_db'] . " created.\n\n";
	    }
	    else {
		$outputbuffer .= "Table " . $_POST['upload_db'] . " failed to create.\n\n";
	    }

	}
	//Parse data
	$rowcount = 0;
	$failedrowcount = 0;
	while ($dataparse != "")
	{

	    $rowdata = array();
	    $break = false;
	    for ( $n = 0; (($break == false) && ($dataparse != "")); $n++ )
	    {
		$nlpos = strpos($dataparse, $newline);
		$delpos = strpos($dataparse, $_POST['separator']);
		if ( $delpos === false ) //Last field before EOF
		{
		    $rowdata[$n] = trim($dataparse);
		    $dataparse = "";
		    $break = true;
		}
		else if ( $nlpos === false ) //Last line, more fields
		{
		    $rowdata[$n] = trim(substr($dataparse, 0, $delpos));
		    $dataparse = substr($dataparse, $delpos + $dellength);
		}
		else if ( $nlpos < $delpos ) //Last field of not last line
		{
		    $rowdata[$n] = trim(substr($dataparse, 0, $nlpos));
		    $dataparse = substr($dataparse, $nlpos + $nllength);
		    $break = true;
		}
		else	//More fields, more lines
		{
		    $rowdata[$n] = trim(substr($dataparse, 0, $delpos));
		    $dataparse = substr($dataparse, $delpos + $dellength);
		}
	    }

	    if ( $rowdata[0] == '' )
		continue;
	    $querystring = "INSERT INTO " . $_POST['upload_db'] . "(";
	    for ($n = 0; $n < count($fieldlist); $n++)
	    {
		if ( $n != 0 )
		    $querystring .= ", ";
		$querystring .=  $fieldlist[$n];
	    }
	    $querystring .= ") VALUES (";
	    for ($n = 0; $n < count($fieldlist); $n++)
	    {
		if ( $n != 0 )
		    $querystring .= ", ";
		if ( strtolower(substr($typelist[$n], 0, 3)) == 'int' )
 		{
		    if ( $rowdata[$n] == '' ) {
			$querystring .= 'NULL';
		    } else {
		        $querystring .= $rowdata[$n];
		    }
		}
		else if ( strtolower(substr($typelist[$n], 0, 8)) == 'datetime' )
 		{
		    if ( $rowdata[$n] == '' ) {
			$querystring .= 'NULL';
		    } else {
		        $querystring .=  "'" . $rowdata[$n] . "'";
		    }
		}
		else {
		    $querystring .=  "'" . $rowdata[$n] . "'";
		}
	    }
	    $querystring .= ")";
	    $result = mysqli_query($con, $querystring);
	    $outputbuffer .= $querystring . "\n\n";
	    if ( $result ) {
		$outputbuffer .= "Record inputted successfully.\n\n";
	        $rowcount++;
	    }
	    else {
		$outputbuffer .= "Record failed to insert.\n\n";
		$failedrowcount++;
	    } 
	}  
	$outputbuffer .= "Restore {$_POST['upload_db']}: {$fieldcount} fields on {$rowcount} record(s) processed successfully. {$failedrowcount} record(s) failed.";

}

echo "<br /><div class='recordinput'>
<h3 style='text-align:center'>Database Backup</h3>
<form name='backupform' method='post' style='text-align:center' action='" . pageLink("backup") . "'>
<input id='bak' name='bak' type='hidden' />
<input type='checkbox' name='db_all' onclick=\"";
for ( $n = 0; $n < count($tables); $n++ )
    echo "document.forms['backupform'].elements['db_{$tables[$n]}'].checked='true'; ";
echo "\" />All  <br />";
echo "<i>Gazebo Databases:  </i>";
for ( $n = 0; $n < count($tables); $n++ )
{
    if ( isset($cms) && ($cms=="wp") && ($n == $wp_index))
        echo "<br /><i>Wordpress Databases:  </i>";
    echo "<input type='checkbox' name='db_{$tables[$n]}' />{$tables[$n]}  ";
}
echo "<br />";

echo "<input type='submit' value='Backup' onclick='document.forms[\"backupform\"].elements[\"bak\"].value=\"backup\"' />
</form></div>
<div class='recordinput'>
<h3 style='text-align:center'>Database Restore</h3>
<form name='restoreform' method='post' style='text-align:center' action='" . pageLink("backup") . "'>
<input id='res' name='res' type='hidden' />
<select name='restore_db'>";
/* Load list of forms */
$files = glob($backupdir . "*.bak");

for ( $i = 0; $i < count($files); $i++ )
{
    $extpos = strpos($files[$i], ".bak");
    $noext = substr($files[$i], strlen($backupdir), $extpos - strlen($backupdir)); 
    echo "<option value='{$noext}'>{$noext}</option>";
}
echo "</select>";
echo "&nbsp;<input type='checkbox' name='recreate_table' onclick='if (this.checked == true) this.checked = window.confirm(\"WARNING: Use this function only as directed by technical support. This function WILL result in data loss. Are you sure you want to proceed?\");' />Drop and recreate table";
echo "<br />
<input type='submit' value='Restore' onclick='document.forms[\"restoreform\"].elements[\"res\"].value=\"restore\"' /><br />
</form></div>
<div class='recordinput'>
<h3 style='text-align:center'>Mass Data SQL Upload</h3>
<form name='sqlupload' method='post' style='text-align:center' action='" . pageLink("backup") . "'>
<table class='criteria'><tbody><tr><td colspan='2'>
Insert data into:<br />";
for ( $n = 0; $n < count($tables); $n++ )
{
    if ( isset($_POST['upload_db']) && ($_POST['upload_db'] == $tables[$n] ))
        echo "<input type='radio' name='upload_db' id='{$tables[$n]}Option' value='{$tables[$n]}' checked=true required='required' />{$tables[$n]}  ";
    else
        echo "<input type='radio' name='upload_db' id='{$tables[$n]}Option' value='{$tables[$n]}' required='required' />{$tables[$n]}  ";
}
echo "</td></tr>
<tr><td colspan='2'><b>Format Field Separator: </b>
<input type='radio' name='fsseparator' value='restore' checked='true' /> Tab    
<input type='radio' name='fsseparator' id='specify' value='specify' /> Specify (\\t - default): 
<input type='text' name='fsseparatorspecify' size='2' value=\"\t\" onchange=\"document.forms['sqlupload'].elements['specify'].checked=true;\" /></td></tr>
<tr><td colspan='2'><b>   Data Field Separator: </b>(default is tab) 
<input type='text' name='separator' size='2' value='	' /></td></tr>
<tr><td colspan='2'><b>   Newline </b><input type='radio' name='newline' value='Linux' checked='true' /> Linux \\n (default)  
		  <input type='radio' name='newline' value='Windows' /> Windows \\r\\n  
		  <input type='radio' name='newline' value='Mac' /> Mac \\r  <br /><br /></td></tr>
<tr><td><b>Format String </b><br />
<input type='file' id='loadFormatString' name='loadFormatString' size='1' /></td>
<td><textarea name='format' rows='4' cols='100'>{$_POST['format']}</textarea></td></tr>
<tr><td><b>CSV Data</b><br /><input type='file' id='loadData' name='loadData' size='1' /></td>
<td><textarea name='data' rows='8' cols='100'>{$_POST['data']}</textarea></td></tr>
<tr><td><b>Output Window</b></td>
<td><textarea name='output' rows='8' cols='100'>{$outputbuffer}</textarea></td></tr>
<td colspan='2'><input type='submit' value='Upload' /><input type='reset' value='Clear' onclick='document.forms[\"sqlupload\"].elements[\"format\"].value = \"\"; document.forms[\"sqlupload\"].elements[\"data\"].value = \"\"; document.forms[\"sqlupload\"].elements[\"output\"].value = \"\";' />
</td></tr></tbody></table>
</form></div>";
//Script to enable text file upload
echo "<script type='text/javascript'>
  function readFormatFile(evt) {
    var f = evt.target.files[0]; 
    if (f) {
      var r = new FileReader();
      r.onload = function(e) { 
	      var contents = e.target.result;
	      document.forms['sqlupload'].elements['format'].value = contents;
      }
      r.readAsText(f);
    } else { 
      alert('Failed to load file');
    }
  }
  function readDataFile(evt) {
    var f = evt.target.files[0]; 
    if (f) {
      var r = new FileReader();
      r.onload = function(e) { 
	      var contents = e.target.result;
	      document.forms['sqlupload'].elements['data'].value = contents;
      }
      r.readAsText(f);
    } else { 
      alert('Failed to load file');
    }
  }
  document.getElementById('loadFormatString').addEventListener('change', readFormatFile, false);
  document.getElementById('loadData').addEventListener('change', readDataFile, false);
</script>";
if (isset($cms) && ($cms == "wp"))
    mysqli_close($con_wp);
$con = $con_gazebo;
include 'gazebo-footer.php';
?>
