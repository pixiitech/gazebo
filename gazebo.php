<?php
session_start();
/*
Plugin Name: Gazebo
Plugin URI: http://pixiitech.net/#
Description: Gazebo Property Management System
Author: Pixii Computing
Version: 0.7
Author URI: http://www.pixiitech.net/
*/

if ( !class_exists ('wp_gazebo_plugin')) {
    class wp_gazebo_plugin {

	function gazebo_addcolumn() {
		global $wpdb;
		if (false === $wpdb->query("SELECT gazebo_minlevel FROM $wpdb->posts LIMIT 0")) {
			$wpdb->query("ALTER TABLE $wpdb->posts ADD COLUMN gazebo_minlevel varchar(20)");
		}
	}
			
	function gazebo_update_post($ID) {
		global $wpdb;
		global $post;
		extract($_POST);
		//Update security level setting
		$wpdb->query("UPDATE $wpdb->posts SET gazebo_minlevel = '$gazebo_minlevel' WHERE ID = $ID");
		//Email Blast (don't run if this is an auto save or the function is called for saving revision)
		if (( $EmailResidents == 'on' ) &&
		   !( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) &&
		   !( $post->post_type == 'revision' )) {

			//include_once "config.php";
			//include_once "library.php";
			//echo "<script>window.alert('this is a test');</script>";
			//echo "<script>window.open('";
			//echo pageLink('eblaster');
			//echo "', 'eblaster','width=350, height=600, status=yes'); return false;</script>";
			/*
			$con = connect_gazebo_DB();
			$commName = fetchSetting('Name', $con);
			$querystring = "SELECT Email FROM Residents WHERE Email <> ''";
			$result = mysql_query($querystring, $con);
			while ( $row = mysql_fetch_array($result) ) {
			    $to = $row['Email'];
			    $subject = $commName . ' | ' . $post->post_title;
			    $message = $post->post_content;
			    $mailresult = mail($to, $subject, $message);
			    debugText("Email to {$to}: <br />Subject: {$subject}<br />Message: {$message}<br />");
			}
			mysql_close($con);
			*/
		}
	}

	function gazebo_publish_options() {
		include_once "config.php";
		include_once "library.php";
		$dbconnector = connect_gazebo_DB();
		$editingpost = $post;
		global $post, $editingpost;
		$gazebo_minlevel = $post->gazebo_minlevel;
		if (get_post_status($post->ID) == 'auto-draft') {
		    $gazebo_minlevel = fetchSetting("WPPostDefaultMinLevel", $dbconnector);
		}

		echo "
		<fieldset id='gazebo-settings-div'>
		<div>
		<p>
		<label for='gazebo_minlevel' >Minimum Security Level</label><br />
			<select name='gazebo_minlevel' id='gazebo_minlevel'>";
			    for ( $i = 0; $i < count($levels); $i++ ) {
				echo "<option value='{$i}' ";
				if ( $gazebo_minlevel == $i ) {
				    echo "selected='selected' ";
				}
				echo ">";
				if ( $i == 0 ) {
				    echo "Public</option>";
				}
				else {
				    echo "{$levels[$i]}</option>";
				}
			    }
		echo "
			</select>
		</p>
		</div>
		<div>
		<p>";
/*		echo "<label for='gazebo_emailblast' ></label>
			  <input id='EmailResidents' name='EmailResidents' type='checkbox' />&nbsp;
		Email blast to residents<br />";*/
		add_thickbox();
		echo "<a href='" . pageLink("eblaster", "postid={$post->ID}&TB_iframe=true&width=600&height=550", $wp_popup_container) . "' class='thickbox'>Send email blast of this post...</a><br /> (be sure to Publish/Update first)";
		echo "</p>
		</div>
		</fieldset>";
		mysql_close($dbconnector);
	}

	// Main parse filter - Access Control and Processing of <gazebo ... > tags

	function gazebo_filter($input) {

	    include_once "config.php";
	    include_once 'library.php';
	    global $post;

	    $output = "";

	    //Determine page/post minimum access level
	    if ( $post->gazebo_minlevel == NULL ) {
		$gazebo_minlevel = 0;
	    }
	    else {
		$gazebo_minlevel = $post->gazebo_minlevel;
	    }
	    //Determine user access level
	    if ( !isset( $_SESSION['Level'] ) || ( $_SESSION['Level'] == NULL )) {
		$level = 0;
	    }
	    else {
		$level = $_SESSION['Level'];
	    }

	    //Restrict by page settings
	    if ( $level < $gazebo_minlevel ) {
	        $output .= "You do not have authorization to view this content.<br />";
		$output .= "Please <a href='" . pageLink('register') . "'>register</a> to view private content.";
		return $output;
	    } 

	    // Pre-search for form tags, to avoid unnecessary db connections
	    if ( strpos($input, "<gazebo form=") ) {
	        $dbconnector = connect_gazebo_DB();
	    }

	    $start = 0;
	    // Locate <gazebo> tags
	    while ( $loc = strpos($input, "<gazebo", $start) ) {
	        // Output file from start location
	        $output .= substr($input, $start, $loc - $start);

	        // Gazebo Module Output
	        if ( substr($input, $loc + 8, 7) == "module=" ) {
		    $quotechar = substr($input, $loc + 15, 1);
		    $endcharpos = strpos($input, $quotechar, $loc + 16);
		    $module = substr($input, $loc + 16, $endcharpos - ($loc + 16));
		    include $module;
		    $endangle = strpos($input, '>', $endcharpos);
		    $start = $endangle + 1;
	        }
	        // Gazebo Form Output
	        else if ( substr($input, $loc + 8, 5) == "form=" ) {
		    $quotechar = substr($input, $loc + 13, 1);
		    $endcharpos = strpos($input, $quotechar, $loc + 14);
		    $formname = substr($input, $loc + 14, $endcharpos - ($loc + 14));
		    include_once "formhandler.php";
		    $output .= do_processform($formname, $dbconnector);	
		    $endangle = strpos($input, '>', $endcharpos);
		    $start = $endangle + 1;
	        }
	        // Gazebo Level-restricted Output by tag
	        else if ( substr($input, $loc + 8, 9) == "minlevel=" ) {
		    $quotechar = substr($input, $loc + 17, 1);
		    $endcharpos = strpos($input, $quotechar, $loc + 18);
		    $level = substr($input, $loc + 18, $endcharpos - ($loc + 18));
		    $endangle = strpos($input, '>', $endcharpos);
		    $endtag = strpos($input, '</gazebo>', $endangle);
		    if ( $_SESSION['Level'] >= $level ) {
		        $output .= substr($input, $endangle + 1, $endtag - ($endangle + 1));
		    }
		    $start = $endtag + 9;
	        }
	        // Gazebo Level-maximum Output by tag
	        else if ( substr($input, $loc + 8, 9) == "maxlevel=" ) {
		    $quotechar = substr($input, $loc + 17, 1);
		    $endcharpos = strpos($input, $quotechar, $loc + 18);
		    $level = substr($input, $loc + 18, $endcharpos - ($loc + 18));
		    $endangle = strpos($input, '>', $endcharpos);
		    $endtag = strpos($input, '</gazebo>', $endangle);
		    if ( $_SESSION['Level'] < $level ) {
		        $output .= substr($input, $endangle + 1, $endtag - ($endangle + 1));
		    }
		    $start = $endtag + 9;
	        }
	    }
	    // Display remaining input
	    $output .= substr($input, $start);

	    // Disconnect DB connection
	    if ( isset( $dbconnector ) ) {
	        mysql_close( $dbconnector );
	    }
	    return $output;
	}
        // Posts query filter - prevents themes from finding unauthorized posts
        public function gazebo_posts_where($where) {

	    //Determine user access level
	    if ( !isset( $_SESSION['Level'] ) || ( $_SESSION['Level'] == NULL )) {
		$userlevel = 0;
	    }
	    else {
		$userlevel = $_SESSION['Level'];
	    }

	    $where .= $GLOBALS['wpdb']->prepare( " AND gazebo_minlevel <= %s", $userlevel );
    	    return $where;
        } 
	function gazebo_logout() {
	    $_SESSION['Level'] = 0;
	    session_destroy();
	}

	function gazebo_login_filter($username, $password) {
	    $username = trim(strtolower($username));
	    //Resist SQL Injection attacks
	    if ( strpos($username, ';') ) {
		return;
	    }
	    //Allow email logins
	    if ( strpos($username, '@') && !username_exists($username)) {
	        include_once "config.php";
	        include_once 'library.php';
	
		$querystring = "SELECT Idx, Email, Email2 FROM Residents WHERE Email = '{$username}' OR Email2 = '{$username}'";	
	        $dbconnector = connect_gazebo_DB();
		$result = mysql_query($querystring, $dbconnector);
		$row = mysql_fetch_array($result);
		if ( $row ) {
		    if ( fetchSetting( 'DualRegistration', $dbconnector ) == 'true' ) {

			if ( strtolower( $row['Email'] ) == $username ) {
			    $users = get_users(array('meta_query' => array('relation' => 'AND', array('key' => 'gazebo_residx', 'value' => $row['Idx'], 'compare' => '='), array('key' => 'gazebo_emailreg', 'value' => 1, 'compare' => '='))));
			}
			else if ( strtolower( $row['Email2'] ) == $username ) {
			    $users = get_users(array('meta_query' => array('relation' => 'AND', array('key' => 'gazebo_residx', 'value' => $row['Idx'], 'compare' => '='), array('key' => 'gazebo_emailreg', 'value' => 2, 'compare' => '='))));
			}

		    }
		    else {
			$users = get_users(array('meta_key' => 'gazebo_residx', 'meta_value' => $row['Idx']));
		    }
		    $user = $users[0];
		    if ( $user ) {
			$username = $user->user_login;
		    }
		}
		mysql_close($dbconnector);
	    }
	    //return $user;
	}

	function gazebo_disp_posts( $atts ) {
	    $a = shortcode_atts( array(
	        'category' => '0',
	        'numberofposts' => '4',
		'showdates' => 'yes',
		'numberofmonths' => '12'
	    ), $atts );

	    $buffer = "";

	    $loop = new WP_Query( array(
	        'cat' => $a['category'], 
	        'posts_per_page' => $a['numberofposts'],
		'date_query' => array(
			array(
				'column' => 'post_date_gmt',
				'after' => $a['numberofmonths'] . ' months ago'
			)),
	    ));

	    while ($loop->have_posts()) : $loop->the_post();
	        	$buffer .= "<div class='event-list clearfix'>";
	        		$buffer .= "					<div>
		        		<h4 class='event-title'><figure class='event-thumbnail'>";
						$buffer .= "<a href='" . get_the_permalink() . "'>";
						if( has_post_thumbnail() ){
						    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'event-thumbnail', false ); 

						    $buffer .= "<img src='" . esc_url($image[0]) . "' alt='" . get_the_title() . "'>";
						} else {
						    $buffer .= "<img src='" . get_template_directory_uri() . "/images/demo/event-fallback.jpg' alt='" . get_the_title() . "'>";
						}			
						if($a['showdates'] == 'yes'){
							$buffer .= "<div class='event-date'>
							<span class='event-date-day'>" . get_the_date('j') . "</span><span class='event-date-month'>" . get_the_date('M') . "</span></div>";
						}
						$buffer .= "</a>
					</figure>

		        			<a href='" . get_the_permalink() . "'>" . get_the_title() . "</a><i> by " . get_the_author() . "</i>
		        		</h4>

		        		<div class='event-excerpt'>" . get_the_content() . "
		        		</div>
	        		</div>
	        	</div>";

	    endwhile;
	    wp_reset_postdata(); 
	    return $buffer;
	}

    } // class gazebo_plugin
}

//Add functions to hooks
add_action('init', array('wp_gazebo_plugin','gazebo_addcolumn'));
add_action('post_submitbox_misc_actions', array('wp_gazebo_plugin', 'gazebo_publish_options'));
//add_action('attachment_submitbox_misc_actions', array('wp_gazebo_plugin', 'gazebo_publish_options'));
add_action('post_updated', array('wp_gazebo_plugin','gazebo_update_post'));
add_filter('the_content', array('wp_gazebo_plugin','gazebo_filter'), 12);
add_filter('the_excerpt', array('wp_gazebo_plugin','gazebo_filter'));
add_filter('posts_where', array('wp_gazebo_plugin','gazebo_posts_where'));
add_action('wp_logout', array('wp_gazebo_plugin', 'gazebo_logout'));
add_filter('wp_authenticate', array('wp_gazebo_plugin', 'gazebo_login_filter'));
add_shortcode( 'gazebo_disp_posts', array('wp_gazebo_plugin', 'gazebo_disp_posts' ));
/* End of File */
