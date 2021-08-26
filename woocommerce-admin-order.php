<?php
/*
 * Plugin Name: WooCommerce Admin Order
 * Plugin URI:        http://saberhr.me/
 * Description:       Create a custom order page for admin
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Saber Hossen Rabbani
 * Author URI:        http://saberhr.me/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       woo_admin_order
 */


function woo_admin_order_textdomain_load() {
	load_plugin_textdomain( 'woo_admin_order', false, plugin_dir_url( __FILE__ ) . '/languages' );
}

add_action( 'plugins_loaded', 'woo_admin_order_textdomain_load' );


function bangla_phonetic_admin_assets ($screen) {
	if ('woo-admin-order.php' == $screen || 'post-new.php' == $screen) {

		wp_enqueue_script('bootstrap-css', plugin_dir_url( __FILE__ ) . '/assets/js/phonetic.driver.js', null, '1.0.0', true);
		wp_enqueue_script('bangla-phonetic-engine-js', plugin_dir_url( __FILE__ ) . '/assets/js/engine.js', null, '1.0.0', true);
		wp_enqueue_script('bangla-phonetic-quick-tag-js', plugin_dir_url( __FILE__ ) . '/assets/js/qt.js', null, '1.0.0', true);
	}
}

add_action( 'admin_enqueue_scripts', 'bangla_phonetic_admin_assets' );

function woo_admin_order_menu_page() {
	/*$user = wp_get_current_user();
	echo "<pre>";
	print_r($user);
	echo "</pre>";

	die();*/
	add_menu_page( 'Create Order', 'Creat Order', 'activate_plugins', 'woo-admin-order.php', 'woo_admin_page', 'dashicons-tickets', 6 );
}


add_action( 'admin_menu', 'woo_admin_order_menu_page' );


function woo_admin_page() {
	?>

    <div class="wrap">
        <h1 class="wp-heading-inline">Creat an Order</h1>


	<?php

	if ( ! class_exists( 'WooCommerce' ) ) {
		?>
        <h1 class="">Please setup your woocommerce store</h1>
		<?php
	} else {

		?>
        <div style="display: flex; justify-items: center; align-items: center">
            <div style="width: 500px; background-color: white;">
                <table>
                    <thead>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="vertical-align: middle">
                            <img src="http://localhost/woocom/wp-content/uploads/2021/08/hoodie_3_front-150x150.jpg" alt="" width="100"> Denim Hoody
                        </td>
                        <td>
                            <input type="number" name="quantity">
                        </td>
                        <td>$4525</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
		<?php
	}
	?>
    </div>
	<?php


}