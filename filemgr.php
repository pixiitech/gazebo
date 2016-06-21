<?php $pagename = "filemgr";
require 'gazebo-header.php';
?>

<script>
function delFile(fname)
{
    var ok = window.confirm("Are you sure you want to delete " + fname + "?");
    if ( ok == true )
    {
	document.forms['filemgr'].elements['fn'].value='delete'; 
	document.forms['filemgr'].elements['fname'].value=fname; 
	document.getElementById('filemgr').submit();
    }
}

</script>
<?php include 'menu.php'; ?>

<?php
require 'authcheck.php';

if (!isset($_POST['file'])) {
    $_POST['file'] = "";
}
function uploadPic()
{
	require "config.php";
	//Save Picture
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	$extension = end(explode(".", $_FILES["file"]["name"]));
	if ($_FILES['Pic']['type'] != "")
	{
  			if ($_FILES["Pic"]["error"] > 0)
    			{
    			   echo "File Upload: error code " . $_FILES["Pic"]["error"] . "<br>";
			   break;
			}
    			else
    			{
    		    		echo "Upload: " . $_FILES["Pic"]["name"] . "<br>";
    		    		echo "Type: " . $_FILES["Pic"]["type"] . "<br>";
    		    		echo "Size: " . ($_FILES["Pic"]["size"] / 1024) . " kB<br>";
    		    		echo "Temp file: " . $_FILES["Pic"]["tmp_name"] . "<br>";
    		    		if (file_exists($_POST['directory'] . $_FILES["Pic"]["name"]))
      		    		{
      					echo $_FILES["Pic"]["name"] . " already exists. ";
					break;
      		   		}
    		    		else
      		    		{
					$success = move_uploaded_file($_FILES["Pic"]["tmp_name"], $_POST['directory'] . $_FILES["Pic"]["name"]);
					if ( $success )
      					    echo "Stored in: " . $_POST['directory'] . $_FILES["Pic"]["name"] . "<br />";
					else
					{
    			   		    echo "move_uploaded_file: error code " . $_FILES["Pic"]["error"] . "<br>";
					    break;
					}
      		    		}
			}
	}
}

echo "<p><h3 style='text-align:center'>File Management</h3>";

/* Upload or Delete File */
if ( isset($_FILES["file"]) )
  uploadPic();
if ( isset($_POST['fn']) && ($_POST['fn'] == 'delete') )
{
    unlink($_POST['fname']);
    echo "File {$_POST['fname']} deleted.<br />";
}

/* Display file manager */
echo "<div style='text-align:center'><form id='filemgr' name='filemgr' method='post' action='" . pageLink("filemgr") . "' enctype=\"multipart/form-data\" >
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"{$max_upload_size}\" />
<input type='hidden' id='fn' name='fn' value='' />
<input type='hidden' id='fname' name='fname' value='' />
<input type='hidden' id='directory' name='directory' value='' /><br /> ";

if ( !isset($_POST['directory']) || ($_POST['directory'] == ''))
{
    echo "<script>document.forms['filemgr'].elements['directory'].value = '{$rootdir}';</script>";
    $_POST['directory'] = $rootdir;
}
else
    echo "<script>document.forms['filemgr'].elements['directory'].value = '{$_POST['directory']}';</script>";

echo "<p>Directory: {$_POST['directory']}</p>";
echo "<table><tr><td style='width:50%'>";
echo "Upload file:<input id='Pic' type='file' name='Pic' size='30' />&nbsp;&nbsp;";
echo "<input type='submit' value='Upload' />";
echo "<table border='1' style='margin: 0px auto'><tbody>";
echo "<tr><th>Filename</th><th>Size</th><th>Delete</th></tr>";

/* Up-directory link */
if ($_POST['directory'] != $rootdir)
{
    $pos = strrpos(substr($_POST['directory'], 0, strlen($_POST['directory']) - 1), '/');
    $updir = substr($_POST['directory'], 0, $pos);
    echo "<tr><td colspan='3'><a onclick=\"document.forms['filemgr'].elements['directory'].value = '{$updir}/'; document.getElementById('filemgr').submit();\">[..]</a></td></tr>";
}

/* Display Directories */
$dirs = glob($_POST['directory'] . "*", GLOB_ONLYDIR);
for ( $i = 0; $i < count($dirs); $i++ )
{

    $nopath = substr($dirs[$i], strlen($_POST['directory'])); 
    echo "<tr><td colspan='3'><a onclick=\"document.forms['filemgr'].elements['directory'].value = '{$dirs[$i]}/'; document.getElementById('filemgr').submit();\">[{$nopath}]</a></td></tr>";
}

/* Display Files */
$files = glob($_POST['directory'] . "*");
for ( $i = 0; $i < count($files); $i++ )
{
    if ( !is_file($files[$i]) )
	continue;
    $nopath = substr($files[$i], strlen($_POST['directory']));
    $webpath = substr($files[$i], strlen($rootdir));
    $ext = substr($files[$i], strrpos($files[$i], '.') + 1);
    echo "<tr><td><a ";
    if (( $ext == 'gif' ) || ( $ext == 'jpeg' ) || ( $ext == 'jpg' ) || ( $ext == 'png' ))
	echo "onclick=\"document.getElementById('viewImage').src='{$webpath}';
			document.getElementById('viewImage').hidden=false; \"";
    else if (( $ext == 'txt' ) || ( $ext == 'htm' ) || ( $ext == 'php' ) || ( $ext == 'css' ))
	echo "onclick=\"$('#viewText').load('{$webpath}');
			document.getElementById('viewText').hidden=false; \"";
    else
	echo "href='{$webpath}' target='_blank'";
    echo ">{$nopath}</a></td>";
    echo "<td>" . filesize($files[$i]) . "</td>";
    echo "<td><a onclick=\"delFile('{$files[$i]}');\">X</a></td></tr>";
}

echo "</tbody></table><br />";
echo "</td><td style='width:50%'>";
echo "<h3>Preview:</h3>";
echo "<img id='viewImage' name='viewImage' src='{$_POST['file']}' style='overflow:auto' hidden='hidden' /><br />";
echo "<div id='viewText' name='viewText' style='overflow:auto' />";
echo "</form></div>";
echo "</td></tr></table>";

include 'gazebo-footer.php';
?>
