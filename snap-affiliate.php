<?php

/**
 * Plugin Name:       Snap Affiliate for Woocommerce
 * Plugin URI:        http://kavisoftek.in/snap-affiliate/
 * Description:       Plugin to Import Products As affiliate Links for Snapdeal
 * Version:           1.0.1
 * Author:            KaviSoftek
 * Author URI:        http://kavisoftek.in/
 * Text Domain:       snap-affiliate
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function snap_affiliate_install_woocommerce_admin_notice() {
    ?>
    <div class="error">
        <p><?php _e( 'Snap Affiliate is enabled but not effective. It requires WooCommerce in order to work.', 'snap-affiliate' ); ?></p>
    </div>
<?php
}

function snap_affiliate_install() {

    if ( !function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'snap_affiliate_install_woocommerce_admin_notice' );
    }
    else {       
    }
}

add_action( 'plugins_loaded', 'snap_affiliate_install', 11 );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-snap-affiliate-activator.php
 */
function activate_snap_affiliate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-snap-affiliate-activator.php';
	Snap_Affiliate_Activator::activate();	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-snap-affiliate-deactivator.php
 */
function deactivate_snap_affiliate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-snap-affiliate-deactivator.php';
	Snap_Affiliate_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_snap_affiliate' );
register_deactivation_hook( __FILE__, 'deactivate_snap_affiliate' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-snap-affiliate.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_snap_affiliate() {

	$plugin = new Snap_Affiliate();
	$plugin->run();

}

run_snap_affiliate();


