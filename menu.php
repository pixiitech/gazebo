<?php
require "config.php";

/* Standalone - display banner */
if ( !isset($cms) )
{
    echo "<p style='text-align:center; class=\"center\"'>";
    echo "<img style='text-align:center; background-color:#FFFFFF' height='120px' width='400px' src='{$gazebo_imagedir}{$logoPic}' />";
}

/* Temporary style attributes if no stylesheet used */
if ( isset($cms) )
{
    echo "<style>
table.lbox
{
    width: 75%;
    text-align:center;
    height: 30px;
    margin-left:auto; 
    margin-right:auto;
    border-width: 0px;
    border-style: solid;
    padding:0px 0px 0px 0px;
}

td.lbox
{
    text-align:center;
    padding:0px 5px 0px 5px;
    border-style: none;
}

";
    echo "</style>";
}

/* Show menu buttons */
echo "<table class='lbox' ><tbody><tr>";

for ( $i = 0; $i < count($modules); $i++ )
{
    if ( $_SESSION['Level'] < $modules[$i][3] )
	continue;
    if ( $modules[$i][5] == false )
	continue;
    echo "<td class='lbox'><a class='lbox' href='" . pageLink($modules[$i][0]) . "'><img 'class=lbox' src='{$modules[$i][1]}' alt='{$modules[$i][2]}' /></a></td>";
}

echo "</tr></tbody></table>";

require_once 'context-buttons.php';

if ( !isset($cms)) echo "</p>";

?>
