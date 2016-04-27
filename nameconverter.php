<?php 
global $pagename;
$pagename = "nameconverter";
require 'gazebo-header.php'; 
?>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Name Converter</h2>

<?php
require 'authcheck.php';

if ( $_POST['submitted'] == 'yes' ) {
    $updated = 0;
    $notupdated = 0;
    $querystring = "SELECT Idx, {$_POST['firstlast']}, {$_POST['firstlast']}2 FROM Residents";
    debugText($querystring);
    $result = mysql_query($querystring, $con);
    while ( $row = mysql_fetch_array($result) ) {

	$commapos = strpos($row[$_POST['firstlast']], ',');
	if ( $commapos == false ) {
	    $notupdated++;
	}
	else {
	    $first = trim(substr($row[$_POST['firstlast']], $commapos + 1));
	    $last = trim(substr($row[$_POST['firstlast']], 0, $commapos));
	    echo $row[$_POST['firstlast']];
	    echo " => " . $first . " " . $last;
	    if ( $_POST['commit'] == 'commit' ) {
	        $querystring = "UPDATE Residents SET FirstName = '{$first}', LastName = '{$last}' WHERE Idx = {$row['Idx']}";
	        $result2 = mysql_query($querystring, $con);
	        echo " -- UPDATED";
	        $updated++;
	    }
	    echo "<br />";
	}

	$commapos = strpos($row[$_POST['firstlast'] . '2'], ',');
	if ( $commapos == false ) {
	    $notupdated++;
	}
	else {
	    $first2 = trim(substr($row[$_POST['firstlast'] . '2'], $commapos + 1));
	    $last2 = trim(substr($row[$_POST['firstlast'] . '2'], 0, $commapos));
	    echo $row[$_POST['firstlast'] . '2'];
	    echo " => " . $first2 . " " . $last2;
	    if ( $_POST['commit'] == 'commit' ) {
	        $querystring = "UPDATE Residents SET FirstName2 = '{$first2}', LastName2 = '{$last2}' WHERE Idx = {$row['Idx']}";
	        $result2 = mysql_query($querystring, $con);
	        echo " -- UPDATED";
	        $updated++;
	    }
	    echo "<br />";
	}
    }
    echo "<i>{$updated} record(s) updated. {$notupdated} record(s) not updated.</i><br />";
}

echo "<div class='recordinput'>
<h3 style='text-align:center'><i>Be Sure to Create a Backup First!</i></h3>
<form name='convertform' method='post' style='text-align:center' action='" . pageLink("nameconverter") . "'>
<input type='hidden' name='submitted' value='yes' />
<br />
<input type='radio' name='firstlast' value='FirstName'>&nbsp;Use First Name Field&nbsp;&nbsp;
<input type='radio' name='firstlast' value='LastName'>&nbsp;Use Last Name Field&nbsp;&nbsp;
<input type='radio' name='firstlast' value='Name'>&nbsp;Name Field (< 0.7.1)&nbsp;&nbsp;
<br /><input type='radio' name='commit' value='display'>&nbsp;Display Changes only&nbsp;&nbsp;
<input type='radio' name='commit' value='commit'>&nbsp;Commit Changes&nbsp;&nbsp;<br />
<input type='submit' value='SUBMIT' />
</form>
";

include 'gazebo-footer.php';
?>
