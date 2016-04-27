<?php require 'config.php'; 
session_start();

echo "<div class='lbox' style=\"width:100%; height:100%; background-image:url('{$gazebo_imagedir}gradient.png'); background-size:100% 100%; background-repeat:repeat-x; text-align:center\">";

echo "<img src='{$community_imagedir}sailboat_estates.png' class='banner' />";
echo "<img src='{$community_imagedir}pool.jpeg' class='banner' />";
echo "<img src='{$community_imagedir}beach.jpg' class='banner' />";
echo "<img src='{$community_imagedir}lobby.jpg' class='banner' /></div>";
 ?>

<table class="lbox-web" border='3'>
<tbody>
<tr style="text-align:center" width=244px>
<?php if ( !isset($_SESSION['Username']) ) 
    echo "<td><i><a class='lbox-web' href='login.php'>Sign In</a></i></td>"; 
echo "<td><a class='lbox-web' href='index.php'>Home</a></td>";
if ( isset($_SESSION['Username']) ) echo "
      <td><a class='lbox-web' href='calendar.php'>Event Calendar</a></td>
      <td><a class='lbox-web' href='workorder-web.php'>Work Orders</a></td>
      <td><a class='lbox-web' href='vendors.php'>Vendor Listings</a></td>
      <td><a class='lbox-web' href='documents.php'>Documents</a></td>
      <td><a class='lbox-web' href='board.php'>Board Info / Minutes</a></td>
      <td><a class='lbox-web' href='photos.php'>Community Update</a></td>";
if ( !isset($_SESSION['Username']) ) 
    echo "<td><a class='lbox-web' href='floorplan.php'>Floor Plans</a></td>
          <td><a class='lbox-web' href='apply.php'>Apply</a></td>";
echo "<td><a class='lbox-web' href='contact.php'>Contact</a></td>";
if ( isset($_SESSION['Username']) ) 
    echo "<td><a class='lbox' href='profile.php'>Profile</a></td>"; ?>
</tr></tbody>
</table>
