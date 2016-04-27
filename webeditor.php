<?php $pagename="webeditor";
require 'gazebo-header.php';
?>

<?php
//Generate image list
/*
    $file=fopen("{$rootdir}{$imagedir}image_list.js", "w+") or debugText("Unable to open file <i>image_list.js</i> for writing.<br />");
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
*/
?>
<title>
Web Editor
</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo $maindir; ?>tinymce/tinymce.min.js"></script>
<script type="text/javascript">

tinymce.init({
    selector: ".editor",
    theme: "modern",
    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern imagetools"
    ],
    toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    toolbar2: "print preview media | forecolor backcolor emoticons",
    image_advtab: true,
    templates: [
        {title: 'Test template 1', content: 'Test 1'},
        {title: 'Test template 2', content: 'Test 2'}
    ],
    height: "400px"
});
</script>

<!-- /TinyMCE -->

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Website Content Editor</h2>

<?php

require 'authcheck.php';

echo "<div style='text-align:center'>";
if ( isset( $_POST['elm1'] ) )
{
    $criteria = $rootdir . $webcontentdir . $_POST['filename'] . "-content.html";
    debugText( "Criteria: " . $criteria . "<br />");
    $file=fopen($criteria, "w+") or die("Unable to save file <i>" . $_POST['filename'] . "-content.html</i><br />");
    fwrite($file, $_POST['elm1']);
    fclose($file);
    echo "<p><b>File {$_POST['filename']}-content.html saved.</b></p>";
    $_POST['filelist'] = $_POST['filename'];
    $buffer = $_POST['elm1'];
}

/* Perform file operations */
switch ( $_POST['fn'] )
{
    case 'new':
    {
	break;
    }
    case 'delete':
    {
	if ( unlink($rootdir . $webcontentdir . $_POST['filelist'] . '-content.html') )
	    echo "File <i>" . $_POST['filelist'] . "-content.html</i> deleted successfully.<br />";
	else
	    echo "File <i>" . $_POST['filelist'] . "-content.html</i> not deleted.<br />";
	break;
    }
    case 'load':
    {
	$file=fopen($rootdir . $webcontentdir . $_POST['filelist'] . "-content.html", "r") or die("Unable to open file " . $_POST['filelist'] . "-content.html");
	$buffer = "";

	for ( $i=0; !feof($file); $i++ )
	{
	    $text = fgets($file);
	    $buffer .= $text;
	}
	fclose($file);
	break;
    }
    default:
}

echo "<table class='criteria' style='height:100%; width:100%'><tbody><tr><td>
<form style='text-align:center' name='selector' method='post' action='" . pageLink("webeditor") . "' >
<select name='filelist' size='12' required='required'>
<option value='Enter new name' name='new' onclick='document.forms[\"selector\"].elements[\"fn_new\"].checked=true'>[New page]</option> 
";
/* Load list of forms */
$files = glob($rootdir . $webcontentdir . "*-content.html");

for ( $i = 0; $i < count($files); $i++ )
{
    $extpos = strpos($files[$i], "-content.html");
    $noext = substr($files[$i], strlen($rootdir . $webcontentdir), $extpos - strlen($rootdir . $webcontentdir)); 
    echo "<option value='{$noext}' onclick='document.forms[\"selector\"].elements[\"fn_load\"].checked=true' ondblclick='document.forms[\"selector\"].submit();'>{$noext}</option>";
}

echo "</select><br />
<input type='radio' id='fn_new' name='fn' value='new' required='required' onclick='document.forms[\"selector\"].elements[\"filelist\"].selectedIndex=0' />New Page  <br />
<input type='radio' id='fn_load' name='fn' value='load' required='required' />Load Page  <br />
<input type='radio' id='fn_delete' name='fn' value='delete' required='required' />Delete Page  <br />
<input type='submit' name='go' value='Go' />
</form></td><td>";

/* TinyMCE editor */
echo "<form method='post' name='editor' action='" . pageLink("webeditor") . "'>
	<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
	<b>Filename: </b><input type='text' size='25' id='filename' name='filename' value='{$_POST['filelist']}' required='required' onclick='if (this.value == \"Enter page name\") this.value = \"\"' />
	<textarea class='editor' id='elm1' name='elm1' rows='40' cols='120' style='height:100%; width:100%'>
	{$buffer}
	</textarea>
	<br />
	<input type='submit' name='save' value='Save' />
	<input type='reset' name='reset' value='Reset' />
</form></td></tr></tbody></table></div>";
include 'gazebo-footer.php';
?>
