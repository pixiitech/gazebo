<?php
echo "<style>
.calendarleft {
    width:30%;
    min-width:250px;
    height:60%;
}

.calendarright {
}
span.calendarday
{
display:block;
font-weight:bold;
font-size:1.2em;
}

a.calendar,a:visited.calendar,span.calendar
{
display:block;
font-weight:bold;
font-size:0.8em;
color:{$colors["text-color"]};
background-color:{$colors["table-background-color"]};
text-align:center;
padding:2px;
}

a:hover.calendar,a:active.calendar,a.calendar-selected
{
display:block;
font-weight:bold;
font-size:0.8em;
color:{$colors["link-lbox-color"]};
background-color:{$colors["link-lbox-hover-bgcolor"]};
text-align:center;
padding:2px;
}

td.calendar,th.calendar
{
min-width:75px;
text-align:center;
}

tr.calendar
{
height:75px;
}

div.calendarstatus
{
font-weight:bold;
font-size:1.3em;
}

.hidden
{ visibility: hidden; }
";

if ( $_SESSION['ColorScheme'] == 7 )
  echo "
span:hover.calendarday, span.calendar-selected
{
background-color:#cccccc;
}";

echo "</style>";
?>
