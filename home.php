<?php 
$pagename = "home";
require 'gazebo-header.php'; 
?>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Home</h2>

<?php
require 'authcheck.php';

echo "<table style='margin-left:auto; margin-right:auto;'>";
if ($module_workorders)
{
	echo "<tr><td>";
	echo "<strong>Open <a href='" . pageLink("workorder") . "'>Work Orders</a></strong>";
	$querystring = "SELECT * FROM WorkOrders WHERE (Status = {$status_submitted} OR Status = {$status_approved})";
	debugText($querystring);
	$result = mysql_query($querystring, $con); 
	$k=0;
	$results=0;
	echo "<table class='criteria' style='width:100%'><tr><th>WO#</th><th>Status</th><th>Unit</th><th>Summary</th><th>Name</th><th>Submitted</th><th>Assigned To</th><th>Approved By</th></tr>";
	while ( $row = mysql_fetch_array($result) )
	{
            echo "<tr>";
            if ( $_SESSION['Level'] >= $editlevel )
	        $selectOpt = 4;
	    else
	        $selectOpt = 2;
            echo "<td>";
            echo $row['Idx'];
            echo "</td><td>";
            echo statusText($row['Status']);
            echo "</td><td>";
	    echo $row['Unit'];
	    echo "</td><td>";
            echo mysql_real_escape_string($row['Summary']);
	    echo "</td><td>";
	    echo $row['Name'];
	    echo "</td><td>";
	    echo $row['Submitted'];
	    echo "</td><td>";
	    echo $row['AssignedTo'];
	    echo "</td><td>";
	    echo $row['ApprovedBy'];
	    echo "</td></tr>";
	    $results++;
	}
	echo "</table>";
	echo "<i>" . $results . " open work order(s)." . "</i></td></tr>";
}
if ($module_packages)
{
	echo "<tr><td>";
	echo "<strong><a href='" . pageLink("packages") . "'>Packages</a> Awaiting Pickup</strong>";
	$querystring = "SELECT * FROM Packages WHERE PickupTime = 0";
	debugText($querystring);
	$result = mysql_query($querystring, $con); 
	$k=0;
	$results=0;
	echo "<table class='criteria' style='width:100%'><tr><th>Index#</th><th>Unit</th><th>For</th><th>Receive Time</th><th>Received By</th><th>Carrier</th></tr>";
	while ( $row = mysql_fetch_array($result) )
	{
	  	echo "<tr>";
                $pic = $gazebo_imagedir . carrierPic($row['Type']);
                if ( $_SESSION['Level'] >= $editlevel )
                    $selectOpt = 4;
                else
                    $selectOpt = 2;
                $picture = $imagedir . $row['Pic'];
                echo "<td>";
                echo $row['Idx'];
                echo "</td><td>";
                echo $row['Unit'];
                echo "</td><td>";
                echo $row['Recipient'];
                echo "</td><td>";
                echo $row['ReceiveTime'];
                echo "</td><td>";
                echo $row['ReceivedBy'];
                echo "</td>";
		echo "<td>";
                echo "<img src='{$pic}' height='50' width='50' />";
                echo "</td>";
                echo "</tr>";
                $results++;
	}
	echo "</table>";
	echo "<i>" . $results . " package(s) awaiting pickup." . "</i></td></tr>";
}
if ($module_violations)
{

}

echo "</table>";

include 'gazebo-footer.php';
?>
