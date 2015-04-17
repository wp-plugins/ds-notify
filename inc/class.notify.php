<?php
    ob_start();
    
    if (!defined('ABSPATH'))
    {
        die;
    }

    include('class.notify_admin.php');

    // -- Class Name : notify
    // -- Purpose : 
    // -- Created On : 
    class notify EXTENDS notify_admin
    {
        

        // -- Function Name : notify_install
        // -- Params : NULL
        // -- Purpose : Creating and inserting default Notify plugin data.
        function notify_install()
        {
            global $wpdb;
            $notify_table = $wpdb->prefix . 'notify';
            
            if ($wpdb->get_var("show tables like '$notify_table'") != $notify_table)
            {
                $sql = "CREATE TABLE " . $notify_table . " (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `title` varchar(255) NOT NULL,
                      `message` varchar(255) NOT NULL,
                      `notify_type` varchar(50) NOT NULL,
                      `notification_type` varchar(50) NOT NULL,
                      `in_posts` varchar(255) NOT NULL,
                      `in_pages` varchar(255) NOT NULL,
                      PRIMARY KEY (`id`)
                    );";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }

        }

        

        // -- Function Name : notify_uninstall
        // -- Params : NULL
        // -- Purpose : Dropping the Notify plugin tables on deactivation of plugin
        function notify_uninstall()
        {
            global $wpdb;
            $notify_table = $wpdb->prefix . 'notify';
            $sql          = "DROP TABLE " . $notify_table;
            $wpdb->query($sql);
        }

        // register custom post type to work with
        

        // -- Function Name : custom_notification_type
        // -- Params : NULL
        // -- Purpose : Registring notification post type.
        function custom_notification_type()
        {

        // set up labels

        $labels = array(
            'name' => 'Notifications',
            'singular_name' => 'Notification',
            'add_new' => 'Add New Notification',
            'add_new_item' => 'Add New Notification',
            'edit_item' => 'Edit Notification',
            'new_item' => 'New Notification',
            'all_items' => 'All Notification',
            'view_item' => 'View Notification',
            'search_items' => 'Search Notifications',
            'not_found' => 'No Notifications Found',
            'not_found_in_trash' => 'No Notifications found in Trash',
            'parent_item_colon' => '',
            'menu_name' => 'Notifications'
        );

        // register post type

        register_post_type('notification', array('labels' => $labels,
            'has_archive' => true,
            'public' => true,
            'supports' => array(
                'title',
                'editor',
                'thumbnail'
            ) ,
            'exclude_from_search' => false,
            'capability_type' => 'post',
            'rewrite' => array(
                'slug' => 'notifications'
            )
        ));
        }

        

        // -- Function Name : notify_type_selection
        // -- Params : NULL 
        // -- Purpose : Adding custom fields to notification post type.
        function notify_type_selection()
        {
            add_meta_box('notify_type', 'Notification Settings', array($this,'notify_type_boxes'),'notification','normal','high');
        }

        

        // -- Function Name : notify_type_boxes
        // -- Params : $post
        // -- Purpose : Add custom fields to custom notifications.
        function notify_type_boxes($post)
        {
            $values   = $this::get_notification($post->post_title);
            $selected = isset($values[0]->notification_type) ? esc_attr($values[0]->notification_type) :
            "";
            if(!empty($values)){
            $posts    = json_decode($values[0]->in_posts);
            foreach ($posts as $key => $value)
            {
                $posts_select[$value] = $value;
            }

            $pages = json_decode($values[0]->in_pages);
            foreach ($pages as $key => $value)
            {
                $pages_select[$value] = $value;
            }
            } else {
                $pages_select[] = '';
                $posts_select[] = '';
            }

            ?>
        <p>
            <label for="notification_type">Type of notification</label>
            <select class="chosen-select" name="notification_type" id="notification_type">
                <option value="success" <?php echo selected($selected, 'success'); ?>>Success</option>
                <option value="information" <?php echo selected($selected, 'information'); ?>>Information</option>
                <option value="error" <?php echo selected($selected, 'error'); ?>>Error</option>
                <option value="warning" <?php echo selected($selected, 'warning'); ?>>Warning</option>
            </select>
        </p>
        <p>
            <label for="posts">Show in post</label>
            <hr>
            <select name="in_posts[]" class="chosen-select" width="400px" data-placeholder="Choose a posts"  multiple tabindex="3">
            <?php
            $args = array(
                'numberposts' => -1,
                'order' => 'DESC',
                'post_type' => 'post',
                'post_status' => 'publish'
                );
            $posts_array = get_posts($args);
            
            if (array_key_exists('all', $posts_select) == true)
            {
                ?>
            <option value="all" selected>All</option>
            <?php
            }
            else
            {
                ?>
            <option value="all">All</option>
             <?php
            }

            foreach ($posts_array as $p)
            {
                ?>
                <option value="<?= $p->post_title;
                ?>" <?php
                
                if (array_key_exists($p->post_title, $posts_select) == true)
                {
                    echo 'selected';
                }

                ?>><?= $p->post_title;
                ?></option>
            <?php
 } ?>
          </select>
        </p>
        <p>
            <label for="pages">Show in Pages</label>
            <hr>
            <select name="in_pages[]" class="chosen-select" data-placeholder="Choose a page" width="400px" multiple tab-index="4">
                <?php
            $argpage = array(
                'sort_order' => 'ASC',
                'post_type' => 'page',
                'post_status' => 'publish'
                );
            $pages   = get_pages($argpage);
            
            if (array_key_exists('all', $pages_select) == true)
            {
                ?>
            <option value="all" selected>All</option>
            <?php
            }
            else
            {
                ?>
            <option value="all">All</option>
             <?php
            }

            foreach ($pages as $pa)
            {
                ?>
                <option value="<?= $pa->post_title; ?>"
                <?php
                
                if (array_key_exists($pa->post_title, $pages_select) == true)
                {
                    echo 'selected';
                }

                ?>><?= $pa->post_title;
                ?></option>
            <?php
 } ?>
            </select>
        </p>
    <?php
        }

        

        // -- Function Name : get_notification
        // -- Params : $title
        // -- Purpose : Fetch notification row based on it's title.
        function get_notification($title)
        {
            global $wpdb;
            $query  = "SELECT * FROM " . $wpdb->prefix . "notify WHERE title = '" . $title . "'";
            $result = $wpdb->get_results($query, OBJECT);
            return $result;
        }

        function delete_note($note_id){
            global $wpdb;
            $post = get_post($note_id);
            $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix."notify WHERE title = '".$post->post_title."'" ) );
        }

        // -- Function Name : notify_append
        // -- Params : $post = null
        // -- Purpose : Show notification on front end.
        function notify_append($post = null)
        {
            $where = 'AND wp_term_taxonomy.term_id IN (' . implode(',', array_map('intval', $array)) . ')';
            global $wpdb;
            $query  = "SELECT * FROM " . $wpdb->prefix . "notify";
            $result = $wpdb->get_results($query, OBJECT);
            
            if (get_option('ds_notify_internal') && !is_admin())
            {
                echo '<div id="ds-notify-container" class="ds-notify-container"></div>';
            }

            foreach ($result as $note)
            {
                $posts = json_decode($note->in_posts);
                foreach ($posts as $key => $value)
                {
                    $posts_array[$value] = $value;
                }

                $pages = json_decode($note->in_pages);
                foreach ($pages as $key => $value)
                {
                    $pages_array[$value] = $value;
                }

                
                if (is_front_page())
                {
                    
                    if (array_key_exists("all", $posts_array) == true)
                    {
                        $allowed = true;
                    }
                    else
                    {
                        $allowed = false;
                    }

                }

                
                if (is_single())
                {
                    global $post;
                    $post_info = get_post($post->ID);
                    
                    if (array_key_exists("all", $posts_array) == true)
                    {
                        $allowed = true;
                    }
                    else
                    {
                        
                        if (array_key_exists($post_info->post_title, $posts_array) == true)
                        {
                            $allowed = true;
                        }
                        else
                        {
                            $allowed = false;
                        }

                    }

                }

                
                if (is_page())
                {
                    global $post;
                    $page_info = get_page($post->ID);
                    if (array_key_exists("all", $pages_array) == true)
                    {
                        $allowed = true;
                    }
                    else
                    {
                        
                        if (array_key_exists($page_info->post_title, $pages_array) == true)
                        {
                            $allowed = true;
                        }
                        else
                        {
                            $allowed = false;
                        }

                    }

                }
                if ($allowed == true)
                {
                    $this::view_notification($note->title, $note->message, $note->notify_type, $note->notification_type);
                }

            }

        }

        

        // -- Function Name : save_notify
        // -- Params : $post_id
        // -- Purpose : Save notification while post is bieng published.
        function save_notify($post_id)
        {
            global $wpdb;
            
            if (!wp_is_post_revision($post_id))
            {
                $content_post = get_post($post_id);
                $content      = wp_strip_all_tags($content_post->post_content);
                $post_content = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $content);
                $post_title   = $content_post->post_title;
                $type         = 'information';
                $result       = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "notify WHERE notify_type = 'post_notification'", OBJECT);
                
                if (count($result) > 0)
                {
                $wpdb->update($wpdb->prefix . "notify",
                            array('title' => $post_title,
                                  'message' => $post_content,
                                'notification_type' => $type,
                                "in_posts" => json_encode(array("all")),
                                "in_pages"=>json_encode(array("all"))
                ), 
                        array('title' => $result[0]->title)
                             );
                }
                else
                {
                    $wpdb->insert($wpdb->prefix . "notify", 
                        array("title" => $post_title,
                            "message" => $post_content,
                            "notify_type" => "post_notification",
                            "in_posts" => json_encode(array("all")),
                            "in_pages"=>json_encode(array("all")),
                            "notification_type" => $type
                            )
                        );
                }

            }

        }

        

        // -- Function Name : save_custom_notify
        // -- Params : $post_id
        // -- Purpose : Save custom notification.
        function save_custom_notify($post_id)
        {
            global $wpdb;
            
            if (!wp_is_post_revision($post_id))
            {
                $content_post = get_post($post_id);
                $content      = wp_strip_all_tags($content_post->post_content);
                $post_content = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $content);
                $post_title   = $content_post->post_title;
                
                if (isset($_POST['notification_type']))
                {
                    $type = $_POST['notification_type'];
                }
                else
                {
                    $type = 'information';
                }

                
                if (isset($_POST['in_posts']))
                {
                    $inposts = $_POST['in_posts'];
                    $inposts = json_encode($inposts);
                }
                else
                {
                    $all     = array('all');
                    $inposts = json_encode($all);
                }

                
                if (isset($_POST['in_pages']))
                {
                    $inpages = $_POST['in_pages'];
                    $inpages = json_encode($inpages);
                }
                else
                {
                    $all     = array('all');
                    $inpages = json_encode($all);
                }

                $result = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "notify WHERE notify_type = 'custom_notification' AND title = '" . $post_title . "'", OBJECT);
                
                if (count($result) > 0)
                {
                    $wpdb->update($wpdb->prefix . "notify", array(                    'title' => $post_title,                    'message' => $post_content,                    'notification_type' => $type,                    'in_posts' => $inposts,                    'in_pages' => $inpages                ), array(                    'title' => $result[0]->title                ));
                }
                else
                {
                    $wpdb->insert($wpdb->prefix . "notify", array(                    "title" => $post_title,                    "message" => $post_content,                    'notification_type' => $type,                    'in_posts' => $inposts,                    'in_pages' => $inpages,                    "notify_type" => "custom_notification"                ));
                }

            }

        }

        

        // -- Function Name : admin_register_head
        // -- Params : null
        // -- Purpose : Add plugin css files to wordpress admin dashboard 
        function admin_register_head()
        {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('notify', plugins_url('../assets/css/ds-notify.css', __FILE__));
        }

        

        // -- Function Name : load_css_file
        // -- Params : null
        // -- Purpose : Adding internal notifications css data from database
        function load_css_file()
        {
            echo "
        <style>
        .toast-error {
            background-color: " . get_option('ds_error_color') . " !important;
            }
        .toast-info {
            background-color: " . get_option('ds_info_color') . "!important;
        }
        .toast-warning {
            background-color: " . get_option('ds_warning_color') . " !important;
            }
        .toast-success {
             background-color: " . get_option('ds_success_color') . " !important;
            }
         </style>";
            wp_enqueue_style('notify', plugins_url('../assets/css/ds-notify.css', __FILE__));
        }

        

        // -- Function Name : load_js_file
        // -- Params : NULL
        // -- Purpose : Adding javascript files to site frontend for showing the notifications
        function load_js_file()
        {
            wp_enqueue_script('notify_toast', plugins_url('../assets/js/toast.js', __FILE__));
            wp_enqueue_script('notify', plugins_url('../assets/js/ds-notify.js', __FILE__));
        }

        // -- Function Name : anim_js
        // -- Params : NULL
        // -- Purpose : Toast notification animation and position.
        function anim_js()
        {
            
            if (get_option('ds_note_close'))
            {
                $close = 'true';
            }
            else
            {
                $close = 'false';
            }

            echo '<script type="text/javascript">
            toastr.options = {
              "closeButton": ' . $close . ',
              "newestOnTop": true,
              "showDuration": "'.get_option('ds_notify_delay_time').'",
              "timeOut": "'.get_option('ds_notify_time').'",
              "positionClass": "' . get_option('ds_note_pos') . '"
            }
            </script>';
        }

        // -- Function Name : admin_load_js_file
        // -- Params : NULL
        // -- Purpose : Adding javascript files for worpdress admin dashboard  .
        function admin_load_js_file()
        {
            wp_enqueue_script('notify_chosen', plugins_url('../assets/js/chosen.jquery.js', __FILE__));
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('notify_toast', plugins_url('../assets/js/ds-notify-admin.js', __FILE__));
        }

        

        // -- Function Name : view_notification
        // -- Params : $title, $msg, $type, $notetype
        // -- Purpose : Getting params from notify_append and showing the notificaiton in front end
        function view_notification($title, $msg, $type, $notetype)
        {
            $icon  = get_option('ds_notify_logo');
            $time  = get_option('ds_notify_time');
            $delay = get_option('ds_notify_delay_time');
            $sound = get_option('ds_notify_sound');
            
            if (get_option('ds_notify_web') && !is_admin())
            {
                $webnote = 'true';
            }
            else
            {
                $webnote = 'false';
            }

            
            if (get_option('ds_notify_internal') && !is_admin())
            {
                $internal = 'true';
            }
            else
            {
                $internal = 'false';
            }
            if (get_option('ds_note_cookie') && !is_admin())
            {
                $cookie = 'true';
            }
            else
            {
                $cookie = 'false';
            }
            
            if (!is_admin())
            {
                echo '<script type="text/javascript">showNotification("' . $title . '","' . $msg . '","' . $icon . '","' . $time . '","' . $delay . '","' . $webnote . '","' . $internal . '","' . $notetype . '","'.$sound.'","'.$cookie.'");</script>';
            }

        }

        // -- Function Name : __construct
        // -- Params : NULL
        // -- Purpose : __construct
        function __construct()
        {
            add_action('wp_footer', array($this,'load_js_file'));
            // Adding javascript to front end
            add_action('wp_footer', array($this,'notify_append'),30,30);
            // Loading notifications on site load.
            add_action('wp_footer', array($this,'anim_js'),20,10);
            // Loading notifications on site load.
            add_action('wp_head', array($this,'load_css_file'), 30, 0);
            // Loading notifications on site load.
            add_action('admin_head', array($this,'admin_register_head'));
            // Adding custom css file for plugin options
            add_action('admin_head', array($this,'admin_load_js_file'),30,0);
            // Loading notifications for site admin.
            add_action('publish_post', array($this,'save_notify'));
            // Saving notifications to table when post submits
            add_action('publish_notification', array($this,'save_custom_notify'));
            // Saving notifications to table when notification post type submits
            add_action('init',array($this,'custom_notification_type'));
            // Registering Notification type
            add_action('add_meta_boxes',array($this,'notify_type_selection'));
            // Adding custom fields to Notification type
            add_action('wp_trash_post', array($this,'delete_note'));
        }

    }