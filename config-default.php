<?php
global $cols, $con, $sql_server, $sql_user, $sql_pass, $sql_db, $wp_db, $modules, $cms;

//MySQL settings
$sql_user = "root";
$sql_pass = "battleship787";
$sql_server = "localhost";
$sql_db = "gazebo";

//Basic info
$logoPic = "gazebo-wb2.png";
$rootdir = "/var/www/wp-content/plugins/gazebo/";			//Root directory on local filesystem for saving files
$maindir = "wp-content/plugins/gazebo/";		//Gazebo directory relative to main webserver directory
					//Leave blank if gazebo is installed in root directory
$imagedir = $maindir . "images/";
$community_imagedir = $maindir . "images/community/";
$gazebo_imagedir = $maindir . "images/gazebo/";
$violation_imagedir = $maindir . "images/violation/";
$formdir = $maindir . "form_letters/";
$backupdir = $maindir . "backups/";
$webcontentdir = $maindir . "web-content/";
$securimagedir = $maindir . "securimage/";
$phpmailerdir = $maindir . "PHPMailer/";
$encryption_salt = "pixii";
$max_upload_size = 16000000;		//16 MB


//Login timeouts
$session_expiration = 60*60*24;
$cookie_expiration = 60*60*24;		//24 hours

//Wordpress integration
$cms = "wp";				//not set = standalone, wp = wordpress
$wp_root = "";
$wp_db = "gazebo_wp";
$wp_roles = array("", "subscriber", "subscriber", "author", "subscriber", "editor", "administrator", "administrator", "administrator");
$wp_container_pagename = "Gazebo Container Page";
$wp_popup_container = "Gazebo Popup Container";
$wp_binary_container = "Gazebo Secure File";

//Enable or disable modules. 1=enabled, 0=disabled.
$module_packages = 1;
$module_violations = 1;
$module_formletters = 1;
$module_community = 1;
$module_workorders = 1;

//Define how privilege levels are saved in the database
$level_logout = -1;
$level_disabled = 0;
$level_tenant = 1;
$level_resident = 2;
$level_board = 3;
$level_security = 4;
$level_staff = 5;
$level_management = 6;
$level_developer = 8;

//Privilege Level names
$levels = array("Disabled", "Tenant", "Resident", "Board", "Security", "Staff", "Management", "Level 7", "Developer");

/* Page Registry */
$modules = array(
//  (0)Filename-(1)Image-----------------------------(2)Title--(3)View Sec. Level-(4)Edit Sec. Level-(5)Show in menubar
array( "home", $gazebo_imagedir . "home-button.png", "Home", $level_security, $level_security, true),
array( "residents", $gazebo_imagedir . "residents-button.png", "Residents", $level_resident, $level_security, true),
array( "properties", $gazebo_imagedir . "properties-button.png", "Properties", $level_security, $level_security, true),
array( "packages", $gazebo_imagedir . "packages-button.png", "Packages", $level_security, $level_security, true),
array( "violations", $gazebo_imagedir . "violations-button.png", "Violations", $level_security, $level_security, true),
array( "formletters", $gazebo_imagedir . "formltr-button.png", "Form Letters", $level_staff, $level_staff, true),
array( "workorder", $gazebo_imagedir . "workorders-button.png", "Work Orders", $level_staff, $level_staff, true),
array( "community", $gazebo_imagedir . "community-button.png", "Community", $level_staff, $level_staff, true),
array( "settings", $gazebo_imagedir . "settings-button.png", "Settings", $level_staff, $level_staff, true),
array( "profile", $gazebo_imagedir . "profile-button.png", "Profile", $level_tenant, $level_tenant, true),
//Community submenu
array( "comminfo", "", "Community Info", $level_staff, $level_staff, false),
array( "calendar", "", "Event Calendar", $level_tenant, $level_security, false),
array( "announce", "", "Manage Announcements", $level_staff, $level_staff, false),
array( "subdivmgr", "", "Subdivision Management", $level_staff, $level_staff, false),
array( "amenitymgr", "", "Amenity Management", $level_staff, $level_staff, false),
array( "webeditor", "", "Website Editor", $level_staff, $level_staff, false),
//Tools submenu
array( "gensettings", "", "General Settings", $level_staff, $level_staff, false),
array( "rosterconfig", "", "Roster Configuration", $level_staff, $level_staff, false),
array( "filemgr", "", "File Management", $level_staff, $level_staff, false),
array( "securefilemgr", "", "Secure File Management", $level_staff, $level_staff, false),
array( "notification", "", "Email Notification Setup", $level_staff, $level_staff, false),
array( "violationmgr", "", "Violation Type Manager", $level_staff, $level_staff, false),
array( "loginmgr", "", "Login Management", $level_staff, $level_staff, false),
array( "backup", "", "Backup", $level_staff, $level_staff, false),
// Non-menu pages
array( "resname", "", "Resident name selector", $level_security, $level_security, false),
array( "workorder-web", "", "Submit a Work Order", $level_tenant, $level_security, false),
array( "overnightguestform", "", "Overnight Guest Form", $level_tenant, $level_tenant, false),
array( "vendoraccessform", "", "Vendor Access Form", $level_tenant, $level_tenant, false),
array( "holdharmlessauto", "", "Hold Harmless - Auto", $level_tenant, $level_tenant, false),
array( "holdharmlessemployee", "", "Hold Harmless - Employee", $level_tenant, $level_tenant, false),
array( "holdharmlesspest", "", "Hold Harmless - Pest", $level_tenant, $level_tenant, false),
array( "forgotpass", "", "Forgot Password", $level_tenant, $level_tenant, false),
array( "resetpass", "", "Reset Password", $level_tenant, $level_tenant, false),
array( "login", "", "Login", $level_disabled, $level_disabled, false),
array( "help", "", "Help", $level_tenant, $level_tenant, false),
array( "print", "", "Print", $level_tenant, $level_tenant, false),
array( "register", "", "Register", $level_disabled, $level_disabled, false),
array( "securefile", "", "Secure File", $level_disabled, $level_disabled, false),
array( "eblaster", "", "Email Blast", $level_security, $level_security, false),
array( "nameconverter", "", "Name Converter", $level_developer, $level_developer, false) );

//Calendar icon list
$iconlist = array("alert.png", "balloons.png", "construction.png", "meeting.jpeg", "party.png", "check.png", "reservation.png", "beach.png", "musicnote.png", "star.png", "bingo.png", "movie.png");

//Work Orders
$status_submitted = 0;
$status_approved = 1;
$status_denied = 2;
$status_completed = 3;

//Form Letters
$formletter_default = 0;
$formletter_eform = 1;
$formletter_violation = 2;

//Default Color Scheme
$default_colorscheme = 7; //No Style

?>
