<?php
/* Show public login prompt */
if (!(current_user_can('level_0')) && !isset($silent_login)){ ?>
	<table><tr><td>
	<form action="<?php echo get_option('home'); ?>/wp-login.php" method="post">
	Username: <input type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="15" />
	Password: <input type="password" name="pwd" id="pwd" size="15" />
	<input type="submit" name="submit" value="Login" class="button" />
    				
	<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me</label>
       	<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
    	</form></td><td>
	<a href="<?php echo get_option('home'); ?>/wp-login.php?action=lostpassword">Recover password</a></td></tr></table>
<?php } 
/* If logged in, show logged in status, logout link and supermenu */
else { 
	global $wp_container_pagename;
	include_once 'config.php';
	if ( !$nolibrary ) {
	    include_once $maindir . 'library.php';
	}
	$user = wp_get_current_user();
	/* Check for expired logins */
	$expiration = get_user_meta($user->ID, 'expiration', true);
	if ( ($expiration != "") && ($expiration < time()) ) {
	    wp_logout();
	    echo "<script>window.alert('Your login has expired. Please contact the administration office.'); window.location.href='" . get_site_url() . "';</script>";
	}
					
	/* Log into gazebo */
    	$_SESSION['Username'] = $user->user_login;
    	$_SESSION['Level'] = get_user_meta($user->ID, 'gazebo_level', true);
    	$_SESSION['ColorScheme'] = get_user_meta($user->ID, 'gazebo_colorscheme', true);
	$_SESSION['Residx'] = get_user_meta($user->ID, 'gazebo_residx', true);
	$_SESSION['24HrTime'] = get_user_meta($user->ID, 'gazebo_24hrtime', true);
	$_SESSION['DebugMode'] = get_user_meta($user->ID, 'gazebo_debugmode', true);

	/* Set PHP error mode */
	if ( $_SESSION['DebugMode'] == 'on' ) {
	    set_error_handler("customError"); 
	}

	/* Display logged in name with supermenu */
	if ( ! isset($silent_login) ) {
		echo "<table><tr><td>Logged in: ";
		$current_user = wp_get_current_user();
		echo wp_specialchars(stripslashes($current_user->display_name), 1) . "</td>";
		echo "<td><a href='" . wp_logout_url(get_site_url()) . "'>Logout</a></td>";
		if ( current_user_can("publish_posts") ) {
	    	    echo "<td><a href='" . get_site_url() . "/wp-admin/'>Wordpress Admin</a></td>";
		}
		if ( $_SESSION['Level'] >= $level_security ) {
	    	    echo "<td><a href='" . pageLink('home') . "'>Gazebo Admin</a></td>";
		}
		echo "<td><a href='" . pageLink('profile') . "'>Profile</a></td>";
		echo "</tr></table>";
	}
}
/* end login prompt */
?>
