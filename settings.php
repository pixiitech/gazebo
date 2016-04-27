<?php $pagename = "settings"; 
require 'gazebo-header.php';
?>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Settings and Tools</h2>

<?php
require 'authcheck.php';

echo "<br />
<p><h3 style='text-align:center'><a href='" . pageLink("gensettings") . "'>General Settings</a></h3></p>
<p><h3 style='text-align:center'><a href='" . pageLink("filemgr") . "'>File Management</a></h3></p>
<p><h3 style='text-align:center'><a href='" . pageLink("securefilemgr") . "'>Secure File Management</a></h3></p>
<p><h3 style='text-align:center'><a href='" . pageLink("notification") . "'>Email Notification Setup</a></h3></p>
";
if ($module_violations)
    echo "<p><h3 style='text-align:center'><a href='" . pageLink("violationmgr") . "'>Violation Type Management</a></h3></p>";
echo "
<p><h3 style='text-align:center'><a href='" . pageLink("loginmgr") . "'>Login Management</a></h3></p>
<p><h3 style='text-align:center'><a href='" . pageLink("backup") . "'>Backup, Restore and Mass Data</a></h3></p>";

include 'gazebo-footer.php';
?>
