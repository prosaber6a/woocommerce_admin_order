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


function woo_admin_order_textdomain_load()
{
    load_plugin_textdomain('woo_admin_order', false, plugin_dir_url(__FILE__) . '/languages');
}

add_action('plugins_loaded', 'woo_admin_order_textdomain_load');


function woo_admin_order_assets($screen)
{

    if ($screen == 'toplevel_page_woo-admin-order') {

        wp_enqueue_style('select2-css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_style('tailwind-css', '//unpkg.com/tailwindcss@^2/dist/tailwind.min.css');
//        wp_enqueue_style('woocommerce-admin-order-css', plugin_dir_url(__FILE__) . '/assets/css/woocommerce-admin-order.css', null, time());
        wp_enqueue_script('select2-js', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
        wp_enqueue_script( 'woocommerce-admin-order-js', plugin_dir_url(__FILE__) . '/assets/js/woocommerce-admin-order.js', array(), time(), true );
    }
}

add_action('admin_enqueue_scripts', 'woo_admin_order_assets');

function woo_admin_order_menu_page()
{
    add_menu_page('Create Order', 'Create Order', 'activate_plugins', 'woo-admin-order.php', 'woo_admin_page', 'dashicons-tickets', 6);
}


add_action('admin_menu', 'woo_admin_order_menu_page');


function woo_admin_page()
{


    ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">Create an Order</h1>

        <div class="container flex justify-center">


            <?php

            if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))):
                ?>
                <h1 class="text-red">Please setup your woocommerce store</h1>
                <?php
            else:

                global $woocommerce;
                $countries_obj = new WC_Countries();
                $countries = $countries_obj->__get('countries');

                $products = wc_get_products([
                    'status' => ['publish'],
                    'return' => 'objects',
                    'paginate' => false,
                    'limit' => -1,
                    'stock_status' => 'instock',
                ]);


        
                $products_arr = [];
                foreach ($products as $product){
                    $_thumbnail = wp_get_attachment_image_url($product->image_id, 'thumbnail');
                    $_product_arr['id'] = $product->id;
                    $_product_arr['img'] = $_thumbnail;
                    $_product_arr['price'] = $product->price;
                    $_product_arr['name'] = $product->name;
                    $_product_arr['sku'] = $product->sku;
                    
                    $products_arr[] = $_product_arr;
                }

                wp_localize_script('woocommerce-admin-order-js', 'products', json_encode($products_arr));
                wp_localize_script('woocommerce-admin-order-js', 'action_url', json_encode([admin_url('admin-ajax.php')]));
                                


                ?>

                <div class="w-2/4 bg-white p-3">

                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" id="form">

                        <?php
                        wp_nonce_field('woo_admin_order_nonce', 'nonce');
                        ?>


                        <input type="text" id="product-sku-search" autocomplete="off"  placeholder="select product by sku" class=" w-full rounded mb-6">
                        <div class="w-full relative hidden" id="search-container">
                            <ul id="search-result" class="absolute z-20 overflow-y-scroll overflow-x-hidden h-56 bg-white w-full border border-black shadow-2xl -mt-7">
                            
                            </ul>
                        </div>


                        
                                

                        <table id="orderitems_table" class="table-auto w-full">
                            <thead>
                            <tr class="border-2 border-black border-l-0 border-r-0 border-t-0 mb-1">
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <th>Total: <span id="total_price">$0</span></th>
                            </tr>
                            </tfoot>
                        </table>

                        <h2 class="border-b-2 border-gray-400 mb-4 mt-12 text-2xl text-center">Customer</h2>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <input type="text" id="cust_first_name" class="w-full" placeholder="First Name">
                            </div>
                            <div>
                                <input type="text" id="cust_last_name" class="w-full" placeholder="Last Name">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <div>
                                <input type="text" id="cust_email" class="w-full" placeholder="Email">
                            </div>
                            <div>
                                <input type="text" id="cust_phone" class="w-full" placeholder="Phone">
                            </div>
                        </div>


                        <h2 class="border-b-2 border-gray-400 mb-4 mt-12 text-2xl text-center">Address</h2>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <input type="text" id="address_line" class="w-full" placeholder="Address Line">
                            </div>
                            <div>
                                <input type="text" id="city" class="w-full" placeholder="City">
                            </div>
                        </div>
                        <div class="mt-2 text-center">

                            <select id="country" class="w-full">
                                <option value="" disabled selected>Select country</option>
                                <?php foreach ($countries as $key => $value): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <div class="mt-4 text-center">
                            <button type="button" id="submit"
                                    class="px-6 py-4 bg-indigo-400 text-white text-xl hover:bg-indigo-800">Save Order
                            </button>
                        </div>
                    </form>
                </div>

                <?php endif; ?>

                
        </div>
    </div>
    <?php


}


add_action('wp_ajax_woo_admin_order_submit', function () {

    global $woocommerce;

    $nonce = $_POST['nonce'];

    $verify = wp_verify_nonce($nonce, 'woo_admin_order_nonce');

    if (!$verify) {
        echo json_encode(['error' => 'Sorry! You are not verified']);

    } else {


        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $address_line = sanitize_text_field($_POST['address_line']);
        $city = sanitize_text_field($_POST['city']);
        $country = sanitize_text_field($_POST['country']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);


        $address = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'address_1' => $address_line,
            'city' => $city,
            'country' => $country
        );

        // Now we create the order
        $order = wc_create_order();

        // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php


        $product_data = $_POST['product_data'];


        foreach ($product_data as $key) {
            $order->add_product(wc_get_product(intval($key['product_id'])), intval($key['quantity']));
        }

        $order->set_address($address, 'billing');
        $order->set_address($address, 'shipping');
        $order->calculate_totals();
        $order->update_status("Completed", 'Imported order', TRUE);


        echo json_encode(['success' => 'Successfully order inserted']);
    }
});