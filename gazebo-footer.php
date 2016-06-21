<?php require "config.php" ?>
<?php

mysqli_close($con);

if ( ! isset( $nologo ) ) {
echo "
<br /><br />
<i>'Gazebo' Property System v0.8  </i>
<i>Created by:</i><br />
<a href='http://www.pixiitech.net/'><img src='{$gazebo_imagedir}pixii.png' alt='PiXii computing' /></a>
<br />
<br />
<p>
    <a href='http://validator.w3.org/check?uri=referer'><img
        src='http://www.w3.org/Icons/valid-xhtml10-blue'
        alt='Valid XHTML 1.0 Transitional' height='31' width='88' /></a>
  </p>";
}

if ( ! isset($cms) ) {
	echo "</body></html>";
}
else {
	echo "</div>";
}
?>
