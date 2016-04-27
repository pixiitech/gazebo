<?php
if ( !isset($cms) )
    $font = "times new roman";
if ( isset($cms) && !isset($_SESSION['ColorScheme']) ) {
    $_SESSION['ColorScheme'] = $default_colorscheme;
}
switch ( $_SESSION['ColorScheme'] )
{
    case 0:
	$colors = array("link-color" => "#0033FF",
	      "link-hover-color" => "#0099FF",
	      "link-lbox-color" => "#FFFFFF",
	      "link-lbox-background-color" => "#00BBDD",
	      "link-lbox-hover-bgcolor" => "#00DDEE",
	      "table-lbox-border-color" => "#00FFFF",
	      "general-background-color" => "#00CCCC",
	      "input-background-color" => "#99FFFF",
	      "input-text-color" => "#002255",
	      "table-background-color" => "#CCFFFF",
	      "table-border-color" => "#FFFFFF",
	      "text-color" => "#003366");
	break;
    case 1:
	$colors = array("link-color" => "#FFFFFF",
	      "link-hover-color" => "#FFFFFF",
	      "link-lbox-color" => "#DDDDFF",
	      "link-lbox-background-color" => "#3333DD",
	      "link-lbox-hover-bgcolor" => "#8888FF",
	      "table-lbox-border-color" => "#0000FF",
	      "general-background-color" => "#0000FF",
	      "input-background-color" => "#0000BB",
	      "input-text-color" => "#FFFFFF",
	      "table-background-color" => "#3333DD",
	      "table-border-color" => "#FFFFFF",
	      "text-color" => "#FFFFFF");
	break;
    case 2:
	$colors = array("link-color" => "#DDDDDD",
	      "link-hover-color" => "#CF006F",
	      "link-lbox-color" => "#999999",
	      "link-lbox-background-color" => "#220000",
	      "link-lbox-hover-bgcolor" => "#CF006F",
	      "table-lbox-border-color" => "#CF006F",
	      "general-background-color" => "#222222",
	      "input-background-color" => "#220000",
	      "input-text-color" => "#CF006F",
	      "table-background-color" => "#332233",
	      "table-border-color" => "#555555",
	      "text-color" => "#EF008F");
	break;
    case 3:
	$colors = array("link-color" => "#000000",
	      "link-hover-color" => "#666666",
	      "link-lbox-color" => "#000000",
	      "link-lbox-background-color" => "#666666",
	      "link-lbox-hover-bgcolor" => "#AAAAAA",
	      "table-lbox-border-color" => "#000000",
	      "general-background-color" => "#CCCCCC",
	      "input-background-color" => "#AAAAAA",
	      "input-text-color" => "#000000",
	      "table-background-color" => "#CCCCCC",
	      "table-border-color" => "#000000",
	      "text-color" => "#000000");
	break;
    case 4:
	$colors = array("link-color" => "#99FF99",
	      "link-hover-color" => "#FFFFFF",
	      "link-lbox-color" => "#55AA55",
	      "link-lbox-background-color" => "#002200",
	      "link-lbox-hover-bgcolor" => "#99FF99",
	      "table-lbox-border-color" => "#99FF99",
	      "general-background-color" => "#000000",
	      "input-background-color" => "#002200",
	      "input-text-color" => "#99FF99",
	      "table-background-color" => "#070C07",
	      "table-border-color" => "#99FF99",
	      "text-color" => "#99FF99");
	break;
    case 5:
	$colors = array("link-color" => "#882200",
	      "link-hover-color" => "#FFBB33",
	      "link-lbox-color" => "#CC6600",
	      "link-lbox-background-color" => "#FFFF99",
	      "link-lbox-hover-bgcolor" => "#FFCC55",
	      "table-lbox-border-color" => "#FF9900",
	      "general-background-color" => "#FFFFBB",
	      "input-background-color" => "#FFCC55",
	      "input-text-color" => "#000000",
	      "table-background-color" => "#FFFF99",
	      "table-border-color" => "#CC6600",
	      "text-color" => "#000000");
	break;
    case 6:
	$colors = array("link-color" => "#33FFFF",
	      "link-hover-color" => "#33CCFF",

	      "link-lbox-color" => "#FFFFFF",
	      "link-lbox-background-color" => "#110066",
	      "link-lbox-hover-bgcolor" => "#332288",
	      "table-lbox-border-color" => "#3333CC",

	      "general-background-color" => "#3300CC",
	      "input-background-color" => "#110066",
	      "input-text-color" => "#FFFFFF",
	      "table-background-color" => "#3333CC",
	      "table-border-color" => "#110066",
	      "text-color" => "#FFFFFF");
	break;
    default:
	break;

}
echo "<style media='screen' type='text/css'>";
if ( $_SESSION['ColorScheme'] <> 7 ) {
echo "
a:link.lbox,a:visited.lbox
{
display:block;
width:100px;
font-weight:bold;
color:{$colors["link-lbox-color"]};
background-color:{$colors["link-lbox-background-color"]};
text-align:center;
padding:4px;
text-decoration:none;
text-transform:uppercase;
}

a:hover.lbox,a:active.lbox
{
text-align:center;
display:block;
width:100px;
color:{$colors["link-lbox-color"]};
background-color:{$colors["link-lbox-hover-bgcolor"]};
}

a:link.lbox-web,a:visited.lbox-web
{
display:block;
max-width:150px;
min-width:80px;
font-weight:bold;
font-size:90%;
color:{$colors["link-lbox-color"]};
background-color:{$colors["link-lbox-background-color"]};
text-align:center;
padding:4px;
text-decoration:none;
text-transform:uppercase;
}

a:hover.lbox-web,a:active.lbox-web
{
text-align:center;
display:block;
max-width:150px;
min-width:80px;
font-size:90%;
color:{$colors["link-lbox-color"]};
background-color:{$colors["link-lbox-hover-bgcolor"]};
}

img.banner
{
   height: auto;
   width: 25%;
   height:20%;
   height:200px;
   max-width:290px;
}

table.lbox
{
    margin-left:auto; 
    margin-right:auto;
    border: 3px;
    background-color:{$colors["link-lbox-background-color"]};
    border-style: solid;
    border-color: {$colors["table-lbox-border-color"]};
}

table.lbox-web
{
    margin-left:auto; 
    margin-right:auto;
    border: 3px;
    background-color:{$colors["link-lbox-background-color"]};
    border-style: solid;
    border-color: {$colors["table-lbox-border-color"]};
}

table.main, p.main
{
    width: 100%;
    text-align:center;
    margin-left:auto; 
    margin-right:auto;
    border: 3px;
    background-color:{$colors["table-background-color"]};
    border-style: solid;
    border-color: {$colors["table-border-color"]};
}

.tdhalf {
    width:50%;
    min-width:500px;
}

div.recordinput
{
    text-align: center;
    color: {$colors["text-color"]};
    font-family: {$font};
    border-style: solid;
    border-color: {$colors["table-border-color"]};
}

input.label
{
    color: {$colors["text-color"]};
    background-color: {$colors["table-background-color"]};
    border: none;
}
table.criteria, table.result
{
    margin-left:auto; 
    margin-right:auto;
    border: 2px;
    border-style: solid;
    background-color: {$colors["table-background-color"]};
    border-color: {$colors["table-border-color"]};
    text-align:center;
}

table.vendors
{
    margin-left:auto; 
    margin-right:auto;
    border: 2px solid black;
    background-color: {$colors["table-background-color"]};
    border-color: {$colors["table-border-color"]};
    text-align:center;
    border-spacing:10px;
}
td.vendors
{
    border: 2px solid black;
}
td.thinbottom
{
    border-bottom: 1px solid #ddd;
}
img.photos
{
    height:240px;
    width:350px;
}
img.viewer
{
    background-color: {$colors["general-background-color"]};
    position:fixed;
    left:50%;
    top:50%;
    height:1px;
    width:1px;
}

input,select,button,input[type='text']
{
    color: {$colors["input-text-color"]};
    background-color: {$colors["input-background-color"]};
    <?php if ( !isset($cms)) { echo 'font-size: 70%;'; } ?>
}

h1,h2, h3, h4
{
    color: {$colors["text-color"]};
    border: white solid thin;
}

p 
{
    color: {$colors["text-color"]};
    background-color: {$colors["general-background-color"]};
    font-family: {$font};
    font-size: large;
}

p.center
{
    color: {$colors["text-color"]};
    background-color: {$colors["general-background-color"]};
    font-family: {$font};
    text-align: center;
    font-size: large;
}

span.formentry
{
    font-family: courier;
    font-weight: bold;
}

textarea
{
    color: {$colors["input-text-color"]};
    background-color: {$colors["input-background-color"]};
}

body, div.module
{
    background-color: {$colors["general-background-color"]};
    color: {$colors["text-color"]};
    font-family: {$font};
}";
}
else {
    echo "p.center
{
    text-align: center;
    font-size: large;
}";
}

if ( !isset( $cms ) ) //Standalone only
{
    echo "a:link {color: {$colors["link-color"]}}
    a:visited {color: {$colors["link-color"]}}
    a:hover {color: {$colors["link-hover-color"]}}";
}

echo "</style>";
?>
