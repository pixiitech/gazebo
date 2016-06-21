<?php $pagename = "securefilemgr";
require 'gazebo-header.php';
?>

<?php include 'menu.php'; ?>

<?php
require 'authcheck.php';

echo "<p><h3 style='text-align:center'>Secure File Management</h3>";
echo "<p><i>Upload files to be stored securely in the database with individual privilege levels.</i></p>";
/* Upload or Delete File */

//Upload File
$php_errors = array(1 => 'Maximum file size in php.ini exceeded',
			2 => 'Maximum file size in HTML form exceeded',
			3 => 'Only part of the file was uploaded',
			4 => 'No file was selected to upload.');

$extension = end(explode(".", $_FILES["file"]["name"]));
if ($_FILES['Pic']['type'] != "")
{
  	if ($_FILES["Pic"]["error"] > 0) {
    		errorMessage( "File Upload: error code " . $php_errors[$_FILES["Pic"]["error"]] . "<br>", 3);
		break;
	}
    	else {
    	    echo "File: " . $_FILES["Pic"]["name"] . "  ";
    	    echo "Type: " . $_FILES["Pic"]["type"] . "  ";
    	    echo "Size: " . ($_FILES["Pic"]["size"] / 1024) . " kB  <br />";
    	    if (secureFileExists($_FILES["Pic"]["name"], $con)) {
		echo $_FILES["Pic"]["name"] . " already exists. Please use a different file name";
      	    }
    	    else {
		//Create metadata entry
		$thefile = $_FILES['Pic'];
		$insertstring = sprintf("INSERT INTO SecureFileMeta (Filename, Minlevel, Size, MIME, Behavior, Description) VALUES ('%s', '%s', %d, '%s', '%s', '%s')",
				mysqli_real_escape_string($con, $thefile['name']),
				mysqli_real_escape_string($con, fetchSetting('WPPostDefaultMinLevel', $con)),
				mysqli_real_escape_string($con, $thefile['size']),
				mysqli_real_escape_string($con, $thefile['type']),
				mysqli_real_escape_string($con, fetchSetting('SecureFileDefaultBehavior', $con)),
				mysqli_real_escape_string($con, $_POST['newfile-description']));
		$result = mysqli_query($con, $insertstring);

		//Recover index
		$querystring = "SELECT Idx FROM SecureFileMeta WHERE Filename = '" . mysqli_real_escape_string($con, $thefile['name']) . "'";
		$result = mysqli_query($con, $querystring);
		$newrec = mysqli_fetch_array($result);
		$newidx = $newrec['Idx'];

		//Upload file contents
		$insertstring = sprintf("INSERT INTO SecureFileData (Idx, Data) VALUES (%d, '%s')",
					$newidx, mysqli_real_escape_string($con, file_get_contents($thefile['tmp_name'])));
		$result = mysqli_query($con, $insertstring);
		if ( $result ) {
		    echo "File Uploaded Successfully.<br />";
		}
	    }
	}
}

//Update info and delete
$querystring = "SELECT * FROM SecureFileMeta";
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) ) {
    //Update Minlevel
    if ( isset($_POST['minlevel-' . $row['Idx']] ) && ($row['Minlevel'] != $_POST['minlevel-' . $row['Idx']] )) {
	$querystring2 = "UPDATE SecureFileMeta SET Minlevel = {$_POST['minlevel-' . $row['Idx']]} WHERE Idx = {$row['Idx']}";
	$result2 = mysqli_query($con, $querystring2);
	if ( $result2 ) {
	    echo "Updated security level of file {$row['Filename']} to {$_POST['minlevel-' . $row['Idx']]}.<br />";
	}
    }

    //Update behavior
    if ( isset($_POST['behavior-' . $row['Idx']] ) && ($row['Behavior'] != $_POST['behavior-' . $row['Idx']] )) {
	$querystring2 = "UPDATE SecureFileMeta SET Behavior = '{$_POST['behavior-' . $row['Idx']]}' WHERE Idx = {$row['Idx']}";
	$result2 = mysqli_query($con, $querystring2);
	if ( $result2 ) {
	    echo "Updated behavior of file {$row['Filename']} to {$_POST['behavior-' . $row['Idx']]}.<br />";
	}
    }

    //Update description
    if ( isset($_POST['description-' . $row['Idx']] ) && ($row['Description'] != $_POST['description-' . $row['Idx']] )) {
	$querystring2 = "UPDATE SecureFileMeta SET Description = '{$_POST['description-' . $row['Idx']]}' WHERE Idx = {$row['Idx']}";
	$result2 = mysqli_query($con, $querystring2);
	if ( $result2 ) {
	    echo "Updated description of file {$row['Filename']} to {$_POST['description-' . $row['Idx']]}.<br />";
	}
    }

    //Delete File
    if ( isset($_POST['delete-' . $row['Idx']] )) {
	$idx = $row['Idx'];
	$filename = $row['Filename'];
	$querystring2 = "DELETE FROM SecureFileMeta WHERE Idx = {$idx}";
	$result2 = mysqli_query($con, $querystring2);
	$querystring2 = "DELETE FROM SecureFileData WHERE Idx = {$idx}";
	$result2 = mysqli_query($con, $querystring2);
	if ( $result2 ) {
	    echo "Deleted file {$filename}.<br />";
	}
    }

}

/* Display file manager */
echo "<div style='text-align:center'><form id='filemgr' name='filemgr' method='post' action='" . pageLink("securefilemgr") . "' enctype=\"multipart/form-data\" >
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"{$max_upload_size}\" />
<input type='hidden' id='fn' name='fn' value='' />
<input type='hidden' id='fname' name='fname' value='' />
<input type='hidden' id='preview' name='preview' />";

echo "<table><tr><td style='width:75%'>";

echo "Upload file:&nbsp;<input id='Pic' type='file' name='Pic' size='30' />&nbsp;";
echo "<input id='newfile-description' type='text' name='newfile-description' size='30' value='Enter Description'
		onclick='this.value = \"\";' />&nbsp;";
echo "<input type='submit' value='Upload' />";

echo "<table border='1' style='margin: 0px auto' class='sortable tablesorter'><thead>";
echo "<tr><th>File</th><th>Size</th><th>Options</th><th>Link Code</th><th>Delete</th></tr></thead><tbody>";

/* Display Files */
$querystring = "SELECT * FROM SecureFileMeta";
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) )
{
    $link = pageLink("securefile", "Idx={$row['Idx']}");
    echo "<tr><td>{$row['Filename']}<br />";
    echo "<a href='{$link}'>download</a>&nbsp;";
    echo "<a onclick='document.forms[\"filemgr\"].elements[\"preview\"].value = \"{$row['Idx']}\";
					 document.forms[\"filemgr\"].submit();'>preview</a><br />";
    echo "Description:<input name='description-{$row['Idx']}' size='25' value='{$row['Description']}' />";
    echo "</td><td>";
    $size = intval($row['Size'] / 1024);
    echo "<span hidden='hidden'>" . padInt($size, 10) . " </span>"; //for sorting purposes
    if ( $size < 1000 ) {
	echo $size . ' kB';
    }
    else {
        echo intval($size / 1024) . ' MB';
    }
    echo "</td>";
    echo "<td>Security Lvl:<select name='minlevel-{$row['Idx']}'>";
    for ( $i = 0; $i < count($levels); $i++ ) {
	echo "<option value='{$i}'";
	if ( $row['Minlevel'] == $i ) {
	    echo " selected='selected'";
	}
	if ( $i == 0 ) {
	    echo ">Public</option>";
	}
	else {
	    echo ">{$levels[$i]}</option>";
	}
    }
    echo "</select><br />";
    echo "Behavior:<select name='behavior-{$row['Idx']}'>";

	echo "<option value='download'";
	if ( $row['Behavior'] == 'download' ) {
	    echo " selected='selected'";
	}
	echo ">Download</option>";
	echo "<option value='display'";
	if ( $row['Behavior'] == 'display' ) {
	    echo " selected='selected'";
	}
	echo ">Display</option>";

    echo "</select></td>";
    echo "<td><textarea style='height:60px; width:120px'><a href='{$link}'>{$row['Description']}</a></textarea></td>"; 
    echo "<td><input type='checkbox' name='delete-{$row['Idx']}' /></td>";
    echo "</tr>";
}
echo "</tbody>";
echo "<tfoot><td colspan='5'><span class='center'><input type='submit' value='Save Settings' /></span></td></tfoot></table><br />";
echo "</td><td style='width:25%'>";
echo "<h3>Preview:</h3>";
if ( isset($_POST['preview']) ) {
    $querystring = "SELECT * FROM SecureFileMeta WHERE Idx = {$_POST['preview']}";
    debugText($querystring);
    $result = mysqli_query($con, $querystring);
    $row = mysqli_fetch_array($result);
    $mime = $row['MIME'];
    debugText("MIME:{$row['MIME']} Size:{$row['Size']}");
    if ( isset( $cms )) {
        kses_remove_filters();
    }
    try {
        $querystring = "SELECT * FROM SecureFileData WHERE Idx = {$_POST['preview']}";
        $result = mysqli_query($con, $querystring);
        $row = mysqli_fetch_array($result);
        if ( strtolower(substr( $mime, 0, 5 )) == 'image' ) {
            echo "<img src='data:{$mime};base64," . base64_encode($row['Data']) . "' style='height:200px; width:200px' />";
        }
	else if ( strtolower(substr( $mime, 0, 4 )) == 'text' ) {
	    echo $row['Data'];
	}
	else {
	    echo "<b>This file cannot be previewed.</b>";
	}
    } catch (Exception $exc) {
	handle_error("Something went wrong loading the image.", "Error loading image: " . $exc->getMessage());
    }
    if ( isset( $cms )) {
        kses_init_filters();
    }
}
//echo "<img id='viewImage' name='viewImage' src='{$_POST['file']}' style='overflow:auto' hidden='hidden' /><br />";
//echo "<div id='viewText' name='viewText' style='overflow:auto' />";
echo "</form></div>";
echo "</td></tr></table>";

include 'gazebo-footer.php';
?>
