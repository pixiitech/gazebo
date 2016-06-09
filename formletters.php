<?php 
$pagename = "formletters";
require 'gazebo-header.php';

foreach(['fn'] as $key) {
    if (!isset($_POST[$key])) {
        $_POST[$key] = "";
    }
}
?>
<title>
Form Letters
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
<!-- Form letter scripts -->
<script type="text/javascript">
function fillInCustomTextBox() {
    var controlName = window.prompt("Enter custom text field name:");
    controlName = controlName.replace(/ |'/g, "");
    var defaultText = window.prompt("Enter default text in box, or leave blank.");
    return "<input type='text' name='" + controlName + "' value='" + defaultText + "' />";
}
</script><script>
function fillInCustomCheckBox() {
    var Name = window.prompt("Enter custom check box name:");
    controlName = Name.replace(/ |'/g, "");
    return "<input type='checkbox' name='" + controlName + "' /> " + Name + "  ";
}

</script><script>
function fillInCustomRadio() {
    var controlName = window.prompt("Enter custom radio field name:");
    controlName = controlName.replace(/ |'/g, "");
    var i = 1;
    var output = "";
    do {
	var option = window.prompt("Enter option #" + i + " text or leave blank and click OK to end:");
	if ( option == "" ) {
	    break;
	}
	output += "<input type='radio' name='" + controlName + "' value='" + option.replace(/ |'/g, "") + "' /> " + option + "  ";
	i++;
    } while ( option != "" );
    return output;
}

function fillInYesNoRadio() {
    var controlName = window.prompt("Enter yes/no radio field name:");
    controlName = controlName.replace(/ |'/g, "");
    return "<input type='radio' name='" + controlName + "' value='Yes' />Yes&nbsp;&nbsp;
			<input type='radio' name='" + controlName + "' value='No' />No";
}

</script>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Forms - Template Editor</h2>

<?php

require 'authcheck.php';

echo "<div style='text-align:top'>";

/* Save Form */

if ( isset( $_POST['elm1'] ) )
{
    if ( $_POST['Idx'] == '' ) {
        $querystring = "INSERT INTO Forms (Title, Text, Type, Email) VALUES (";
        $querystring .= "\"{$_POST['Title']}\", ";
        $querystring .= "\"{$_POST['elm1']}\", ";
        $querystring .= $_POST['Type'] . ", ";
	$querystring .= "\"{$_POST['Email']}\")"; 
	debugText($querystring);
        $_POST['formlist'] = $_POST['Title'];
        $buffer = $_POST['elm1'];
	$result = mysqli_query($con, $querystring);
	if ( $result ) {
	    echo "Form <i>{$_POST['Title']}</i> created.<br />";
	}
	else {
	    echo "Form <i>{$_POST['Title']}</i> failed to create.<br />";
	}
    }
    else {
	$querystring = "UPDATE Forms SET Title = \"{$_POST['Title']}\", Text = \"";
	$querystring .= $_POST['elm1'];
	$querystring .= "\", Type = {$_POST['Type']}, Email = \"{$_POST['Email']}\" WHERE Idx = {$_POST['Idx']}";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result ) {
	    echo "Form <i>{$_POST['Title']}</i> updated..<br />";
	}
	else {
	    echo "Form <i>{$_POST['Title']}</i> failed to update.<br />";
	}
        $buffer = $_POST['elm1'];
    }
    $querystring = "SELECT Idx From Forms WHERE Title = '{$_POST['Title']}'";
    debugText($querystring);
    $result = mysqli_query($con, $querystring);
    $row = mysqli_fetch_array($result);
    $_POST['fn'] = "load";
    $_POST['formlist'] = $row['Idx'];
}

/* New Form, Delete Form, Load Form */

switch ( $_POST['fn'] )
{
    case 'new':
    {
	break;
    }
    case 'delete':
    {
	$querystring = "DELETE FROM Forms WHERE Idx = {$_POST['formlist']}";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result ) {
	    echo "Form <i>" . $_POST['formlist'] . "</i> deleted successfully.<br />";
	}
	else {
	    echo "Form <i>" . $_POST['formlist'] . "</i> failed to delete.<br />";
	}
	break;
    }
    case 'load':
    {
	$querystring = "SELECT * FROM Forms WHERE Idx = {$_POST['formlist']}";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $row = mysqli_fetch_array($result) ) {
		$type = $row['Type'];
		$title = $row['Title'];
		$buffer = $row['Text'];
		$Idx = $_POST['formlist'];
		$email = $row['Email'];
	}
	else {
		echo "Unable to open form.<br />";
		$buffer = "";
	}
	break;
    }
    default:
}

//Default type - 

if ( !isset( $type ) ) {
	$type = 1;
}
echo "<table class='criteria' style='height:100%; width:100%'><tbody><tr><td>";
echo "<form name='formletter' method='post' action='". pageLink("formletters") . "' >";
echo "<br />";
echo "<select name='formlist' size='12' required='required'>
<option value='Enter form name' name='new' onclick='document.forms[\"formletter\"].elements[\"fn_new\"].checked=true;'
					   ondblclick='document.forms[\"formletter\"].submit();'>[New form]</option> ";

/* Load list of forms */

$querystring = "SELECT Idx, Title FROM Forms";
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) ) {
    echo "<option value='{$row['Idx']}' onclick='document.forms[\"formletter\"].elements[\"fn_load\"].checked=true;'
				   ondblclick='document.forms[\"formletter\"].submit();'";
    if ( $row['Idx'] == $_POST['formlist'] ) {
	echo " selected='selected'";
    }
    echo ">{$row['Title']}</option>";
}

echo "</select><br />
<input type='radio' id='fn_new' name='fn' value='new' onclick='document.forms[\"formletter\"].elements[\"formlist\"].selectedIndex=0' />New Form  <br />
<input type='radio' id='fn_load' name='fn' value='load' />Load Form  <br />
<input type='radio' id='fn_delete' name='fn' value='delete' />Delete Form  <br />
<input type='submit' name='go' value='Go' />
</form>
</td><td>";

/* TinyMCE editor */

echo "<form name='editor' method='post' action='". pageLink("formletters") . "'>";
echo "<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
	<b>Form Title: </b>
	<input type='hidden' id='Idx' name='Idx' value='{$Idx}' />
	<input type='hidden' id='Type' name='Type' value='{$type}' />
	<input type='text' size='25' id='Title' name='Title' value='{$title}' onclick='if (this.value == \"Enter form name\") this.value = \"\"' />	
	<input type='submit' name='save' value='Save Form' />
	<input type='reset' name='reset' value='Reset' />
	<textarea class='editor' id='elm1' name='elm1' rows='40' cols='120' style='height:100%; width:100%'>
	{$buffer}
	</textarea><b>Notification Email:</b> <input type='text' id='Email' name='Email' size='50' value='{$email}' />
</form>
</td><td>";

/* List of static field codes */

echo "<b>Insert Static Fields:</b><br />
<form name='tagger' method=''>
<select name='columnlist' size='10'>
<option value='Date:MM/DD/YY' ondblclick=\"document.forms['tagger'].elements['add'].click();\">Date:MM/DD/YY</option>
<option value='Date:MM/DD/YYYY' ondblclick=\"document.forms['tagger'].elements['add'].click();\">Date:MM/DD/YYYY</option>
<option value='Date:Month DDth, YYYY' ondblclick=\"document.forms['tagger'].elements['add'].click();\">Date:Month DDth, YYYY</option>";

$querystring = "SHOW COLUMNS FROM Properties";
debugText($querystring);
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) )
    echo "<option value=\"Properties:{$row['Field']}\" ondblclick=\"document.forms['tagger'].elements['add'].click();\">Properties:{$row['Field']}</option>";
$querystring = "SHOW COLUMNS FROM Residents";
debugText($querystring);
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) )
    echo "<option value=\"Residents:{$row['Field']}\" ondblclick=\"document.forms['tagger'].elements['add'].click();\">Residents:{$row['Field']}</option>";

/*$querystring = "SHOW COLUMNS FROM Violations";
debugText($querystring);
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) )
    echo "<option value=\"Violations:{$row['Field']}\">Violations:{$row['Field']}</option>";
*/
echo "</select><br /><button name='add' value='Add' id='add' onclick=\"tinyMCE.execCommand('mceInsertContent',false, '{' + document.forms['tagger'].elements['columnlist'].options.item(document.forms['tagger'].elements['columnlist'].selectedIndex).value + '}'); return false;\">Add Code</button>";
echo "</form><br />";

/* List of entry boxes */
echo "<b>Insert Entry Boxes:</b><br />
<form name='inputtagger' method=''>
<select name='inputboxlist' size='10' style='width:100%'>";

echo "<option value=\"<input type='text' name='Name' size='30' id='Name' onclick='this.clear();/>\">Name Box</option>";
echo "<option value=\"<input type='text' name='Signature' size='30' id='Signature' required='required'  onclick='this.clear(); />\">Signature Box</option>";
echo "<option value=\"
<input type='text' name='DateMM' size='3' id='DateMM' onclick='this.clear();' />&nbsp;/&nbsp;
<input type='text' name='DateDD' size='3' id='DateDD' onclick='this.clear();' />&nbsp;/&nbsp;
<input type='text' name='DateYY' size='4' id='DateYYYY' onclick='this.clear();' />\">Date Box</option>";
echo "<option onclick='this.value = fillInCustomTextBox(); document.forms[\"inputtagger\"].elements[\"add\"].click();' />Custom Text Box</option>";
echo "<option onclick='this.value = fillInYesNoRadio(); document.forms[\"inputtagger\"].elements[\"add\"].click();' />Yes/No Radio Option</option>";
echo "<option onclick='this.value = fillInCustomRadio(); document.forms[\"inputtagger\"].elements[\"add\"].click();' />Custom Radio Options</option>";

echo "<option onclick='this.value = fillInCustomCheckBox(); document.forms[\"inputtagger\"].elements[\"add\"].click();' />Custom Checkbox</option>";
echo "</select><br /><button name='add' id='add' value='Add' onclick=\"tinyMCE.execCommand('mceInsertContent',false, document.forms['inputtagger'].elements['inputboxlist'].options.item(document.forms['inputtagger'].elements['inputboxlist'].selectedIndex).value); return false;\">Add Entry Box</button>";
echo "</form>";


echo "</td></tr></tbody></table></form></div>";

include 'gazebo-footer.php';
?>
