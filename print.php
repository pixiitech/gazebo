<?php 
$pagename = "print";
$nologo = true;
$customjs = true;
require 'gazebo-header.php'; ?>


<script>
$(document).ready( function() {
    window.print();
    window.close();
});

</script>
<?php
echo "<h3 id='title' name='title'>{$_POST['title']}</h3>
<div id='printContainer' name='printContainer'>
{$_POST['printdata']}
</div>";

require 'gazebo-footer.php';
?>
