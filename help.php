<?php $pagename = "help";
require "gazebo-header.php";
?>

<?php

echo "<h3 style='text-align:center'><img src='{$gazebo_imagedir}help.png' />&nbsp;&nbsp;Gazebo Help</h3>";

require 'authcheck.php';

/* $helptext - A hierarchical array of help text items */
/* Help text items are structured as follows:
	array( level, subject, text, modulename ) */
$helptext = 
array( array( 0, "I. Logging into the site", "<p>Visit the website and log in. Once logged in, the site should look the same except with a 'Logged in: (username)' at the top and four links next to that (if you are an administrator): Logout, Wordpress Admin, Gazebo Admin, Profile.</p>

<p>Wordpress Admin is for modifying the site pages, and Gazebo admin is for managing databases and changing site settings.</p>", ""),
	array( 0, "II. Gazebo Admin", "The following explain how to use the various modules of the Gazebo system.", ""),
	array( 1, "A. Home", "Once logged into Gazebo Admin, a second menu bar appears with the Gazebo Modules and you land at the Home page. The Home page is a summary of open work orders and a list of held packages, with links to those respective pages in the body.", "home"),
	array( 1, "B. Residents", "<p>The resident page is for managing residents and their personal information, with the exception of the unit number. The page defaults to List mode. Clicking Search with no parameters entered will also bring up all residents. You may also search by keywords, partial keywords, unit #, owners or tenants only.</p>

<p>Once a search is entered, the results show below. You may click a column heading to sort the results by that heading. To view or update a specific resident, click on their name in the results. Modify the resident info and click 'Update'. To delete, click the X in the right column. The top of the page will ask if you are sure, then click 'Delete' to remove it from the database.</p>

<p>If you click a unit # in the search results, you will leave the Residents page and it will open the unit on the Properties page.</p>

<p>A new resident may be entered by clicking the 'Submit New' option at the top. Enter the new residents information, pick the publish settings and click 'Submit'.</p>

<p>For residents and tenants logged into the system, the resident page is shown as a roster without the Gazebo menu and is read-only. Only published information is shown in the results.", "residents"),
	array( 1, "C. Properties", "<p>The Properties page is for managing units and which resident is associated with them. The page defaults to List mode. Clicking Search with no parameters entered will bring up all properties, or you may search by unit number.</p>

<p>Once a search is entered, the results show below. You may click a column heading to sort the results by that heading. To view or update a specific unit, click on the unit# in the results.  Modify the property info and click 'Update'. To delete, click the X in the right column. The top of the page will ask if you are sure, then click 'Delete' to remove it from the database.</p>

<p>Clicking the owner name from the results will bring that resident in the residents page (you will leave the properties page).</p>

<p>A new unit number may be entered by clicking the 'Submit New' option at the top. Enter the new unit information and click 'Submit'.
Be careful when adding or deleting units as this should only be done in cases of mistakes in the unit list, or changes to the building.</p>", "properties"),
	array( 1, "D. Work Orders", "<p>The work order page is for managing open and closed work orders. The page defaults to List mode. Clicking Search with no parameters entered will bring up all open work orders by default. A search can also be made to search all in process, denied, or completed work orders. </p>

<p>Once a search is entered, the results show below. You may click a column heading to sort the results by that heading. To view or update a specific work order, click the summary name in the results. To delete, click the X in the right column. Once deleted, the work order info is no longer saved so changing the status is preferred.</p>

<p>A new unit number may be entered by clicking the 'Submit New' option at the top. Enter the new work order information and click 'Submit'.</p>

<p>Residents enter their work order requests through the website's 'Submit a Work Order' page which only takes the unit number, name, summary and description.</p>", "workorder"),
	array( 1, "E. Packages", "<p>The packages page is for registering packages that come to the front desk to hold for residents.</p> 
<p>The page defaults to List mode, or clicking Search with no parameters entered will bring up all packages awaiting pickup, by default. A search can also be made to search packages that have already been picked up by unchecking 'Awaiting Pickup Only'. To search specific dates of packages received, enter a start/end day, month and year. </p>
<p>Once a search is entered, the results show below. You may click a column heading to sort the results by that heading. To view or update a specific package status, click the package number in the leftmost column of the results. Modify any information to be changed, and click the 'Update' button at the bottom or press enter. </p><p>To mark a package as picked up by a resident, perform a search. Select the package number in the leftmost column. Select 'Mark Picked-up' at the top and then the 'Mark as Picked-up' button at the bottom or press enter to confirm.</p><p> To delete a package, perform another search. Click the X in the rightmost column of the search results. You will be asked to confirm deletion. Press the 'Delete' button at the bottom or press enter to confirm. Once deleted, the package info is no longer saved so changing the status is preferred to keep a consistent record.</p>
<p>A new package may be entered by clicking the 'Submit New' option at the top. Enter the new package unit #, recipient, carrier and description, and click 'Submit'. An email will be sent to the address on file, if entered, for the resident associated with the Unit number.</p>
", "packages"),
	array( 1, "F. Violations", "<p>The violations page is for keeping track of violations in the community.</p> 
<p>The page defaults to List mode. Clicking Search with no parameters entered will bring up all non-expired violations, by default. A search can also be made to search violations that have expired by checking 'Include Expired'. The Nth violation dropdown box will show all unexpired violations that are the 1st, 2nd, or 3rd violation for that resident.</p>
<p>Once a search is entered, the results show below. You may click a column heading to sort the results by that heading. To view or update a specific violation status, click the package number in the index# column of the results. Modify any information to be changed, and click the 'Update' button at the bottom or press enter. </p><p> To delete a violation, perform another search. Click the garbage can in the leftmost column of the search results. You will be asked to confirm deletion. Press the 'Delete' button at the bottom or press enter to confirm. Once deleted, the violation info is no longer saved in the database.</p>
<p>A new violation may be entered by clicking the 'Submit New' option at the top. Enter the new violation unit #, tag#, violation type and description. An image may also be uploaded using the 'Browse' button. Click 'Submit' to save.</p><p>To print a form letter for a particular violation, click the letter icon in the leftmost column of the violation. The form text page will show with the appropriate letter.</p>", "violations"),
	array( 1, "G. Form Letters", "", "formletters"),
	array( 1, "H. Community", "The following items are part of the Community submenu.", "community"),
	array( 2, "1. Community Info", "This page is where basic community info is entered that may be referenced throughout the site. Modify any items to change and click 'Save'.", "comminfo"), 
	array( 2, "2. Event Calendar", "<p>The event calendar is shown as 'Calendar Management' for administrators and defaults to the current month. By clicking 'Prev' and 'Next' months can be navigated.</p>

<p>To enter a new event, click the day numerals in the calendar spread. The left pane will prompt an icon selection, amenity, title and description. Amenities are not required but are a way of marking that amenity as in use for the day.
Click 'create' to submit the new entry. To modify or delete an entry, click on the event icon in the calendar spread. The info may be modified in the left page, and saved by clicking 'Modify' at the bottom. To delete, click 'Delete'.</p>

<p>Residents and tenants have a similar interface but do not have capabilities of modifying any of the event information.</p>", "calendar"),
	array( 2, "3. Manage Announcements", "<p>The announcements page is where calendar data can be modified in a list format. The page defaults to Search mode. Clicking search with no parameters entered will bring up all events by default. A search can also be made by subject or time.</p>

<p>Once a search is entered, the results show below. You may click a column heading to sort the results by that heading. To view or update a specific event, click the subject name in the results. To delete, click the X in the right column.</p>

<p>A new event entry may be entered by clicking the 'Submit New' option at the top. Enter the new event information (Subject, Description, Icon, Reserved Amenity, Dates and Times), check off which mailing lists you want to be notified, and click 'Submit'.</p>", "announce"),
	array( 2, "4. Amenity Management", "<p>This page opens as a list of all amenities. Simply modify the names that need to be changed, or click 'Add Amenity' to add one. A new row will appear for the new amenity info. Check the delete box of any amenities that need to be deleted. When finished with entry, click on 'Submit'.</p>", "amenitymgr"),
	array( 1, "I. Settings", "The following items are part of the Settings submenu. This includes general site settings and tools.", "settings"),
	array( 2, "1. General Settings", "<p>This page is where basic site settings can be changed. The following settings are included here:</p>
<p><strong>Allow Tenant Registration</strong><br />When set to 'Yes', a Owner/Tenant option will appear on the Register page, giving tenants the option to create a website login.</p>
<p><strong>Default Tenant Login Expiration</strong><br />This option will put an expiration date on all tenant logins created with the specified number of days from registration.</p>
<p><strong>Violation Expiration</strong><br />This option will set the expiration date of all violations entered in the system for purposes of showing in searches by default, and whether the violation is included in tallies of the resident's violations.</p> 	
<p><strong>Lock Publish Name Setting</strong><br />When set the 'Yes', the Publish Name setting will be disabled for all residents, both in the admin pages and in the user's profile page. New users created will have the default setting.</p>
<p><strong>Default Publish Settings</strong><br />These series of settings determine whether new users have each detail published in the roster. If Default Publish Name is not selected here, residents will not show up in the roster by default.</p>   
<p><strong>Show Guest Info</strong><br />This setting determines whether the 'Guest Info' field is shown (both on admin and profile). This is a text box that can be used by residents on their Profile page to enter messages to security staff about their guests. Security and Admin staff see this on the Residents page when a resident is selected (if shown).</p>", "gensettings"),
	array( 2, "2. File Management", "<p>This page is for uploading, viewing, or deleting files within the web server. Use is for diagnostic and troubleshooting only, so please use only with assistance from Pixii Computing. </p><p>A list of files in the home directory will appear. Click the [..] link to go up a directory. Other directory names will appear in brackets. Click a file name to preview images or text (a download popup may occur for unknown file types).</p><p> At the bottom is an upload box. Clicking the browse button will show a dialog to select a file to upload. Then click the upload button once this is selected. The file stats will show at the top once the file is uploaded.</p>", "filemgr"),
	array( 2, "3. Secure File Management", "<p>Use this page to upload, rename, and change settings on your securely uploaded files. These files exist only in the SQL database and cannot be accessed directly through the filesystem.</p><p>To upload a file, click on 'Browse...' and select a file to upload from your computer. Then type a description in the box next to it with 'Enter Description here'. Click on 'Upload'. The file may take a few seconds to a minute to upload depending on its size. The page will reload with a status on the uploaded file and size in kB. </p><p>In the table below, a list of all uploaded files is shown. The filename and description in the leftmost column, along with links to download or preview the file. Preview currently only works for image and text files. The next column is the size. The options box has Security Lvl, the minimum security level required to access the file, and Behavior, which specifies how the file link behaves in the browser. The Link Code box is the HTML code to copy into the site's HTML page editor to create a link to the file (visitors will see the Description). The Delete check box can be checked to delete multiple files. Click on 'Save Settings' at the bottom to save changes when done.</p>", "securefilemgr"),
        array( 2, "4. Email Notification Setup", "This page is for specifying where emails are sent to for various site functions. Simply modify the fields shown and click Submit.<p>Email Blast Default Recipient List - This is a semicolon-separated list of emails that are to be included in the email blast dialog by default.</p>", "notification"),
	array( 2, "5. Login Management", "<p>This page is for adding, removing and updating user logins for the website. Wordpress and Gazebo share a single sign-on database. Passwords can be reset to 12345 by checking 'Reset'. Access levels are also modified here. Resident names can be changed by clicking 'Select' and a popup window will show. Type in a partial name and click search to find that resident. Highlight the resident and click 'Select' to fill that resident name in the Login Management window. </p><p>Expiration dates can also be modified, leave blank for users that do not expire. Check the 'Delete' check box to mark for deletion. To add a user, click the 'Add User' button at the bottom. A new line will appear for entering the new user's information.
 Once all changes are entered, click on 'Save' to make the changes in the database.</p>", "loginmgr"),
	array( 2, "6. Backup and Restore", "This page is for backing up, restoring, and uploading mass data to and from the database.", "backup"),
	array( 3, "i. Backup", "Select all databases to be backed up from the list of checkboxes. Select 'All' to perform a complete backup. Click 'Backup'. At the bottom of the page, the output window will show the backup status.", ""),
	array( 3, "ii. Restore", "At this time, the Restore page can restore one database at a time to a previous backed up state. Select the database to be restored from the drop-down list, and click 'Restore'. The output window at the bottom of the page will show the status of the restore operation.", ""),
	array( 3, "iii. Mass Data", "<p>This section requires CSV data to be uploaded. Only use data that is delimited by tab (\\t) or another symbol that does not appear in the data. Select a database to upload your data into. You may specify separators for both format and data fields. Specify what type of newlines will appear in your data (typically use Linux). </p>

<p>The format string will describe what SQL fields are being imported into the database. Example:<br />
<span style='font-family:courier'>	Name	Email	MailingAddress	Type</span></p>

<p>The data follows the format of the format string. Each record is separated by a newline. Example:<br />
<span style='font-family:courier'>	Smith, John	jsmith@website.com	123 Elm St.	0<br />
	Doe, Jane	jdoe@internet.com	248 Oak Ln.	1</span></p>

<p>You may paste data into the fields, or use the Browse button to import a file from your device directly. Click 'Upload' and the results will show in the output window.</p>", ""),
	array( 1, "J. Profile", "<p>This page is for changing user-specific settings for your account.</p>

<p>The Display section is where a color scheme can be picked, however 'No Style' ensures that all pages will display with the Wordpress fonts and colors and will work best on all displays.</p>

<p>To change your password, type the current password and the new password twice.</p>

<p>If the account logged in is associated with a resident:</p>
Other personal information can be updated on this page, however your username cannot be changed without recreating the account. Publish settings define whether the information is published in the Resident roster (it can still be seen by administrators). Guest information is a way to enter information for security to look at when guests arrive. (if shown by your site's settings)", "profile"),
	array( 0, "III. Wordpress Admin", "To access Wordpress and use the page/post editors, click on Wordpress Admin at the upper right of the page.", ""),
	array( 1, "A. Posts", "This section explains how to edit and update posts in Wordpress. Help may also be accessed from within WordPress by clicking on 'Help' in the upper right while in Wordpress Admin.", ""),
	array( 2, "1. Editing an existing post", "<p>To view existing posts, click on Posts on the left sidebar. Posts here can be modified by clicking on their title. That will take you to the 'Edit Post' screen. From this page, the title and text of the post can be edited. There are two tabs of the text window: Visual and Text. When in the visual tab, the text is shown as is will be on the website. The Text tab is where the actual HTML can be edited. On the right is the Categories list. Posts may belong to multiple categories.</p><p>
    Manager's Corner - Posts will show on the main page in the 'Manager's Corner' box.<br />
    Home Page Slider - These posts contain images only and are for the Home Page slideshow.<br /></p>

<p>The Featured Image box is where an image can be set for the 'Home Page Slider' posts. These images must be 1500 x 551 pixels exactly to match the existing images.</p>

<p>To create a link: Select the text you want to turn into a hyperlink. Click the button that looks like a chain link in the toolbar, and a popup window will ask you where to link it to. Type the URL, title (hover text) and the other link settings. You may also search an existing page or post from the list at the bottom of the window, and the interface will fill in the link automatically. Click ok when complete.</p>

<p>To remove a link: Select the existing link and click the broken chain link in the toolbar to remove it.</p>

<p>Adding media: Click the 'Add Media' button. A popup window will show existing media items. You may filter the media items with the drop down boxes, or type a search term. Clicking 'Upload' will allow you to upload a new media item to the site. Once the media is selected, click 'Insert into Post' to embed it into the post.</p>

<p>Deleting media: Click on the picture or document and press delete. Backspace can also be used if the cursor is after the item.</p>

<p>When editing is complete, click the 'Update' button in the Publish box. Sending an email blast - see (3)</p>", ""),
	array( 2, "2. Creating new post", "Under Posts on the left sidebar, there is an option to Add New. Click this takes you to a similar page to create a new post. Enter the title and text of the post, select a category, and Publish. Sending an email blast - see (3)", ""),
	array( 2, "3. Sending an email blast", "<p>Be sure to click on 'Update' or 'Publish' to save your post first. Then click on 'Send Email Blast of this Post...' to bring up the Email Blast dialog. Check or uncheck any specific emails to include in the blast, or use the check box at the very top to select or deselect all emails. The emails at the bottom of the list are those specified in 'Email Blast Default Recipients' in 'Email Notification Settings'. </p></p>
Look over the preview of your email, and click on 'Send Email Blast' at the bottom to complete. You will get a status of all emails that the system was able to send to successfully.", "eblaster"),

	array( 1, "B. Pages", "This section explains how to edit and update pages in Wordpress. Help may also be accessed from within WordPress by clicking on 'Help' in the upper right while in Wordpress Admin.", ""),
	array( 2, "1. Editing an existing page", "<p>To view existing pages, click on Pages on the left sidebar. Pages here can be modified by clicking on their title. That will take you to the 'Edit Page ' screen. From this page, the title and text of the page can be edited. There are two tabs of the text window: Visual and Text. When in the visual tab, the text is shown as is will be on the website. The Text tab is where the actual HTML can be edited. On the right is the Template list. Most pages are not using a template, however some are using the Gazebo templates. These are links to dynamic php files and their text only contains the link.</p>

<p>Creating and removing links and media are done in the same way as posts.</p>

<p>When editing is complete, click the 'Update' button in the Publish box.</p>", ""),
	array( 2, "2. Creating new page", "Under Pages on the left sidebar, there is an option to Add New. Click this takes you to a similar page to create a new page. Enter the title and text of the page, select a category, and Publish.", ""),
	array( 1, "C. Editing the home page", "The home page settings can be found under the left sidebar. Go to Appearance, and Theme options in the submenu. The right pane will have Basic Settings and Home Page Settings.
Here the Welcome post can be changed (to edit the text, edit the 'Welcome' post in Posts). Also the number of posts shown on the main page can be modified here.", ""),
	array( 1, "D. Media", "Media can be viewed and modified under Media on the left sidebar of the Wordpress interface.", ""),
	array( 2, "1. Media Library", "Selecting Library shows a list of all uploaded media organized into pages. The arrows to the right top of the page are used to navigate the pages. If you hover the cursor over the name of a media upload, you will see the options Edit, Delete Permanently, and View. Editing the media allows captions and description to be added, as well as cropping, resizing and rotation. Clicking 'Delete Permanently' will cause Wordpress to remove the upload. Clicking 'View' will show a page with a link to the item. ", ""),
	array( 2, "2. Add New", "Selecting Add New shows a page with a target drop area to drag files from your computer for upload. Another way is to click 'Select Files' to bring up a file selector box. Once the file is selected, it is instantly uploaded to the website, provided it is of allowed type and size. The file will appear in the 'Library' view in the 'Media' submenu.", "")
);

if ( (isset($_GET['search']) ) && ($_GET['search'] != "") ) {
    for ( $i = 0; $i < count($helptext); $i++ ) {
	if ( strpos(strtolower($helptext[$i][1]), trim(strtolower($_GET['search'])))) {
	    echo "Found Article: " . $helptext[$i][1] . "<br />";
	    $_GET['article'] = $i;
	    break;
	}
    }
    $_GET['search'] = NULL;
}
else if ( isset($_GET['module']) ) {
    for ( $i = 0; $i < count($helptext); $i++ ) {
	if ( $helptext[$i][3] == $_GET['module'] ) {
	    $_GET['article'] = $i;
	}
    }
}
echo "<div style='height:90vh; width:100vw'>";
echo "<table><tr style='height:100%; width:100%'>";
echo "<td style='width:20%'>";


echo "<form name='search' method='get' action='" . pageLink("help") . "'>";
if ( $cms == "wp" ) {
    echo "<input type='hidden' name='page_id' value='" . pageID($pagename) . "' />";
    echo "<input type='hidden' name='page' value='{$pagename}' />";
}
echo "<input type='text' name='search' size='15' />
<button onclick='document.getElementById(\"article\").selectedIndex = -1; 
		document.forms[\"search\"].submit();'>Search Topics</button></form><br />";
echo "<form name='help' method='get' action='" . pageLink("help") . "'>";
if ( $cms == "wp" ) {
    echo "<input type='hidden' name='page_id' value='" . pageID($pagename) . "' />";
    echo "<input type='hidden' name='page' value='{$pagename}' />";
}
echo "<select name='article' id='article' size='15' >";

/* Build topic tree */
for ( $i = 0; $i < count($helptext); $i++ )
{
    $title = $helptext[$i][1];
    $identlevel = $helptext[$i][0];
    while ( $identlevel > 0 ) {
	$title = "&nbsp;&nbsp;" . $title;
	$identlevel--;
    }
    echo "<option value='{$i}' ";
    if ( isset( $_GET['article'] ) && ( $_GET['article'] == $i ) ) {
	echo "selected='selected' ";
    }
    echo "ondblclick=\"document.forms['help'].submit()\">{$title}</option>";
}
echo "</select><br />
<input type='submit' value='View' /></form></td>";

/* Display Help Text */
echo "<td style='width:80%'>";
$articletitle = "Help Topics";
$articletext = "Click on a help topic in the left column and then click View.";
if ( isset( $_GET['article'] ) ) {
    $articletitle = $helptext[$_GET['article']][1];
    $articletext = $helptext[$_GET['article']][2];

    $articletext .= "<p><ul>";
    /* Show child articles as links */
    for ( $link = $_GET['article'] + 1; $helptext[$link][0] > $helptext[$_GET['article']][0]; $link++ ) {
	if ( $helptext[$link][0] == $helptext[$_GET['article']][0] + 1 ) {
	    $articletext .= "<li><a href='" . pageLink("help", "article={$link}") . "'>{$helptext[$link][1]}</a></li>";
	}
    }
    $articletext .= "</ul></p>";
}
echo "<div id='articletext' style='height:80vh; overflow-y: auto'>
	  <h3>{$articletitle}</h3>
	  {$articletext}
      </div>
</td></tr></table></div>";
include 'gazebo-footer.php';
?>
