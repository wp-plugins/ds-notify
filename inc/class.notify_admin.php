<?php	
	if (!defined('ABSPATH')){
		die;
	}

	

	// -- Class Name : notify_admin
	// -- Purpose : Notify Admin Functions.
	class notify_admin{

		// -- Function Name : notify_menu
		// -- Params : NULL
		// -- Purpose : Adding Notify menu option to admin menu.
		function notify_menu(){
			$page_title = "Notify Settings";
			$menu_title = "Notify Settings";
			$capability = "administrator";
			$menu_slug = "notify_settings";
			$function = "notify_settings";
			add_menu_page($page_title, $menu_title, $capability, $menu_slug, array(&$this,$function));
			add_action('admin_init', array(&$this,'register_mysettings'));
			//call register settings function
		}

		

		// -- Function Name : register_mysettings
		// -- Params : NULL
		// -- Purpose : Registering Plugin settings(inputs)
		function register_mysettings(){
			register_setting('notify-group', 'ds_notify_logo');
			register_setting('notify-group', 'ds_notify_time');
			register_setting('notify-group', 'ds_notify_sound');
			register_setting('notify-group', 'ds_notify_delay_time');
			register_setting('notify-group', 'ds_notify_web');
			register_setting('notify-group', 'ds_notify_internal');
			register_setting('notify-group', 'ds_info_color');
			register_setting('notify-group', 'ds_warning_color');
			register_setting('notify-group', 'ds_error_color');
			register_setting('notify-group', 'ds_success_color');
			register_setting('notify-group', 'ds_note_pos');
			register_setting('notify-group', 'ds_note_close');
			register_setting('notify-group', 'ds_note_cookie');
		}

		

		// -- Function Name : notify_settings
		// -- Params : NULL
		// -- Purpose : Adding admin settings html.
		function notify_settings(){
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			
			if (!user_can($user_id, 'create_users')) return false;
			
			if (isset($_GET['settings-updated'])){
				?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.'); ?></strong></p>
    </div>
	<?php
 	} ?>
	<div class="wrap notify_panel">
	<h2 class="notify_title">Notify Options</h2><br />
	<hr>
    <form method="post" action="options.php">
        <?php settings_fields('notify-group'); ?>
        <label for="ds_notify_logo">Notify Icon</label><br />
        <input type="text" name="ds_notify_logo" id="ds_notify_logo" value="<?php echo get_option('ds_notify_logo'); ?>" /><br /><br />
        <label for="ds_notify_delay_time">Notify Delay Time (eg: 3000);</label><br />
        <input type="text" name="ds_notify_delay_time" id="ds_notify_delay_time" value="<?php echo get_option('ds_notify_delay_time'); ?>" /><br /><br />
        <label for="ds_notify_time">Notify Duration Time (eg: 5000);</label><br />
        <input type="text" name="ds_notify_time" id="ds_notify_time" value="<?php echo get_option('ds_notify_time'); ?>" /><br /><br />
         <label for="ds_notify_sound">Notify Sound Url (format: mp3);</label><br />
        <input type="text" name="ds_notify_sound" id="ds_notify_sound" value="<?php echo get_option('ds_notify_sound'); ?>" /><br /><br />
       	<br />
       	<br />
       	<h3 class="mini_title">Enable Notifications</h3>
       	<hr>
       	<input type="checkbox" name="ds_notify_web" <?php
			
			if (get_option('ds_notify_web')){
				echo "checked";
			}

			?>/>Enable Web Notifications (doesn't support phones) ?<br /><br />
        <input type="checkbox" name="ds_notify_internal" <?php
			
			if (get_option('ds_notify_internal')){
				echo "checked";
			}

			?>/>Enable Internal Notifications (supported on phones)?
        <br />
       	<br />
       	<h3 class="mini_title">Internal Notifications</h3>
       	<hr>
       	<label for="picker1">Info Notification</label>
       	<input type="text" name="ds_info_color" value="<?php echo get_option('ds_info_color'); ?>" data-default-color="<?php echo get_option('ds_info_color'); ?>" id="picker1" /><br /><br />
       	<label for="picker1">Warning Notification</label>
       	<input type="text" name="ds_warning_color" value="<?php echo get_option('ds_warning_color'); ?>" data-default-color="<?php echo get_option('ds_warning_color'); ?>" id="picker2" /><br /><br />
       	<label for="picker1">Error Notification</label>
       	<input type="text" name="ds_error_color" value="<?php echo get_option('ds_error_color'); ?>" data-default-color="<?php echo get_option('ds_error_color'); ?>" id="picker3" /><br /><br />
	<label for="picker4">Success Notification</label>
    <input type="text" name="ds_success_color" value="<?php echo get_option('ds_success_color'); ?>" data-default-color="<?php echo get_option('ds_success_color'); ?>" id="picker4" /><br /><br />
	<label>Internal Notifications Position</label>
	<?php 
			$selected = get_option('ds_note_pos')  ? esc_attr( get_option('ds_note_pos')) :
			"";
			?>
		<select name="ds_note_pos">
			<option value="toast-top-right" <?php  echo selected( $selected, 'toast-top-right'); ?>>Top right</option>
			<option value="toast-top-left" <?php  echo selected( $selected, 'toast-top-left'); ?>>Top left</option>
			<option value="toast-bottom-left" <?php  echo selected( $selected, 'toast-bottom-left'); ?>>Bottom left</option>
			<option value="toast-bottom-right" <?php  echo selected( $selected, 'toast-bottom-right'); ?>>Bottom right</option>
			<option value="toast-top-full-width" <?php  echo selected( $selected, 'toast-top-full-width'); ?>>Top full width</option>
			<option value="toast-bottom-full-width" <?php  echo selected( $selected, 'toast-bottom-full-width'); ?>>Bottom full width</option>
			<option value="toast-top-center" <?php  echo selected( $selected, 'toast-top-center'); ?>>Top center</option>
			<option value="toast-bottom-center" <?php  echo selected( $selected, 'toast-bottom-center'); ?>>Bottom center</option>
		</select>
		<br /><br />
		<label>Internal Notifications close button</label>
<input type="checkbox" name="ds_note_close" <?php
			
			if (get_option('ds_note_close')){
				echo "checked";
			}

			?>/>Enable Internal notifications close button?<br /><br />
			<label>Save Notification cookies?(for one time view).</label>
<input type="checkbox" name="ds_note_cookie" <?php
			
			if (get_option('ds_note_cookie')){
				echo "checked";
			}

			?>/>Enable Notifications Cookie?<br /><br />
        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
        </p>
    </form>
	</div>
	<?php
		}

	}