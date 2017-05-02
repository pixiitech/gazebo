<?php $pagename = "loginmgr";
$customjs = true;
require 'gazebo-header.php';
?>

<script>
//jQuery scripts
$(document).ready(function(){
    $("#ShowNew").click(function(){
        $("#NewUser").fadeIn(500);
    });
});
</script>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Login Management</h2>

<?php
require 'authcheck.php';
$default_pw = fetchSetting("DefaultPW", $con);
$lastfirst = fetchSetting("DisplayLastFirst", $con);
$ucase = fetchSetting("DisplayUppercaseNames", $con);
foreach( ["search_username", "search_resident"] as $key ) {
	if (!isset($_POST[$key])) {
		$_POST[$key] = "";
	}
}
/* Create newly submitted user */

if ( isset( $_POST['NewUser-Username'] ) && ( $_POST['NewUser-Username'] != "New User" ) && ( $_POST['NewUser-Username'] != "" ))
{
    if ($cms == "wp")
    {
        $userdata = array (
          'user_login' => $_POST['NewUser-Username'],
          'user_pass' => $_POST['NewUser-Password']
        );
        $newID = wp_insert_user( $userdata );
        add_user_meta( $newID, 'gazebo_level', $_POST['NewUser-Level']);
        add_user_meta( $newID, 'gazebo_residx', $_POST['NewUser-Residx']);
        add_user_meta( $newID, 'gazebo_colorscheme', $default_colorscheme);
        add_user_meta( $newID, 'expiration', strtotime($_POST['NewUser-ExpirationYear']
					 . "-" . $_POST['NewUser-ExpirationMonth'] . "-" . $_POST['NewUser-ExpirationDay']));
        if ( $_POST['NewUser-Level'] <= $level_security )
          update_user_meta( $newID, 'show_admin_bar_front', false);
        get_user_by('id', $newID)->set_role($wp_roles[$_POST['NewUser-Level']]);
        echo "Add new user {$_POST['NewUser-Username']} succeeded.<br />";
    }
    else
    {
        $_POST['NewUser-Username'] = strtolower($_POST['NewUser-Username']);
        $querystring = "SELECT Username FROM Login WHERE LOWER(Username) = '{$_POST['NewUser-Username']}'";
	debugText($querystring);
        if (mysqli_fetch_array(mysqli_query($con, $querystring)))
          echo "User {$_POST['NewUser-Username']} already exists, please choose a different username.<br />";
        else if ( hasSpaces($_POST['NewUser-Username']))
          echo "Error: Username contains spaces.";
        else
        {
          $cryptpass = mysqli_real_escape_string($con, crypt($_POST['NewUser-Password'],$encryption_salt));
          $querystring = "INSERT INTO Login (Username, Password, Level, Residx, ColorScheme, ResultsPerRow) VALUES ('{$_POST['NewUser-Username']}', '{$cryptpass}', '{$_POST['NewUser-Level']}', '{$_POST['NewUser-Residx']}', $default_colorscheme, 5)";
          debugText($querystring);
          $result = mysqli_query($con, $querystring);
          if ($result)
            echo "Add new user {$_POST['NewUser-Username']} succeeded.<br />";
          else
            echo "Add new user {$_POST['NewUser-Username']} failed.<br />";
        }
    }
}
/* Update users that were changed */

if ($cms == "wp")
{
    include $wp_root . "wp-admin/includes/user.php";
    $user_query = new WP_User_Query( array( 'search' => '*' ) );
    // User Loop
    if ( ! empty( $user_query->results ) ) {
	foreach ( $user_query->results as $user )
    {

    	$lUser = strtolower($user->user_login);
      $userid = $user->ID;
      $user_level = get_user_meta($user->ID, gazebo_level, true);
      $user_residx = get_user_meta($user->ID, gazebo_residx, true);
      $user_expiration = get_user_meta($user->ID, expiration, true);
      $changed_user_expiration = strtotime($_POST["User{$userid}" . '-expirationYear']
          . "-" . $_POST['User' . $userid . '-expirationMonth'] . "-" . $_POST["User{$userid}" . '-expirationDay']);
      if ( isset( $_POST["User{$userid}" . '-clearpass'] )) {
        wp_set_password($default_pw, $user->ID);
        echo "Reset password of user " . $user->user_login . " to {$default_pw} <br />";
      }
      if ( isset( $_POST["User{$userid}" . '-level'] ) && ( $_POST["User{$userid}" . '-level'] <> $user_level)) {
        update_user_meta( $user->ID, 'gazebo_level', $_POST["User{$userid}" . '-level'] );
        update_user_meta( $user->ID, 'wp_capabilities', "");
        if ( $_POST["User{$userid}" . '-level'] <= $level_security )
          update_user_meta( $newID, 'show_admin_bar_front', false);
        else
          update_user_meta( $newID, 'show_admin_bar_front', true);
            get_user_by('id', $user->ID)->set_role($wp_roles[$_POST["User{$userid}" . '-level']]);
        echo "Set level of user " . $user->user_login . " to " . $_POST["User{$userid}" . '-level'] . ".<br />";
      }
        if ( isset( $_POST["User{$userid}" . '-residx'] ) && ( $_POST["User{$userid}" . '-residx'] <> $user_residx))
        {
	    update_user_meta( $user->ID, 'gazebo_residx', $_POST["User{$userid}" . '-residx'] );
	    echo "Set resident # of user " . $user->user_login . " to #" . $_POST["User{$userid}" . '-residx'] . ".<br />";
        }
        if ( isset( $_POST["User{$userid}" . '-expirationDay'] ) && ( $changed_user_expiration <> $user_expiration))
        {
	    if ( $changed_user_expiration == false )
	    {
		delete_user_meta( $user->ID, 'expiration');
		echo "Cleared expiration date of user " . $user->user_login . ".<br />";
	    }
	    else
	    {
		update_user_meta( $user->ID, 'expiration', $changed_user_expiration );
	        echo "Set expiration date of user " . $user->user_login . " to " . date("m/d/Y", $changed_user_expiration) . ".<br />";
	        debugText("Timestamp: " . $changed_user_expiration);
	    }
        }
        if ( isset( $_POST["User{$userid}" . '-delete'] ))
        {
	    wp_delete_user( $user->ID );
	    echo "Deleted user " . $user->user_login . ".<br />";
	}
    }
    }
}
else    // Standalone
{
$querystring = "SELECT * FROM Login";
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) )
{
    $lUser = strtolower($row['Username']);
    $userid = $row['Idx'];
    if ( isset( $_POST["User" . $userid . '-clearpass'] ))
    {
      $defaultpass = mysqli_real_escape_string($con, crypt($default_pw,$encryption_salt));
      $querystring2 = "UPDATE Login SET Password='{$defaultpass}' WHERE Idx = '{$userid}'";
      debugText($querystring2);
      $result2 = mysqli_query($con, $querystring2);
      if ($result2)
          echo "Reset password of user " . $row['Username'] . " to {$default_pw}<br />";
      else
          echo "Failed reset password of user " . $row['Username'] . ".<br />";
    }
    if ( isset( $_POST['User' . $userid . '-level'] ) && ( $_POST['User' . $userid . '-level'] <> $row['Level']))
    {
      $querystring2 = "UPDATE Login SET Level='" . $_POST['User' . $userid . '-level'] . "' WHERE Idx = '{$userid}'";
      debugText($querystring2);
      $result2 = mysqli_query($con, $querystring2);
      if ($result2)
          echo "Set level of user " . $row['Username'] . " to " . $_POST['User' . $userid . '-level'] . ".<br />";
      else
          echo "Failed to set level of user " . $row['Username'] . ".<br />";
    }
    if ( isset( $_POST['User' . $userid . '-residx'] ) && ( $_POST['User' . $userid . '-residx'] <> $row['Residx']))
    {
      $querystring2 = "UPDATE Login SET Residx='" . $_POST['User' . $userid . '-residx'] . "' WHERE Idx = '{$userid}'";
      debugText($querystring2);
      $result2 = mysqli_query($con, $querystring2);
      if ($result2)
        echo "Set resident # of user " . $row['Username'] . " to #" . $_POST['User' . $userid . '-residx'] . ".<br />";
      else
        echo "Failed to set resident # of user " . $row['Username'] . ".<br />";
    }
    if ( isset( $_POST['User' . $userid . '-delete'] ))
    {
      $querystring2 = "DELETE FROM Login WHERE Idx = '{$userid}'";
      debugText($querystring2);
      $result2 = mysqli_query($con, $querystring2);
      if ($result2) {
        echo "Deleted user " . $row['Username'] . ".<br />";
        $querystring2 = "SELECT * FROM Login";
        $result2 = mysqli_query($con, $querystring2);
      } else
        echo "Failed to delete user " . $row['Username'] . ".<br />";
    }
  }
}

/* Display search form */
echo "<div style='text-align:center'>";
echo "<h4>Search:</h4>";
echo "<form id='search' name='search' method='post' action='" . pageLink("loginmgr") . "'>";
echo "<table style='margin:0px auto' border=1 cellpadding=4 class='sortable'><tbody><tr>";
echo "<th>Username</th><th>Resident Name</th><th>";

echo "<tr><td><input name='search_username' size='35' value='{$_POST['search_username']}' /></td>";
echo "<td><input name='search_resident' size='35' value='{$_POST['search_resident']}' /></td>";
echo "<td><input type='submit' value='Search' />&nbsp;";
echo "<button name='clear' onClick='document.forms[\"search\"].elements[\"search_username\"].value=\"\";
				    document.forms[\"search\"].elements[\"search_resident\"].value=\"\";'>Clear</button></td>";
echo "</tr></table></form></div>";

/* Display user list and create HTML form */
echo "<div style='text-align:center'>";
echo "<form id='recordinput' name='recordinput' method='post' action='" . pageLink("loginmgr") . "'>";
echo "<table style='margin:0px auto' border=1 cellpadding=4 class='tablesorter sortable'><thead><tr><th>Username</th><th>Password</th>";
echo "<th>Access Level</th><th>Resident Name</th><th></th><th>Unit</th><th>Expiration (mm/dd/yy)</th><th>Delete User</th></tr></thead>";
echo "<tbody>";
if ( $cms == "wp" )
{
    if ( isset( $_POST['search_username'] ) && ( $_POST['search_username'] != "" ))
      $query = array( 'search' => $_POST['search_username'], 'search_columns' => array( 'user_login' ) );
    else if ( isset( $_POST['search_resident'] ) && ( $_POST['search_resident'] != "" ))
    {
      $querystring = "SELECT Residents.Idx FROM Residents, Properties
                 WHERE (Residents.FirstName LIKE '%{$_POST['search_resident']}%'
                  OR Residents.LastName LIKE '%{$_POST['search_resident']}%'
                  OR Residents.FirstName2 LIKE '%{$_POST['search_resident']}%'
                  OR Residents.LastName2 LIKE '%{$_POST['search_resident']}%')";
      debugText($querystring);
      $result = mysqli_query($con, $querystring);
      $meta_query = array('relation' => 'OR');
      while ( $row = mysqli_fetch_array($result) )
      {
        debugText($row['Idx']);
        array_push($meta_query, array('key' => 'gazebo_residx', 'value' => $row['Idx'], 'compare' => '='));
      }
      $query = array( 'meta_query' => $meta_query);
    }
    else
      $query = array( 'search' => '*' );

    $user_query = new WP_User_Query( $query );

    // User Loop
    if ( ! empty( $user_query->results ) ) {
      foreach ( $user_query->results as $user )
      {
        $lUser = strtolower($user->user_login);
        $userid = $user->ID;
        $user_level = get_user_meta($user->ID, gazebo_level, true);
        $user_residx = get_user_meta($user->ID, gazebo_residx, true);
        $user_expiration = intval(get_user_meta($user->ID, expiration, true));
        if ( $user_expiration != 0 ) {
          $user_expiration_month = date("m", $user_expiration);
          $user_expiration_day = date("d", $user_expiration);
          $user_expiration_year = date("Y", $user_expiration);
        }
        else {
          $user_expiration_month = NULL;
          $user_expiration_day = NULL;
          $user_expiration_year = NULL;
        }
        $resname = fetchResname($user_residx, $con, $ucase, $lastfirst);
        echo "<tr>";
        echo "<td>" . $user->user_login . "</td>";
        echo "<td>";
        if ($_SESSION['Level'] >= $editlevel)
          echo "<input type='checkbox' name='User{$user->ID}-clearpass' /> Reset";
        echo "</td>";
        echo "<td>";
        if (($_SESSION['Level'] <= $editlevel) && ($user_level >= $editlevel)) {
          echo $levels[$user_level];
        } else {
          if ($_SESSION['Level'] == $editlevel) {
            $numlevels = $editlevel;
          } else {
            $numlevels = count($levels);
          }
          echo "<span hidden='hidden'>{$user_level}</span>";  //hidden sort parameter
          echo "<select name='User{$userid}-level'>";
          for ( $i = 0; $i < $numlevels; $i++ ) {
            $selected = ($user_level == $i) ? "selected='selected'" : '';
            if ($levels[$i] != "") {
              echo "<option value='{$i}' {$selected}>{$levels[$i]}</option>";
            }
          }
          /* echo "</select><script>document.forms['recordinput'].elements['User{$userid}-level'].selectedIndex = {$user_level};</script>"; */
        }
        echo "</td>";
        echo "<td><span hidden='hidden'>{$resname}</span>";
        echo "<input type='text' class='label' size='25' name='User{$userid}-resname' value=\"{$resname}\" readonly=true /><input type='hidden' name='User{$userid}-residx' value='{$user_residx}' /></td><td>";
            echo "<button onclick=\"window.open('" . pageLink("resname", "target1=User{$userid}-residx&target2=User{$userid}-resname") . "','selectResname','width=350, height=600, status=yes'); return false;\">Select</button>";
        echo "</td><td>";
        $unit = fetchUnit($user_residx, $con);
        echo $unit[0];
        echo "</td><td>";
        if ($_SESSION['Level'] >= $editlevel)
          echo "<input type='text' name='User{$userid}-expirationMonth' size='3' value='{$user_expiration_month}' />/
  <input type='text' name='User{$userid}-expirationDay' size='3' value='{$user_expiration_day}' />/
  <input type='text' name='User{$userid}-expirationYear' size='4' value='{$user_expiration_year}' /></td>";
        else
          echo $user_expiration;
        echo "</td><td>";
        if ( $_SESSION['Level'] >= $editlevel )
            echo "<input type='checkbox' name='User{$userid}-delete' />";
        echo "</td></tr>";
      }
    }
    else
	echo 'No users found.';
}
else	//Standalone
{
    $querystring = "SELECT * FROM Login";
    if ( $_POST['search_username'] != "" ) {
	$querystring = "SELECT * FROM Login WHERE Username LIKE '%{$_POST['search_username']}%'";
    }
    else if ( $_POST['search_resident'] != "" ) {
	$querystring = "SELECT * FROM Login, Residents WHERE (Residents.Name LIKE '%{$_POST['search_resident']}%' OR
							OR Residents.LastName LIKE '%{$_POST['search_resident']}%'
							OR Residents.FirstName2 LIKE '%{$_POST['search_resident']}%'
							OR Residents.LastName2 LIKE '%{$_POST['search_resident']}%')";
    }
    debugText($querystring);
    $result = mysqli_query($con, $querystring);
    if (!$result)
			echo "No users found.";
    while ( $row = mysqli_fetch_array($result) )
    {
        $resname = fetchResname($row['Residx'], $con);
        $lUser = strtolower($row['Username']);
        echo "<tr><td>{$row['Username']}</td>";
        echo "<td>";
        if (($_SESSION['Level'] >= $editlevel) && ($row['Level'] < $editlevel))
            echo "<input type='checkbox' name='User{$row['Idx']}-clearpass' /> Clear";
        echo "</td><td>";
        if (($_SESSION['Level'] <= $editlevel) && ($row['Level'] >= $editlevel))
    	echo $levels[$row['Level']];
        else if ($_SESSION['Level'] == $editlevel)
        {
            echo "<select name='User{$row['Idx']}-level'>";
            for ( $i = 0; $i < $editlevel; $i++ )
    	        echo "<option value='{$i}'>{$levels[$i]}</option>";
	    echo "</select><script>document.forms['recordinput'].elements['User{$row['Idx']}-level'].selectedIndex = {$row['Level']};</script>";
        }
        else
        {
	    echo "<select name='User{$row['Idx']}-level'>";
            for ( $i = 0; $i < count($levels); $i++ )
    	        echo "<option value='{$i}'>{$levels[$i]}</option>";
	    echo "</select><script>document.forms['recordinput'].elements['User{$row['Idx']}-level'].selectedIndex = {$row['Level']};</script>";
        }
        echo "</td><td><span hidden='hidden'>{$resname}</span>
<input type='text' class='label' size='25' name='User{$row['Idx']}-resname' value='{$resname}' readonly=true /><input type='hidden' name='User{$row['Idx']}-residx' value='{$row['Residx']}' /></td><td>";
        echo "<button onclick=\"window.open('resname.php?fn=0&target1=User{$row['Idx']}-residx&target2=User{$row['Idx']}-resname','selectResname','width=350, height=600, status=yes'); return false;\">Select</button>";
        echo "</td><td></td><td>";
	$unit =  fetchUnit($row['Idx'], $con);
	echo $unit[0];
	echo "</td><td>";
        if ( ( $_SESSION['Level'] > $editlevel ) || ( ($_SESSION['Level'] == $editlevel) && ($row['Level'] < $editlevel) ) )
            echo "<input type='checkbox' name='User{$row['Idx']}-delete' />";
        echo "</td></tr>";
    }
}
echo "</tbody><tfoot>";
if ( $_SESSION['Level'] >= $editlevel )
{
    echo "<tr hidden='hidden' id='NewUser'><td><input type='text' name='NewUser-Username' size='10' value='New User' onclick='this.value=\"\"' /></td>";
    echo "<td><input type='password' name='NewUser-Password' size='10' value='' /></td><td>";
    if ( $_SESSION['Level'] == $editlevel )
    {
	echo "<select name='NewUser-Level'>";
	for ( $i = 0; $i < $editlevel; $i++ )
	    echo "<option value='{$i}'>{$levels[$i]}</option>";
	echo "</select>";
    }
    else
    {
	echo "<select name='NewUser-Level'>";
	for ( $i = 0; $i < count($levels); $i++ )
	    echo "<option value='{$i}'>{$levels[$i]}</option>";
	echo "</select>";
    }
    echo "</td><td><input type='text' class='label' size='25' name='NewUser-Resname' value='' readonly=true /><input type='hidden' name='NewUser-Residx' /></td><td>";
    echo "<button onclick=\"window.open('";
    echo pageLink("resname", "fn=0&target1=NewUser-Residx&target2=NewUser-Resname");
    echo "','selectResname','width=350, height=600, status=yes'); return false;\">Select</button></td>";
    echo "<td><input type='text' name='NewUser-ExpirationMonth' size='3' value='' />/
<input type='text' name='NewUser-ExpirationDay' size='3' value='' />/
<input type='text' name='NewUser-ExpirationYear' size='4' value='' /></td>";
    echo "<td></td>";
}

echo "</tfoot></tbody></table>";
echo "<input type='button' value='Add User' id='ShowNew' />&nbsp;";
echo "<input type='submit' value='Save' style='text-align:center' /></form></div>";
include 'gazebo-footer.php';
?>
