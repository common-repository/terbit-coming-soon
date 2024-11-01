<?php

/**

 * Config

 *

 * @package WordPress

 * @subpackage Terbit_Coming_Soon_Page

 * @since 0.1

 */



if ( ! class_exists( 'squaretrix_Ultimate_Coming_Soon_Page' ) ) {	

    class squaretrix_Ultimate_Coming_Soon_Page extends squaretrix_Framework {

	

		private $coming_soon_rendered = false; 

        

        /**

         *  Extend the base construct and add plugin specific hooks

         */

        function __construct(){

            $squaretrix_comingsoon_options = get_option('squaretrix_comingsoon_options');

            parent::__construct();

            add_action( 'wp_ajax_squaretrix_comingsoon_refesh_list', array(&$this,'refresh_list'));

            if((isset($squaretrix_comingsoon_options['comingsoon_enabled']) && in_array('1',$squaretrix_comingsoon_options['comingsoon_enabled'])) || (isset($_GET['cs_preview']) && $_GET['cs_preview'] == 'true')){

                if(function_exists('bp_is_active')){

                    add_action('template_redirect', array(&$this,'render_comingsoon_page'),9);

                }else{

                    add_action('template_redirect', array(&$this,'render_comingsoon_page'));

                }

                add_action( 'admin_bar_menu',array( &$this, 'admin_bar_menu' ), 1000 );

            }

            add_action( 'wp_ajax_squaretrix_mailinglist_callback', array(&$this,'ajax_mailinglist_callback') );

            add_action( 'wp_ajax_nopriv_squaretrix_mailinglist_callback', array(&$this,'ajax_mailinglist_callback') );

            add_action( 'wp_ajax_squaretrix_email_export_delete', array(&$this,'email_export_delete') );

            add_action( 'wp_enqueue_scripts', array(&$this,'add_frontent_scripts') );

            add_action( 'sc_head','wp_enqueue_scripts',1);

            add_filter( 'plugin_action_links', array(&$this,'plugin_action_links'), 10, 2);

            if($squaretrix_comingsoon_options['comingsoon_mailinglist'] == 'database'){

                $this->email_database_setup();

            }

        }



        /**

        * Display admin bar when active

        */



        function admin_bar_menu(){

            global $wp_admin_bar;



            /* Add the main siteadmin menu item */

                $wp_admin_bar->add_menu( array(

                    'id'     => 'debug-bar',

                    'href' => admin_url().'options-general.php?page=squaretrix_coming_soon',

                    'parent' => 'top-secondary',

                    'title'  => apply_filters( 'debug_bar_title', __('Coming Soon Mode Active', 'terbit-coming-soon') ),

                    'meta'   => array( 'class' => 'ucsp-mode-active' ),

                ) );

        }

        

        /**

         * Display the coming soon page

         */

        function render_comingsoon_page() {

                // Return if a login page

                if(preg_match("/login/i",$_SERVER['REQUEST_URI']) > 0){

                    return false;

                }



	            if(!is_admin()){

	                if(!is_feed()){

	                    if ( !is_user_logged_in() || (isset($_GET['cs_preview']) && $_GET['cs_preview'] == 'true')) {

	                        $this->coming_soon_rendered = true;

							$file = plugin_dir_path(__FILE__).'template/template-coming-soon.php';

	                        include($file);

	                    }

	                }

	            }

        }

        

        /**

         * Load frontend scripts

         */

        function add_frontent_scripts() {

				if($this->coming_soon_rendered){

	                //wp_enqueue_script( 'modernizr', plugins_url('inc/template/modernizr.js',dirname(__FILE__)), array(),'1.7' );  

	                wp_enqueue_script( 'squaretrix_coming_soon_script', plugins_url('inc/template/script.js',dirname(__FILE__)), array( 'jquery' ),$this->plugin_version, true );  

	                $data = array( 

	                    'msgdefault' => __( 'Enter Your Email' , 'terbit-coming-soon'),

	                    'msg500' => __( 'Error :( Please try again.' , 'terbit-coming-soon'),

	                    'msg400' => __( 'Please enter a valid email.' , 'terbit-coming-soon'),

	                    'msg200' => __( "You'll be notified soon!" , 'terbit-coming-soon'),

                

	                );

	                wp_localize_script( 'squaretrix_coming_soon_script', 'squaretrix_err_msg', $data );

            	}

        }

        

        /**

         * Create Database to Store Emails

         */

        function email_database_setup() {

            global $wpdb;

            $tablename = $wpdb->prefix . "squaretrix_emails";

            if( $wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename ){

                $sql = "CREATE TABLE `$tablename` (

                    `id` int(10) unsigned NOT NULL auto_increment,

                    `email` varchar(255) NOT NULL,

                    `created` timestamp NOT NULL default CURRENT_TIMESTAMP,

                    PRIMARY KEY (`id`)

                );";

            

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

 

                dbDelta($sql);

            }

            

        }

        

        

        /**

         *  Callback for mailing list to be displayed in the admin area.

         */

        function refresh_list(){

            if(check_ajax_referer('squaretrix_comingsoon_refesh_list')){

                $api_key = $_GET['apikey'];

                delete_transient('squaretrix_comingsoon_mailinglist');

                $mailchimp_lists = $this->get_mailchimp_lists($api_key);

                echo json_encode($mailchimp_lists);

                exit();

            }

        }

        

        /**

         *  Get List from MailChimp

         */

        function get_mailchimp_lists($apikey){

            $mailchimp_lists = unserialize(get_transient('squaretrix_comingsoon_mailinglist'));

            if($mailchimp_lists === false){

                require_once 'lib/MCAPI.class.php';

                $squaretrix_comingsoon_options = get_option('squaretrix_comingsoon_options');

                if(!isset($apikey)){

                    $apikey = $squaretrix_comingsoon_options['comingsoon_mailchimp_api_key'];

                }

                $api = new MCAPI($apikey);



                $retval = $api->lists();

                if ($api->errorCode){

                	$mailchimp_lists['false'] = __("Unable to load lists, check your API Key!", 'terbit-coming-soon');

                } else {



                	foreach ($retval['data'] as $list){

                	    $mailchimp_lists[$list['id']] = 'MailChimp - '.$list['name'];

                	}

                	set_transient('squaretrix_comingsoon_mailinglist',serialize( $mailchimp_lists ),86400);

                }

            }

            return $mailchimp_lists;

        }

        

        /**

         *  Display mailing list field in admin

         */

        function callback_mailinglist_field() {

            $options = get_option('squaretrix_comingsoon_options');

            $id = 'comingsoon_mailinglist';

            $setting_id = 'squaretrix_comingsoon_options';

            //$option_values = $this->get_mailchimp_lists(null);

            $option_values['none'] = 'Do not display an Email SignUp';

            $option_values['feedburner'] = 'FeedBurner';

            //$option_values['database'] = 'Database';

            $ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=squaretrix_comingsoon_refesh_list','squaretrix_comingsoon_refesh_list'));

            echo "<select id='$id' class='' name='{$setting_id}[$id]'>";

    	    foreach($option_values as $k=>$v){

    	        echo "<option value='$k' ".($options[$id] == $k ? 'selected' : '').">$v</option>";

    	    }

    	    echo "</select><!--<button id='comingsoon_mailinglist_refresh' type='button' class='button-secondary'>Refresh</button>-->

            <br><small class='description'>More Options in the Pro Version :)</small>

            <script type='text/javascript'>

            jQuery(document).ready(function($) {

                $('#comingsoon_mailinglist_refresh').click(function() {

                    apikey = $('#comingsoon_mailchimp_api_key').val();

                    $.post('{$ajax_url}&apikey='+apikey, function(data) {

                      lists = $.parseJSON(data);

                      if(lists){

                          $('#comingsoon_mailinglist').html('');

                      }

                      $.each(lists,function(k,v){

                          $('#comingsoon_mailinglist').prepend('<option value=\"'+k+'\">'+v+'</option>');

                      });

                      $('#comingsoon_mailinglist_refresh').html('Lists Refreshed');

                    });

                }); 

            });

            </script>

            ";

        }

        

        /**

         * Subscribe User to Mailing List or return an error.

         */

        function ajax_mailinglist_callback() {

            //if ( empty($_POST) || !wp_verify_nonce($_GET['noitfy_nonce'],'squaretrix_comingsoon_callback') )

            if(empty($_GET['email']))

            {

               header('HTTP/1.1 403 Forbidden',true,403);

               exit;

            }

            else

            {   

                $squaretrix_comingsoon_options = get_option('squaretrix_comingsoon_options');

                $email = $_GET['email'];

                $errcode = 0;

                // If not email exit and return 400

                if(is_email($email) != $email){

                    die('400');

                }



                // If databse option update db

                if($squaretrix_comingsoon_options['comingsoon_mailinglist'] == 'database'){

                    global $wpdb;

                    $tablename = $wpdb->prefix . "squaretrix_emails";

                    $values = array(

                        'email' => $email

                    );

                    $format_values = array(

                        '%s'

                    );

                    $sql = "SELECT `email` FROM $tablename WHERE email = %s";

                    $safe_sql = $wpdb->prepare($sql,$email);

                    $select_result =$wpdb->get_var($safe_sql);

                    if($select_result != $email){

                        $insert_result = $wpdb->insert(

                            $tablename,

                            $values,

                            $format_values

                        );

                    }

                    

                    if($insert_result != false){

                        die('200');

                    }

                    exit;

                }

                

                // if mailchimp option

                require_once 'lib/MCAPI.class.php';

                $squaretrix_comingsoon_options = get_option('squaretrix_comingsoon_options');

                $apikey = $squaretrix_comingsoon_options['comingsoon_mailchimp_api_key'];

                $api = new MCAPI($apikey);

                $listId = $squaretrix_comingsoon_options['comingsoon_mailinglist'];



                $retval = $api->listSubscribe( $listId, $email, $merge_vars=NULL,$email_type='html', $double_optin=true);

                if($retval == false){

                    die('400');

                }

                if ($api->errorCode){

                	die('500');

                } else {

                    die('200');

                }  

                exit;

            }

        }

        

        /**

         * Incentive Section explanation Text

         */

        function section_incentive() {

        	echo '<p class="squaretrix_section_explanation">'.__('Offer your visitors incentives such as coupons codes, free ebook, free software, etc. in exchange for their email.

        	Just fill out either or both of the fileds below and the information will be displayed after you have succesfully captured their email.

        	', 'terbit-coming-soon').'</p>';

        }

        

        /**

        * Email Export

        */

        function email_export_delete(){

            if(check_ajax_referer('squaretrix_email_export_delete')){

                if($_GET['method'] == 'export'){

                    global $wpdb;

                	$csv_output .= "Email,Created";

                	$csv_output .= "\n";

                    $tablename = $wpdb->prefix . "squaretrix_emails";

                    $sql = "SELECT email,created FROM " . $tablename;

                    $results = $wpdb->get_results($wpdb->prepare($sql));

            

                     foreach ($results as $result) {

                     	$csv_output .= $result->email ."," . $result->created ."\n";

                     }

            

                     $filename = $file."emails_".date("Y-m-d_H-i",time());

                     header("Content-type: text/plain");

                     header("Content-disposition: attachment; filename=".$filename.".csv");

                     print $csv_output;

                     exit;

                }elseif($_GET['method'] == 'delete'){

                    global $wpdb;

                	$tablename = $wpdb->prefix . "squaretrix_emails";

                   	$sql = "TRUNCATE " . $tablename;

                	$result = $wpdb->query($sql);

                	if($result){

                	    echo '200';

                	}

                	exit;

                }

            }else{

                header('HTTP/1.1 403 Forbidden',true,403);

                exit;

            }

        }

        

        /**

         * Callback Email Export

         */

        function callback_database_field(){   

            $ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=squaretrix_email_export_delete','squaretrix_email_export_delete'));

            $data = array( 'delete_confirm' => __( 'Are you sure you want to DELETE all emails?' , 'terbit-coming-soon') );

            wp_localize_script( 'squaretrix_coming_soon_script', 'squaretrix_object', $data );

            echo "<button id='comingsoon_export_emails' type='button' class='button-secondary'>Export</button><button id='comingsoon_delete_emails' type='button' class='button-secondary'>Delete</button>

            <br><small class='description'></small>

            <script type='text/javascript'>

            jQuery(document).ready(function($) {

                $('#comingsoon_export_emails').click(function() {

                    window.location.href = '{$ajax_url}&method=export';

                });

                $('#comingsoon_delete_emails').click(function() {

                    if(confirm(squaretrix_object.delete_confirm)){

                        $.get('{$ajax_url}&method=delete', function(data) {

                           $('#comingsoon_delete_emails').html('Emails Deleted').attr('disabled','disabled');

                        });

                    }

                }); 

            });

            </script>

            ";

        }

        

        function plugin_action_links($links, $file) {

            $plugin_file = 'terbit-coming-soon/terbit-coming-soon.php';

            if ($file == $plugin_file) {

                $settings_link = '<a href="options-general.php?page=squaretrix_coming_soon">Settings</a>';

                array_push($links, $settings_link);

            }

            return $links;

        }



        

        

        // End of Class					

    }

}



/**

 * Config

 */

$squaretrix_comingsoon = new squaretrix_Ultimate_Coming_Soon_Page();

$squaretrix_comingsoon->plugin_base_url = plugins_url('',dirname(__FILE__));

$squaretrix_comingsoon->plugin_version = '0.1';

$squaretrix_comingsoon->plugin_type = 'free';

$squaretrix_comingsoon->plugin_short_url = 'http://bit.ly/pPUKHe';

$squaretrix_comingsoon->plugin_name = __('Coming Soon', 'terbit-coming-soon');

$squaretrix_comingsoon->menu[] = array("type" => "add_options_page",

                         "page_name" => __("Terbit Coming Soon", 'terbit-coming-soon'),

                         "menu_name" => __("Terbit Coming Soon", 'terbit-coming-soon'),

                         "capability" => "manage_options",

                         "menu_slug" => "squaretrix_coming_soon",

                         "callback" => array($squaretrix_comingsoon,'option_page'),

                         "icon_url" => plugins_url('framework/squaretrix-icon-16x16.png',dirname(__FILE__)),

                        );

                        

/**

 *  Do not replace validate_function. Create unique id and copy menu slug 

 * from menu config. Create 'validate_function' if using custom validation.

 */

$squaretrix_comingsoon->options[] = array( "type" => "setting",

                "id" => "squaretrix_comingsoon_options",

				"menu_slug" => "squaretrix_coming_soon"

				);



/**

 * Create unique id,label, create 'desc_callback' if you need custom description, attach

 * to a menu_slug from menu config.

 */

$squaretrix_comingsoon->options[] = array( "type" => "section",

                "id" => "squaretrix_section_coming_soon",

				"label" => __("Settings", 'terbit-coming-soon'),	

				"menu_slug" => "squaretrix_coming_soon");





/**

 * Choose type, id, label, attache to a section and setting id.

 * Create 'callback' function if you are creating a custom field.

 * Optional desc, default value, class, option_values, pattern

 * Types image,textbox,select,textarea,radio,checkbox,color,custom

 */

$squaretrix_comingsoon->options[] = array( "type" => "checkbox",

                "id" => "comingsoon_enabled",

				"label" => __("Enable", 'terbit-coming-soon'),

				"desc" => sprintf(__("Enable if you want to display a coming soon page to visitors. Users who are logged in will not see the coming soon page, this means you.  <a href='%s/?cs_preview=true'>Preview</a>", 'terbit-coming-soon'),home_url()),

                "option_values" => array('1'=>__('Yes', 'terbit-coming-soon')),

				"section_id" => "squaretrix_section_coming_soon",

				"setting_id" => "squaretrix_comingsoon_options",

				

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_comstime",

				"label" => __("Coming Soon Date", 'terbit-coming-soon'),

				"desc" => __("30 November 2013 12:00:00.", 'terbit-coming-soon'),

				"section_id" => "squaretrix_section_coming_soon",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "30 November 2013 12:00:00",

				);				

$squaretrix_comingsoon->options[] = array( "type" => "image",

                "id" => "comingsoon_image",

				"label" => __("Image", 'terbit-coming-soon'),

				"desc" => __("Upload a logo or teaser image (or) enter the url to your image.", 'terbit-coming-soon'),

				"section_id" => "squaretrix_section_coming_soon",

				"setting_id" => "squaretrix_comingsoon_options",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_headline",

				"label" => __("Headline", 'terbit-coming-soon'),

				"desc" => __("Write a headline for your coming soon page. Tip: Avoid using 'Coming Soon'.", 'terbit-coming-soon'),

				"section_id" => "squaretrix_section_coming_soon",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "This is demo text where we can add heading",

				);

$squaretrix_comingsoon->options[] = array( "type" => "wpeditor",

                "id" => "comingsoon_description",

				"label" => __("Description", 'terbit-coming-soon'),

				"desc" => __("Tell the visitor what to expect from your site.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_coming_soon",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => '<p class="intro-text">We have been spending long hours in order to launch our new website. <br/>We will offer freebies, a brand logo and featured content of our latest works & Services. We are always with you!</p>',

				);	



$squaretrix_comingsoon->options[] = array( "type" => "custom",

                "id" => "comingsoon_mailinglist",

                "label" => __("Mailing List", 'terbit-coming-soon'),

                "callback" => array($squaretrix_comingsoon,'callback_mailinglist_field'),

				"section_id" => "squaretrix_section_coming_soon",

				"setting_id" => "squaretrix_comingsoon_options",

				

				);	



$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_feedburner_address",

                "label" => __("FeedBurn Address", 'terbit-coming-soon'),

                "desc" => __("Enter the part after http://feeds2.feedburner.com/ <a href='http://wordpress.org/extend/plugins/terbit-coming-soon/faq/'' target='_blanks'> Learn how</a> to use FeedBurner to collect emails.", 'terbit-coming-soon'),

                "section_id" => "squaretrix_section_coming_soon",

                "setting_id" => "squaretrix_comingsoon_options",

                );



$squaretrix_comingsoon->options[] = array( "type" => "textarea",

                "id" => "comingsoon_customhtml",

				"label" => __("Custom HTML", 'terbit-coming-soon'),

				"desc" => __("Enter any custom html or javascript that you want outputted. You can also enter you Google Analytics code.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_coming_soon",

				"setting_id" => "squaretrix_comingsoon_options",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_form_notice",

				"label" => __("Form Notice", 'terbit-coming-soon'),

				"desc" => __("Enter any notification text here.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_coming_soon",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "* Sign up now and be the first to find out when we launch! We never spam!",

				);

$squaretrix_comingsoon->options[] = array( "type" => "section",

                "id" => "squaretrix_section_style",

				"label" => __("Style", 'terbit-coming-soon'),	

				"menu_slug" => "squaretrix_coming_soon");

				



$squaretrix_comingsoon->options[] = array( "type" => "color",

                "id" => "comingsoon_theme_color",

				"label" => __("Theme Color", 'terbit-coming-soon'),

				"section_id" => "squaretrix_section_style",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#0ea3e2",

				);		

				

$squaretrix_comingsoon->options[] = array( "type" => "color",

                "id" => "comingsoon_custom_bg_color",

				"label" => __("Background Color", 'terbit-coming-soon'),

				"section_id" => "squaretrix_section_style",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#ffffff",

				);
$squaretrix_comingsoon->options[] = array( "type" => "radio",

                "id" => "comingsoon_box_bg",

				"label" => __("Coming Soon Box Background", 'terbit-coming-soon'),

				"option_values" => array(''=>__('Black', 'terbit-coming-soon'),'1'=>__('White', 'terbit-coming-soon')),

				"desc" => __("", 'terbit-coming-soon'),

				"default_value" => "",

				"section_id" => "squaretrix_section_style",

				"setting_id" => "squaretrix_comingsoon_options",

				);					
$squaretrix_comingsoon->options[] = array( "type" => "checkbox",

                "id" => "comingsoon_no_background",

                "label" => __("No Background Image", 'terbit-coming-soon'),

                "desc" => sprintf(__("This will remove background image.", 'terbit-coming-soon'),home_url()),

                "option_values" => array('1'=>__('Yes', 'terbit-coming-soon')),

                "section_id" => "squaretrix_section_style",

                "setting_id" => "squaretrix_comingsoon_options",
				

                );				

$squaretrix_comingsoon->options[] = array( "type" => "image",

                "id" => "comingsoon_custom_bg_image",

				"label" => __("Background Image", 'terbit-coming-soon'),

				"section_id" => "squaretrix_section_style",

				"setting_id" => "squaretrix_comingsoon_options",

				"desc" => __('Upload an optional background image (or) enter the url to your image. This will override the color above if set.', 'terbit-coming-soon'),

				);

$squaretrix_comingsoon->options[] = array( "type" => "checkbox",

                "id" => "comingsoon_background_strech",

                "label" => __("Stretch Background Image", 'terbit-coming-soon'),

                "desc" => sprintf(__("This will stretch your background image to match any browser size.", 'terbit-coming-soon'),home_url()),

                "option_values" => array('1'=>__('Yes', 'terbit-coming-soon')),

                "section_id" => "squaretrix_section_style",

                "setting_id" => "squaretrix_comingsoon_options",

                );

$squaretrix_comingsoon->options[] = array( "type" => "radio",

                "id" => "comingsoon_font_color",

				"label" => __("Font Color", 'terbit-coming-soon'),

				"option_values" => array('black'=>__('Black', 'terbit-coming-soon'),'gray'=>__('Gray', 'terbit-coming-soon'),'white'=>__('White', 'terbit-coming-soon')),

				"desc" => __("", 'terbit-coming-soon'),

				"default_value" => "black",

				"section_id" => "squaretrix_section_style",

				"setting_id" => "squaretrix_comingsoon_options",

				);								

				

$squaretrix_comingsoon->options[] = array( "type" => "section",

                "id" => "squaretrix_section_social",

				"label" => __("Social Address", 'terbit-coming-soon'),	

				"menu_slug" => "squaretrix_coming_soon");

								

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_call_us",

				"label" => __("Call Us", 'terbit-coming-soon'),

				"desc" => __("Enter Phone Number.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "0401 234 5678",

				

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_email",

				"label" => __("Enter Email Address", 'terbit-coming-soon'),

				"desc" => __("Enter Email Address.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "info@email.com",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_facebook",

				"label" => __("Enter Facebook Address", 'terbit-coming-soon'),

				"desc" => __("Enter Facebook Address.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_twitter",

				"label" => __("Enter Twitter Address", 'terbit-coming-soon'),

				"desc" => __("Enter Twitter Address.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_google_plus",

				"label" => __("Enter Google Plus Address", 'terbit-coming-soon'),

				"desc" => __("Enter Google Plus Address.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_pinterest",

				"label" => __("Enter Pinterest Address", 'terbit-coming-soon'),

				"desc" => __("Enter Pinterest Address.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_reddit",

				"label" => __("Enter Reddit Address", 'terbit-coming-soon'),

				"desc" => __("Enter Reddit Address.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_stumbleupon",

				"label" => __("Enter Stumbleupon Address", 'terbit-coming-soon'),

				"desc" => __("Enter Stumbleupon Address.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#",

				);

$squaretrix_comingsoon->options[] = array( "type" => "textbox",

                "id" => "comingsoon_rss",

				"label" => __("Enter Rss Feed Address", 'terbit-coming-soon'),

				"desc" => __("Enter Rss Feed Address.", 'terbit-coming-soon'),

				"class" => "large-text",

				"section_id" => "squaretrix_section_social",

				"setting_id" => "squaretrix_comingsoon_options",

				"default_value" => "#",

				);																																									

 			



?>