<?php
require_once "config.php";
/* Below menu left area */

echo "<span style='position: absolute'>";
if ( !isset( $cms ) ) {
    echo "<br />";
}

/* Show admin-only help icon */
if ( gazebo_user_is($level_security) ) {
  echo "<img src='{$gazebo_imagedir}help.png' onclick=\"window.open('";
  echo pageLink("help", "module={$pagename}");
  echo "','help','width=800, height=550, status=yes'); return false;\">";
}

/* Show print icon */
if ( isset($printable) ) {
    echo "<img src='{$gazebo_imagedir}print.png'
		onClick='document.getElementById(\"printdata\").value = document.getElementById(\"printarea\").innerHTML;
			 document.forms[\"printform\"].submit();' />";
    echo "<form name='printform' method='post' action='" . pageLink("print") . "' target='_blank'>";
    echo "<input type='hidden' name='printdata' id='printdata' />";
    echo "</form>";
}
echo "</span>";
?>
