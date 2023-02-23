<?php
/**
 * Plugin Name: WooCommerce Dawnwing API
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly


/***********************************thankyou page generate order url*********************************************/

add_action('woocommerce_order_status_shipped', 'woocommerce_order_status_shipped', 999, 1);
function woocommerce_order_status_shipped($order_id)
{
    if (!get_post_meta($order_id, 'dawnwing_labels', true)) {
        // get order object and order details
        $order = new WC_Order($order_id);
//        $order_number = $order_id;
        $order_number = $order->order_number;
        if (!empty($order->get_items('shipping'))) {
            $shipping_method_instance_id = '';
            foreach ($order->get_items('shipping') as $item_id => $shipping_item_obj) {
                $shipping_method_instance_id = $shipping_item_obj->get_instance_id();
            }
        }
        $instance_id = $shipping_method_instance_id;

        if ($instance_id == 1) {
            $shipping_type = "ONX";
        } else {
            $shipping_type = "ECON";
        }


        // get product details//
        $items = $order->get_items();

        $parcels = [];
        foreach ($items as $item_id => $item_data) {
//            $id = $item_data['product_id'];
//            $variation_id = $item_data['variation_id'];
//            $product_name = $item_data['name'];
//            $quantity = $item_data['quantity'];
//
            // Product id
//            $product_id = $variation_id > 0 ? $variation_id : $id;
            // Product details
//            $product = wc_get_product($product_id);
//            $weight = wc_get_weight($product->get_weight(), 'lbs');
//            $height = wc_get_dimension($product->get_height(), 'in');
//            $width = wc_get_dimension($product->get_width(), 'in');
//            $length = wc_get_dimension($product->get_length(), 'in');
        }

        // Shipping address
        $shipping_address = $order->get_address('shipping');

        // Billing address
        $billing_address = $order->get_address('billing');
        $phone = $first_name = $last_name = '';
        extract($billing_address);
        $billing_phone = $phone;

        $address_1 = $address_2 = $city = $state = $postcode = '';
        extract($shipping_address);
        !strlen($address_2) > 0 ? $address_2 = 'empty' : '';

        // setup the data which has to be sent//
        $datawaybill = [
            "WaybillNo" => $order_number,
            "SendAccNo" => "xxxxxxxxxxxxxx",
            "SendCompany" => "xxxxxxxxxx",
            "SenDAdd1" => "xxxxxxxxxxx",
            "SendAdd2" => "xxxxxx",
            "SendAdd3" => "",
            "SendAdd4" => "Cape Town",
            "SendAdd5" => "8000",
            "SendContactPerson" => "xxxxxx",
            "SendWorkTel" => "xxxxxxxx",
            "RecCompany" => "",
            "RecAdd1" => "",
            "RecAdd2" => $address_1,
            "RecAdd3" => $city,
            "RecAdd4" => $address_2,
            "RecAdd5" => $postcode,
            "RecAdd7" => $company,
            "RecContactPerson" => $first_name . ' ' . $last_name,
            "RecHomeTel" => "",
            "RecWorkTel" => $billing_phone,
            "RecCell" => $billing_phone,
            'ParcelNo' => $order_number . "_1",
            "customerRef" => $order_number,
            "SpecialInstructions" => $order->get_customer_note(),
            "ServiceType" => $shipping_type,
            "Parcels" => [
                [
                    "WaybillNo" => $order_number,
                    "Length" => 26,
                    "Height" => 5,
                    "Width" => 23,
                    "Mass" => 1,
                    "ParcelDescription" => "xxxxxxx",
                    "ParcelNo" => $order_number . "_1",
                    "ParcelCount" => 1
                ]
            ],
            "CompleteWaybillAfterSave" => true
        ];

        $token = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $ch = curl_init('https://swatws.dawnwing.co.za/dwwebservices/v2/uat/api/waybill'); // Initialise cURL
        $authorization = "Authorization: Bearer " . $token; // Prepare the authorisation token
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization)); // Inject the token into the header
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datawaybill)); // Set the posted fields
        $response = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection
        update_post_meta($order_id, 'dawnwing_api_response', $response);
        $response_array = json_decode($response, true);
        if (isset($response_array, $response_array['data'])) {
            update_post_meta($order_id, 'dawnwing_labels', json_encode($response_array['data']));
        }
    }
}
