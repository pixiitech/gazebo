Gazebo property management - Versions
-------------------------------------
0.1
-----
SQL Setup
Residents Database
PHP Interface
Parking Violations
User Profiles (password change)
Basic Mass upload

0.2 - Pembroke Isles
-----
Package System
Search Violations by Subdiv/Time
Pk Violation pictures
Resident Interface
Search/Insert form

0.25
-----
- Backup system added
- Form letter templates
- Color Scheme options under tools

0.3
-----
New Features:
- Form letter 'Create Document' from parking violation
  with fields
- Search by ordinal violations
- Profile - Change results per row
- Violations - Generalize types from ViolationTypes
- Tools - Image management
- Formletters - Add Community Info fields
- Tools - Login Management

Bugfixes:
- Properties - Violation tallies no longer work
- Formletters - Letter goes away after save
- Violations - IE: Criteria fields not showing
- Formletters - IE: doubleclick on fields not working,
	 changed to a button instead

DB Changes:
Violations:Guest bit(1) -> Type int(11)
Add table ViolationTypes :
    Idx int(11) Primary Key
    Name varchar(40)
Add field Login : ResultsPerRow int(11)
Added DB CommunityInfo :
Idx int(11) Primary Key,
Name varchar(40),
Logo varchar(40),
MailingAddress varchar(40),
Phone varchar(20),
Fax varchar(20),
PropertyMgrName varchar(40),
PropertyMgrEmail varchar(40),
PresidentName varchar(40),
PresidentEmail varchar(40),
SecretaryName varchar(40),
SecretaryEmail varchar(40)

0.32
----
New Features:
Tools - Violation type management
Tools - Subdivision management
Violations - Show action status list in side box
All - New styles for menu bar (/w embossed style icons)
ViolationTypes - Ability to assign default letters, log action lists 
FormDocs - Ability to update violation's action status and log
Tools - Generic backup for all tables
Tools - Backup: Added 'All' checkbox
All tabs - Criteria clears after search
Tools - Restore / Mass upload parsing
All tabs - Switched to php sessions from cookies (more secure)
Home - Added home screen (blank for now)

DB Changes:
Violations: ActionStatus int(11)
Violations: ActionLog varchar(400)
ViolationTypes:
Idx int(11) auto_increment, Name varchar(40), Action1 varchar(40),
     Action2 varchar(40), Action3 varchar(40), Action4 varchar(40), 
     Expiration int(11), FineDue int(11), PRIMARY KEY (Idx)
ViolationTypes: Idx 0 no longer allowed due to auto increment


0.4
-----
New Features / Changes:
Added owner/tenant option for residents
Changed name of residences.php to properties.php
Profile - For resident logins, allowed personal info changes
Profile - For resident logins, added guest information
Properties - No longer adds new residents, they must be created with residents.php first
Hyperlink Resident/Tenant names from Properties to Residents with GET data, added fillInForm
Hyperlink Unit info from Residents to Properties with GET data, added fillInForm
Properties - made residx/tenantidx hidden
Properties - Added Violation Report checkbox
Properties - Added owner/tenant name search popup
Login Mgr. - Added owner name search popup
Residents - Updated code for dynamic form fields based on function
Guest System - add form field so security can view resident guestinfo
Login - Encrypt passwords with crypt()
Residents - accommodate City/State/Zip for MailingAddress (changed it to textarea)
Created Public Website Home Page (claridge demo)
Adapted residents.php and login.php for public site
Resident Directory - respect publish settings for users < board level

BUGFIXES:
Allowed blank passwords at login
Security could not view violation informatiofn=0&target1=Residx&target2=Resnamen
fillInForm not working after property insert errors
Error messages in Properties not filling in Subdivisions
Restoring data with newlines in fields now works
Bugfix  - Pages now check for auth level

DB Changes:
Residents: Add
    Type int(1), Vehicle varchar(30), Tag varchar(12),
    Publish int(1), PublishMailingAddress int(1), 
    PublishHomephone int(1), PublishCellphone int(1),
    PublishEmail int(1), GuestInfo varchar(200)
Residents: MailingAddress varchar(40) -> MailingAddress varchar(60)
Properties: Add
    Tenantidx int(11)
Login: Password -> varchar(50)
Properties: PIUnit -> Unit int(11)

0.41 - Claridge demo
----
***Changes made in 2013:
New Features / Changes:
Community Tab - links to edit website, event calendar, amenity management, announcements
Created event calendar with public access and board editing privileges
Amenity reservation
Amenity Management
***Changes made in May 2014
Changed table output to row/column view for all modules

0.5 - Sea Monarch
----
Added icons to event calendar, and day or event now stays highlighted on calendar
Rearranged profile page so there is only one 'Save' button
Removed references to 'Index' or 'Idx' to be more user-friendly
Allowed searching for partial homephone & cellphone
Removed requirement for address and subdivision
Code consolidation - created library.php with common functions
Added work order module
Added public work order submit module
Added vendor listings, photos, private documents page
Added sortable tables code from sorttable.js by Stuart Langridge, http://www.kryogenix.org/code/browser/sorttable/
Added website editor so staff can edit public pages (create pages coming soon)
Created basic admin home page with open work orders and packages awaiting pickup
Added expirimental jQuery fadeIn/Out for workorders.php and announce.php
Added register module for residents to create website logins - uses captchas
jQuery fadeIn 'new' record button - amenitymgr, subdivmgr, violationmgr, loginmgr
Community Info Editor
Adjusted color schemes
Added file input browse fields for backup.php
Properties - label #'s now say Unit

DB Changes:
Add table Events:
Idx int(11) primary key auto_increment, StartTime datetime, EndTime datetime, TimeCreated datetime, CreatedBy varchar(20), 
	Text varchar(25), Description varchar(150), Icon varchar(25), Amenity int(11)
Add table Amenities:
Idx int(11) primary key, Name varchar(40)
Add table Workorders:
Idx int NOT NULL AUTO_INCREMENT,
PRIMARY KEY (Idx),
Username varchar(50),
Name varchar(50),
Unit int,
Summary varchar(50),
Description varchar(200),
AssignedTo varchar(50),
ApprovedBy varchar(50),
Status int,
Submitted datetime,
Completed datetime

Violations: ABDIName varchar(30) -> Name varchar(30)
CommunityInfo: Added FormEmail varchar(40)
Properties: Unit int -> varchar(10)

0.51
-----
Added forgot password / reset password feature
Web page default style (aqua) now shows in editor window
Added WebmasterEmail field to community info
Added debug for work order failures to go to webmaster

DONE:
BUGFIX:
Properties.php / library.php - allowed unit numbers to have letters in them.
Register.php - Units with letters in them could not register users.

DB Changes:
WorkOrders: Add column StatusText varchar(300)
WorkOrders: Description varchar(200) -> varchar(300)
Events: Description varchar(150) -> varchar(300)
Events: Text varchar(25) -> varchar(50)
Login: ResetToken varchar(50)
Login: ResetTokenExpires datetime
CommunityInfo: Add column WebmasterEmail varchar(40)


0.6 - The Claridge R3 (codename Vanilla Chip) - Claridge

Wordpress interfacing changes
*   Recognize Wordpress logins and use wp_usermeta to store access levels
*   Change loginmgr.php to modify wordpress users using classes instead of Gazebo's Login DB
*   Create gazebo wordpress templates
*   Registration page will use wordpress db
*   Profile page will use wordpress db
*   Created INSTALL instructions for combining Gazebo with WP
*   Created generic pageLink function to generate url links for either gazebo standalone or WP gazebo
*   Consolidated menu.php to use an array (with pre-generated url links)
*   Backup.php will backup both databases
*   Hide 'Gazebo' for Residents

Other changes:
*   Adapted profile.php to use wp_get_user_meta
*   Created debug mode for developer level
*   Established defaults for publishing in roster
*   Required all pages to check security levels from $modules
*   Finished implementing 2nd security levels from config.php ($modules) to all modules
*   Created search form for loginmgr.php
*   Reworked packages.php with jQuery fadeins like workorder.php
*   Packages.php - added notifications
*   Expire tenant logins after 6 months (add'l wp_usermeta key)  
*   Added ability to change publish settings in residents.php
*   Added gray hover background over day # in calendar
*   Adjusted calendar columns
*   Users created as staff or administrators in gazebo have admin role in WP (determined in config.php $wp_roles)
*   Register.php - add resident/tenant radio button
*   Finished changing Image Management to File Management
*   Added jQuery fadeIn to all criteria boxes/reworked code to be the same (residents, properties, violations, packages, workorders, announce)
*      - Combined jQuery fadeIn with fillInForm in one function (fnList, fnSearch... so on) that takes arguments (Idx, form fields...)
*   Added mailer to announce.php
*   Fixed sorttable with wordpress - Used Plugin (see https://wordpress.org/plugins/table-sorter/)
*   register.php - After successful registration, logging in returns to home page
*   Email notification for resident info changes (profile.php)
*   Added $maindir directive to config.php
*   Created connect_gazebo_DB function (modified to create a separate database connection from WP)
*   Fixed resname form to work with WP
*   Use connect_gazebo_DB in all modules

*   Add 2nd name field to Resident
*   Add notification.php (Email Notification Settings)
*   Changed comminfo.php to use Settings SQL table instead of CommInfo
*   Notification of registrations
*   Validate email for registration
*   Photos page - Expand photos in an overlay instead of loading in a new page (http://huge-it.com/lightbox/)

BUGFIX:
*  Allowed loginmgr to support multiple changes
*  First gazebo page load showed no menu - moved gazebo auth code from authcheck.php to gazebo-login-prompt.php
*  BUG: properties.php - resname references
*  BUG: Package update not working
*   BUGFIX: Blank passwords not allowed in WP, loginmgr now resets passwords to $default_pw
*   BUGFIX - register.php not checking existing logins properly, allowing multiple per unit
*   BUGFIX: resname.php - pageLink not working (pageID was broken)

DB CHANGES:
    Residents: Added Name2 varchar(60)
    Login: Username varchar(20) -> Username varchar(35)
    Added $level_tenant to config.php, in between level_disabled and level_resident. 
	All active users in Login will need Level increased by 1.
    Added new table:
	CREATE TABLE Settings (Idx int primary key auto_increment, Type varchar(40), Name varchar(40), Description varchar(60), Value varchar(40));
    Removed table CommunityInfo

Templates added to WP:
    gazebo-template.php
    gazebo-resname-template.php

Files added to theme:
    gazebo-login-prompt.php
    gazebo-menu-selector.php

AccessPress FILES MODIFIED:
    header.php
	- References gazebo-login-prompt.php
	- Menu selector by gazebo_level
    page.php
	- Removed comment template
    style.css
	- Changed a (link) colors and boldness
    footer.php
	- Modified footer text
    index-one.php
	- Removed featured posts section
	- Changed content at lower left corner of home page
	  (Address and phone #)

Web Server:
    php.ini
	- Changed max_upload to 18M

0.61 - minor fixes and preparation for Gazebo becoming a WP Plugin
-----
COMPLETED:
filemgr.php - Updated preview window to show text and html files, and made other types downloadable
workorder.php - BUGFIX Fixed description display bug in FillInForm
workorder.php - Remove Approved By field
Added $publishName_disabled setting in config.php to disable changing the default setting for PublishName
	(both in resident.php and profile.php)
loginmgr.php - Changed default setting for Wordpress to not show admin header bar
Violations.php - reworked FillInForm/add jQuery transitions
BUGFIX: Resname.php logged out before rest of gazebo - resname template now performs 'silent login'
Created common header for all modules
    - Include files
    - DB connection
    - jQuery include for standalone only
    - Common jQuery scripts
Created help module inside Gazebo
All modules - selecting 'Delete' function no longer wipes index or name
All modules - added two spaces between each function radio button
properties.php - Fixed floating 'Show violation info' checkbox
properties.php - Removed readonly attribute from Owner and Tenant boxes, to allow keyword searching
packages.php - add column for Mark picked-up
packages.php - selecting 'Mark picked-up' function no longer wipes index
resname.php - Added double-click functionality
Removed web editor from WP mode

DB CHANGES
WorkOrder - changed Unit int(11) to Unit varchar(11)
Violations - changed Unit int(11) to Unit varchar(11)
Packages - changed Unit int(11) to Unit varchar(11)

0.7
-----
COMPLETED:
    - Added setting: Condo or HOA mode (hid Subdivision settings for Condo mode)
    - Made 'List' the default mode for all modules
    - All modules: Removed noCriteria function, replaced by adding 'WHERE 1=1' to query
    - Fixed change password bug, used wp_signon instead of wp_authenticate. See:
	http://stackoverflow.com/questions/17102451/wp-authenticate-returns-error-for-valid-username-and-password-in-wordpress
    - Residents: Separated address into Address, Address 2, City, State, ZIP, Country
    - Settings: Added Condo or HOA mode (Condo mode hides Subdivision selectors)
    - style-gazebo: Revised color schemes for visibility in Wordpress and easier viewing
    - gazebo-header: Re-implemented style-gazebo for all modules
    - BUGFIX: register.php - Not applying default_colorscheme for wordpress logins
    - BUGFIX: register.php - When username is taken, wrong error message shows. Revised userExists for Wordpress
    - register.php - Changed order of error messages
    - register.php - Added setting to enable/disable tenant registration
    - calendar.php - Calendar now has start and end time display (view level) and drop down boxes (edit level)
    - Added print.php - generic print module. Called from menu.php side button. 
	Must set a div with id=printarea and php directive printable=true for it to work.
    - residents.php - Added 3rd and 4th phone number field
    - profile.php - Add settings: 24hr time and debug mode
    - profile.php - Removed ResultsPerRow
    - tools.php - renamed to settings.php, changed icon, WP container name, pagename
    - Added module gensettings.php - General Settings - moved Tenant Registration Setting
    - calendar.php, announce.php, packages.php, violations.php - Finished implementing 24hr time option
    - Finished implementing default publish settings in gensettings
    - Finished implementing tenant expiration settings
    - residents.php - Fixed sorting by unit # to sort in true ascending order (added hidden sort parameter)
    - All modules - Saved querystring in form so after a record update, query reloads
    - BUGFIX: Profile.php - notifications were getting spammed for PublishName update
    - BUGFIX: residents.php - Names were not showing properly in roster when logged in as a resident
    - residents.php - changed fillInForm to accept an array of arguments
    - BUGFIX - residents.php - Fixed missing update button in admin mode
    - BUGFIX - residents.php - fixed defaults not loading in publish settings for Submit New
    - profile.php - Separated phone numbers into three fields on Profile page
    - profile.php - Added City, State, ZIP and Country entry for Mailing Address
    - profile.php - Change password now shows proper error messages
    - residents.php and profile.php - Made Guest Info optional in settings
    - residents.php - Added Comments field
    - settings.php - Added 'Format' field to each setting: text, option, or yesno
    - BUGFIX - profile.php - Fixed name unpublish bug (names would unpublish on profile update when PublishNameLock was set to true)
    - profile.php - Added unit# to notification email on info changes
    - BUGFIX: formletters.php - now showing pictures after saving & reopening forms
    - Gazebo is now WP plugin and also keeps standalone functionality, retains separate gazebo DB
    - Filter searches for the following:
	<gazebo module='modulename' />		Includes the named module in the page
	<gazebo minlevel='5'> ... </gazebo>	Runs security check before displaying tagged code
	<gazebo maxlevel='5'> ... </gazebo>     Maxlevel check (used for error messages mainly)
	<gazebo form='formname' />		Displays the named form, accepts input & sends an email when submitted
    - Added post/page security settings to WP
    - Added email blast functionality to new post/page interface in WP
    - Created eblaster.php (Email blast list selector) and added a link to WP post editor admin page
    - Added secure file uploader
    - Added default behavior setting for secure files
    - BUGFIX - added filter to gazebo.php that logs out of Gazebo on wp_logout
    - Added gazebo-binary-template.php to theme directory
    - BUGFIX - Security: added security check for using a $_GET search on residents.php
    - packages.php - Added mailing notification header with From line filled with the site's Name from FetchSetting

WP CHANGES:
Gazebo resident name selector has been renamed to Gazebo popup template
File name has changed from gazebo-resname-template.php to gazebo-popup-template.php
All files using this template need to be reassigned their template

AccessPress CHANGES:
    single.php
	- Removed comment template

DB CHANGES:
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Community", "Type", "option", "Condo or HOA", "Condo");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "TenantRegistration", "no", "Allow Tenant Registration", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "TenantExpiration", "text", "Default Tenant Login Expiration (days)", "180");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "DefaultPW", "text", "Default Reset Password", "12345");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "ShowGuestInfo", "yesno", "Show Guest Info", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "PublishNameLock", "yesno", "Lock Publish Name Setting", "true");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "PublishNameDefault", "yesno", "Default Publish Name Setting", "true");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "PublishHomephoneDefault", "yesno", "Default Publish Home Phone Setting", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "PublishCellphoneDefault", "yesno", "Default Publish Cell Phone Setting", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "PublishMailingAddressDefault", "yesno", "Default Publish Mailing Address Setting", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "PublishEmailDefault", "yesno", "Default Publish Email Setting", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Violations", "ViolationExpiration", "text", "Violation Expiration (days)", 
"180");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "WPPostDefaultMinLevel", "levelbox", "Default WP Post Security Level", "2");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "SecureFileDefaultBehavior", "sfdefaultbehavior", "Secure File Link Default Behavior", "download");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Email", "EBlastDefaultRecipients", "textbox", "Email Blast Default Recipient List (separator is ;)", "");

CREATE TABLE Forms (Idx int NOT NULL AUTO_INCREMENT, PRIMARY KEY (Idx), Title varchar(50), Text varchar(2000), Type int, Email varchar(50));
CREATE TABLE SecureFileMeta (Idx int NOT NULL AUTO_INCREMENT, PRIMARY KEY (Idx), Filename varchar(50), Description varchar(200), Size int, MIME varchar(50), Minlevel varchar(20), Behavior varchar(20));
CREATE TABLE SecureFileData (Idx int NOT NULL AUTO_INCREMENT, PRIMARY KEY (Idx), Data mediumblob);

Residents: added columns MailingAddress2 varchar (60)
			 Country varchar(60)
			 City varchar(60)
			 State varchar(60)
			 ZIP varchar(15)
			 Phone3 varchar(15)
			 Phone4 varchar(15)
			 Comments varchar(200)

Residents: add column	Email2 varchar(40)
Login: removed ResultsPerRow

Settings: changed column Value from varchar(40) to varchar(400)

OTHER CHANGES:
New images in /images/gazebo 
    -help.png
    -print.png
    -settings-button.png

0.71
-----
COMPLETED:
- Redefined 'pageLink' function to locate one of three container pages (Gazebo Container Page, Gazebo Popup Container and Gazebo Secure File) instead of individual containers for each gazebo module page
- Gazebo page titles are now set from a script in gazebo-header.php using the title from $modules in config.php
- Added setting to display all resident names in uppercase
- Added setting to display resident's last name first or vice versa
- Added Phone 3 and Phone 4 to Profile page for modification
- Registrations can be validated by email from Email or Email2
- Added filter to remove Â from any email blasts
- Fixed select all checkbox in eblaster
- Added setting for a link on the roster form in profile (to answer why name is not editable)

DB CHANGES:
Residents: Change Name varchar(60) -> FirstName varchar(60)
	   Add LastName varchar(60)
	   Change Name2 varchar(60) -> FirstName2 varchar(60)
	   Add LastName2 varchar(60)
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "DisplayUppercaseNames", "yesno", "Display resident names in uppercase", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "DisplayLastFirst", "yesno", "Display resident names as Last, First", "true");

UPDATE PROCEDURE:
- Update all php files
- config.php - add lines:
$wp_container_pagename = "Gazebo Container Page";
$wp_popup_container = "Gazebo Popup Container";
$wp_binary_container = "Gazebo Secure File";
- Remove all existing Gazebo containers
- Create these container pages using the three templates
- Set all menu links to reference the container page + &page=pagename (pagename being $pagename set in the module code)
- Update all form document links to the container page
- Add new settings DisplayUppercaseNames and DisplayLastFirst
- Split first and last names in data

0.7.2
-----
COMPLETED:
- webeditor.php - updated for new TinyMCE, added double-click feature in html list
- residents.php - Fixed name display when deleting a resident
- securefilemgr - Fixed preview in standalone mode

DB CHANGES:

Login: Added column DebugMode int
Add setting:
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "DualRegistration", "yesno", "Enable Dual Registration for each Resident", "false");

0.7.3
-----
COMPLETED:
- loginmgr, library, resname - fixed resident name apostrophe bug
- loginmgr - fixed resident name search to work with first/last name split
- BUGFIX: fixed securefile.php to work with standalone - added quiet authcheck
- formhandler.php - new module to handle form data
- formletters.php - Static fields and entry boxes now operational
- loginmgr.php - Added Unit# to listing
- loginmgr.php - Now uses User IDs instead of usernames in POST data (to prevent special characters issue)
- Theme files - Moved gazebo-login-prompt.php and gazebo-menu-selector.php from theme to plugin directory. Themes will now use
		include getcwd() . "/wp-content/plugins/gazebo/gazebo-login-prompt.php";
		include getcwd() . "/wp-content/plugins/gazebo/gazebo-menu-selector.php";
		to reference the login prompt and menu selector
- calendar.php - No longer shows broken image when 'No Icon' is picked for an event.


DB CHANGES:
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "RegistrationValidationByEmail", "yesno", "Validate registration by email in database", "false");

Login - added column Idx primary key auto_increment

0.7.4
-----
COMPLETED:
- profile.php - Avoided 'Remember password' prompts from various browsers on Change Password boxes (using jQuery)
- eblaster.php - Now allowing SMTP email via PHPMailer
- notification.php - Recognizes yesno and password setting formats
- config.php - Changed default Wordpress role for Board access level to 'Author'
- backup.php - Include a status on each record restored to database in output window, with success/failure tallies
- library.php / gazebo-login-prompt.php - Added custom error handler to be active if 'Debug Mode' selected

- residents.php - Changed format of resident information (split resident 1 and resident 2)
- residents.php - Changed Homephone to Phone 1, Cellphone to Phone 2. Added phone type fields
- residents / profile.php - Added ability to invert how resident publish settings are displayed (DO NOT Publish checkboxes)
- residents.php - Added separators between information of 1st and 2nd residents in list
- library.php - Added formatPhone function to format phone numbers in resident list
- residents.php - Fixed bug where PublishName was getting unchecked - changed behavior so it is the same as other publish settings, only hidden when PublishNameLock setting is enabled
- library.php - Added additional argument to pageLink to return the full URL (only for standalone)
- forgotpass.php - Fixed bug in standalone where email link did not have full URL

DB CHANGES:
Added settings for SMTP email:
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Email", "SMTPHost", "text", "SMTP Mail Host (leave blank for local server)", "");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Email", "SMTPPort", "text", "SMTP Port", "");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Email", "SMTPAuth", "authtype", "SMTP Authentication", "none");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Email", "SMTPUsername", "text", "SMTP Username", "");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Email", "SMTPPassword", "password", "SMTP Password", "");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Email", "FromAddress", "text", "From Email Address", "");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Email", "FromName", "text", "Display name on email originating from site", "");

Added Invert Publish settings display:
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("General", "InvertPublishSettings", "yesno", "Invert Publish Settings Display", "false");

Residents table:
Homephone -> Phone1
Cellphone -> Phone2
PublishHomephone -> PublishPhone1
PublishCellphone -> PublishPhone2
Added Phone1Type varchar(15), Phone2Type varchar(15), Phone3Type varchar(15), Phone4Type varchar(15)
Commands:
ALTER TABLE Residents change column Homephone Phone1 varchar(15);
ALTER TABLE Residents change column Cellphone Phone2 varchar(15);
ALTER TABLE Residents change column PublishHomephone PublishPhone1 int(1);
ALTER TABLE Residents change column PublishCellphone PublishPhone2 int(1);
ALTER TABLE Residents add column Phone1Type varchar(15);
ALTER TABLE Residents add column Phone2Type varchar(15);
ALTER TABLE Residents add column Phone3Type varchar(15);
ALTER TABLE Residents add column Phone4Type varchar(15);

Settings table:
PublishHomephoneDefault -> PublishPhone1Default
PublishCellphoneDefault -> PublishPhone2Default
Commands:
UPDATE Settings SET Name = 'PublishPhone1Default' WHERE Name = 'PublishHomephoneDefault';
UPDATE Settings SET Name = 'PublishPhone2Default' WHERE Name = 'PublishCellphoneDefault';

0.8 - The mysqli update
-----------------------
COMPLETED:
- Updated all modules to use mysqli instead of mysql
- Fixed most Uninitialized Index and Unitialized Variable errors
- Major refactor of residents.php
- BUGFIX: Email2 now shows on resident roster in resident mode (if PublishEmail is on)
- Fixed issue with Gazebo buttons showing too small, removed width: 50% CSS line
- Fixed issue with encrypted password showing when password is reset in loginmgr (standalone only)
- Added roster configuration page, and publish visibility settings
- Fixed issue with Gazebo toolbar showing up very small (allowed 75% width)

DB CHANGES:
The following settings have type changed to Type 'Roster':

INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", "PublishNameDefault", "yesno", "Default Publish Name Setting", "true");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", "PublishPhone1Default", "yesno", "Default Publish Home Phone Setting", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", "PublishPhone2Default", "yesno", "Default Publish Cell Phone Setting", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", "PublishMailingAddressDefault", "yesno", "Default Publish Mailing Address Setting", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", "PublishEmailDefault", "yesno", "Default Publish Email Setting", "false");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", "InvertPublishSettings", "yesno", "Invert Publish Settings Display", "false");

The following settings are new:
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", 
"PublishNameVisibility", "roster_visibility", "Publish Name Checkbox Visibility", "hidden");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", 
"PublishPhone1Visibility", "roster_visibility", "Publish Phone #1 Checkbox Visibility", "enabled");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", 
"PublishPhone2Visibility", "roster_visibility", "Publish Phone #2 Checkbox Visibility", "enabled");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", 
"PublishMailingAddressVisibility", "roster_visibility", "Publish Mailing Address Checkbox Visibility", "enabled");
INSERT INTO Settings (Type, Name, Format, Description, Value) VALUES ("Roster", 
"PublishEmailVisibility", "roster_visibility", "Publish Email Checkbox Visibility", "enabled");

Removed the "PublishNameLock" setting as it is superseded by "PublishNameVisibility"

Add rosterconfig module entry to config.php

TODO:

Testing

BUG: residents.php: Only first name shows in confirmation box when deleting active record from radio option
BUG: calendar.php - ' quotes causing escaped \' to fill in every update for title and description
Require resname.php to update unit # in loginmgr
Add registration e-mail validation settings
securefilemgr.php - Add error message for files over the size limit, and show the size limit

formletters.php - 
	Add custom fields for custom entry boxes
	settings (notification.php): Add email setting for each Form
	Create help file

plugin - 
	Initialization - plugin creates gazebo DB and container pages (if they do not yet exist)
    - Remove gazebo templates
    - Change directory references to plugin_dir_path() or plugins_url()

All modules - Change fillInForm to accept an array instead of individual field arguments
		done: announce, residents
Residents.php - Enter key should submit form, not bring up resname popup

Optional
    - Unique function prefix ( gazebo_ )
    - Use $wpdb->prefix instead of referencing wp_
    - Eliminate PHP errors
    - Use wp_enqueue_script() and wp_enqueue_style
Coding standards  https://make.wordpress.org/core/handbook/coding-standards/php/
    - Use esc_attr() to escape attribute codes
    - Indent using tabs only
    - Associative arrays should have each item on a separate line
    - Brace Style ??
    - Remove trailing whitespace after closing php tags

residents.php - Edit unit number directly
BUG - loginmgr updating ExpirationDate on other users not modified (monitoring)

