<?php
/*
Plugin Name: Terbit Coming Soon
Plugin URI: http://www.uttammitra.com
Description: Creates a Coming Soon or Launch page for your website.
Version: 1.9.5
Author: Uttam Mitra
Author URI: http://www.uttammitra.com
License: GPLv2
*/

/**
 * Init
 *
 * @package WordPress
 * @subpackage Terbit_Coming_Soon
 * @since 0.1
 */

/**
 * Require config to get our initial values
 */
 
load_plugin_textdomain('terbit-coming-soon',false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

require_once('framework/framework.php');
require_once('inc/config.php');

/**
 * Upon activation of the plugin, see if we are running the required version and deploy theme in defined.
 *
 * @since 0.1
 */
function squaretrix_ucsp_activation() {
    if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( __FILE__  );
        wp_die( __('WordPress 3.0 and higher required. The plugin has now disabled itself. On a side note why are you running an old version :( Upgrade!','terbit-coming-soon') );
    }
}
