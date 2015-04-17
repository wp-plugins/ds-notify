<?php
 /*
   Plugin Name: Ds-notify
   Plugin URI: http://dscom.website/notify
   Description: Internal And web notifications based on posts / custom notifications created by administrator,choose posts to show on / pages / type of notification.
   Version: 1.2
   Author: DScom
   Author URI: http://dscom.website
*/
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
register_activation_hook( __FILE__,array( &$notify,'notify_install')); // On plugin activation hook for table creation
register_deactivation_hook( __FILE__,array( &$notify,'notify_uninstall'));
add_action('admin_menu', array( &$notify,'notify_menu'));
require('inc/class.notify.php'); // including main notify class 
$notify = new notify();