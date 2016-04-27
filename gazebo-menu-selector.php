<?php 
    /* Gazebo Addition - Menu Selector */
    require "config.php";
    $level = get_user_meta(wp_get_current_user()->ID, "gazebo_level", true);
    if ( $level < $level_tenant )
        wp_nav_menu( array( 'menu' => 'Public' ) ); 
    else if ( $level == $level_tenant )
        wp_nav_menu( array( 'menu' => 'Tenant' ) ); 
    else
	wp_nav_menu( array( 'menu' => 'Owners & Staff' ) );
    /* End Addition */
?>
