<?php
/**
 * @version 1.0
 * @package Dawn Wing Waybill Send API
 * @author Byron Jacobs (byron@mywebs.co.za)
 *
 * Websites: https://mywebs.co.za
 */

add_action( 'woocommerce_order_status_processing','action_woocommerce_new_order'); 
add_action( 'woocommerce_order_status_on-hold','action_woocommerce_new_order'); 
add_action('woocommerce_update_order', 'action_woocommerce_new_order');

// add_action( 'woocommerce_new_order','action_woocommerce_new_order'); 

function action_woocommerce_new_order($order_id ) { 
$curl = curl_init();
$order = new WC_Order( $order_id ); 
$phone = $order->billing_phone;
$company = $order->shipping_company;
$id = $order->post->ID;// Get the order ID
$order_status = $order->get_status();
$order_number = $order->order_number;
$order_note = $order->get_customer_note();
if(!empty( $order->get_items( 'shipping' ))){
$shipping_method_instance_id ='';
foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
$shipping_method_instance_id .= $shipping_item_obj->get_instance_id(); 
}
}
$instance_id = $shipping_method_instance_id;


// set shipping fields //

if( $instance_id == 1 )
{
$_shipping_type = "ONX";
}
else{
$_shipping_type = "ECON";
}

$siteURL='http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['HTTP_HOST'].'/';
$order_data = $order->get_data(); 

$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
$order_date_modified = $order_data['date_modified']->date('Y-m-d H:i:s');

$shipping_cost = $order->get_total_shipping();

// set the address fields//
$user_id = $order->user_id;
$address_fields = array('country',
'title',
'first_name',
'last_name',
'company',
'address_1',
'address_2',
'address_3',
'address_4',
'city',
'state',
'postcode');

// set the user id//
$user_id = $order->user_id;

$address = array();
if(is_array($address_fields)){
foreach($address_fields as $field){
$address['billing_'.$field] = get_user_meta( $user_id, 'billing_'.$field, true );
$address['shipping_'.$field] = get_user_meta( $user_id, 'shipping_'.$field, true );
}
}

// get product details//
$items = $order->get_items();

$item_name = array();
$item_qty = array();
$item_price = array();
$item_sku = array();
$total_tax = array();

foreach( $items as $key => $item){
$item_name[] = $item['name'];
$item_qty[] = $item['qty'];
$item_price[] = $item['line_subtotal'];
$item_tax[] = $item['line_subtotal_tax'];
$item_id = $item['product_id'];
$product = new WC_Product($item_id);
$item_sku[] = $product->get_sku();
$total_tax[] = $item->get_total_tax();

}
$order_discount_total = $order_data['discount_total'];
$order_discount_tax = $order_data['discount_tax'];
$order_shipping_total = $order_data['shipping_total'];
$order_shipping_tax = $order_data['shipping_tax'];
$order_total = $order_data['total'];
$order_total_tax = $order_data['total_tax'];
$order_payment_method = $order_data['payment_method'];
$order_payment_method_title = $order_data['payment_method_title'];
$order_customer_id = $order_data['customer_id'];


$a = 0;
$b = $item_name[1];
$c = $item_name[2];
$d = $item_name[3];
$e = $item_name[4];
$f = $item_name[5];
$g = $item_name[6];
$h = $item_name[7];
$i = $item_name[8];
$j = $item_name[9];

$item0 = $item_sku[0] . ',' . $item_name[0] . ',' . $item_qty[0] . ',' . $item_price[0];
if (isset($b)) {
$item1 = ', ' . $item_sku[1] . ',' . $item_name[1] . ',' . $item_qty[1] . ',' . $item_price[1];
}
if (isset($c)) {
$item2 =  ', ' . $item_sku[2] . ',' . $item_name[2] . ',' . $item_qty[2] . ',' . $item_price[2];
}
if (isset($d)) {
$item3 =  ', ' . $item_sku[3] . ',' . $item_name[3] . ',' . $item_qty[3] . ',' . $item_price[3];
}
if (isset($e)) {
$item4 =  ', ' . $item_sku[4] . ',' . $item_name[4] . ',' . $item_qty[4] . ',' . $item_price[4];
}
if (isset($f)) {
$item5 =  ', ' . $item_sku[5] . ',' . $item_name[5] . ',' . $item_qty[5] . ',' . $item_price[5];
}
if (isset($g)) {
$item6 =  ', ' . $item_sku[6] . ',' . $item_name[6] . ',' . $item_qty[6] . ',' . $item_price[6];
}
if (isset($h)) {
$item7 =  ', ' . $item_sku[7] . ',' . $item_name[7] . ',' . $item_qty[7] . ',' . $item_price[7];
}
if (isset($i)) {
$item8 =  ', ' . $item_sku[8] . ',' . $item_name[8] . ',' . $item_qty[8] . ',' . $item_price[8];
}
if (isset($j)) {
$item9 =  ', ' . $item_sku[9] . ',' . $item_name[9] . ',' . $item_qty[9] . ',' . $item_price[9];
}
$transaction_key = get_post_meta( $order_id, '_transaction_id', true );
$transaction_key = empty($transaction_key) ? $_GET['key']: $transaction_key;

// set the Company Sending Fields//

$ParcelsLength = "26";
$ParcelsHeight = "5";
$ParcelsWidth = "23";
$ParcelsMass = "1";
$StoreCode = "LBB";
$SendAccNo = "CPT3685";
$SendSite = "CPT3685ONL";
$SendCompany = "LITTLE BRAND BOX";
$SendAdd1 = "101 Bree Castle House";
$SendAdd2 = "68 Bree Street";
$SendAdd3 = "WC";
$SendAdd4 = "Cape Town";
$SendAdd5 = "8000";
$SendContactPerson = "Zak";
$SendWorkTel = "0214236868";
$ParcelDescription = "HAIR HEALTH PRODUCTS";

curl_setopt_array($curl, array(
CURLOPT_URL => "https://control.wooapi.co.za/api/dawnwingapi/add/",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => false,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "POST",

CURLOPT_POSTFIELDS => array(
'id' => $id,
'PostID' => $id,
'WaybillNo' => $order_number,
'SendAccNo' => $SendAccNo,
'SendSite' => $SendSite,
'SendCompany' => $SendCompany,
'SendAdd1' => $SendAdd1,
'SendAdd2' => $SendAdd2,
'SendAdd3' => $SendAdd3,
'SendAdd4' => $SendAdd4,
'SendAdd5' => $SendAdd5,
'SendContactPerson' => $SendContactPerson,
'SendHomeTel' => null,
'SendWorkTel' => $SendWorkTel,
'SendCell' => null,
'RecCompany' => '',
'RecAdd1' => $order->get_shipping_address_1(),
'RecAdd2' => $order->get_shipping_address_2(),
'RecAdd3' => $order->get_shipping_city(),
'RecAdd4' => $order->get_shipping_state(),
'RecAdd5' => $order->get_shipping_postcode(),
'RecAdd6' => '',
'RecAdd7' => $company,
'RecContactPerson' => $order->get_shipping_first_name().' '.$order->get_shipping_last_name(),
'RecHomeTel' => '',
'RecWorkTel' => $phone,
'RecCell' => $phone,
'SpecialInstructions' => $order_note, 
'ServiceType' => $_shipping_type, 
'TotQTY' => '1',
'TotMass' => '1',
'Insurance' => 'false',
'InsuranceValue' => '0',
'CustomerRef' => $order_number, 
'StoreCode' => $StoreCode,
'SecurityStamp' => null,
'RequiredDocs' => null,
'WaybillInstructions' => null,
'InstructionCode' => '',
'IsSecureDelivery' => 'false',
'VerificationNumbers' => null,
'GenerateSecurePin' => 'false',
'ColectionNo' => null,
'invoiceRef' => null,
'ParcelsWaybillNo' => $order_number, 
'ParcelsLength' => $ParcelsLength, 
'ParcelsHeight' => $ParcelsHeight,
'ParcelsWidth' => $ParcelsWidth, 
'ParcelsMass' => $ParcelsMass, 
'ParcelDescription' => $ParcelDescription,
'ParcelNo' => $order_number . '_1',
'ParcelCount' => '1',
'CompleteWaybillAfterSave' => 'true',
'OrderStatus' => $order_status,
'OrderDiscountTotal' => $order_discount_total,
'OrderDiscountTax' => $order_discount_tax,
'OrderShippingTotal' => $order_shipping_total,
'OrderShippingTax' => $order_shipping_tax,
'OrderTotal' => $order_total,
'OrderTotalTax' => $order_total_tax,
'OrderPaymentMethod' => $order_payment_method,
'OrderPaymentMethodTitle' => $order_payment_method_title,
'OrderCustomerID' => $order_customer_id,
'WayBill' => null,
'OrderItems' => $item0 . ' ' . $item1 . ' ' . $item2 . ' ' . $item3 . ' ' . $item4 . ' ' . $item5 . ' ' . $item6 . ' ' . $item7 . ' ' . $item8 . ' ' . $item9,
'Company' => $siteURL,
'OrderModified' => $order_date_modified,
'OrderCreated' => $order_date_created),
CURLOPT_HTTPHEADER => array(
'X-API-KEY: 531DBCBF062810F1EA11FC5F355DDB8E'),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;
}; 
do_action( 'action_woocommerce_new_order' ); 


/***********************************Update Order*********************************************/



add_action('woocommerce_update_order', 'action_woocommerce_update_order');
function action_woocommerce_update_order($order_id){
$curl = curl_init();
$order = new WC_Order( $order_id ); 
$phone = $order->billing_phone;
$company = $order->shipping_company;
$id = $order->post->ID;// Get the order ID
$order_status = $order->get_status();
$order_number = $order->order_number;
$order_note = $order->get_customer_note();
if(!empty( $order->get_items( 'shipping' ))){
$shipping_method_instance_id ='';
foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
$shipping_method_instance_id .= $shipping_item_obj->get_instance_id(); 
}
}
$instance_id = $shipping_method_instance_id;


// set shipping fields //

if( $instance_id == 1 )
{
$_shipping_type = "ONX";
}
else{
$_shipping_type = "ECON";
}

$siteURL='http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['HTTP_HOST'].'/';
$order_data = $order->get_data(); 

$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
$order_date_modified = $order_data['date_modified']->date('Y-m-d H:i:s');

$shipping_cost = $order->get_total_shipping();

// set the address fields//
$user_id = $order->user_id;
$address_fields = array('country',
'title',
'first_name',
'last_name',
'company',
'address_1',
'address_2',
'address_3',
'address_4',
'city',
'state',
'postcode');

// set the user id//
$user_id = $order->user_id;

$address = array();
if(is_array($address_fields)){
foreach($address_fields as $field){
$address['billing_'.$field] = get_user_meta( $user_id, 'billing_'.$field, true );
$address['shipping_'.$field] = get_user_meta( $user_id, 'shipping_'.$field, true );
}
}
$dawnwingwaybill = get_post_meta( $order_id, 'my_field_order_url', true );

// get product details//
$items = $order->get_items();

$item_name = array();
$item_qty = array();
$item_price = array();
$item_sku = array();
$total_tax = array();

foreach( $items as $key => $item){
$item_name[] = $item['name'];
$item_qty[] = $item['qty'];
$item_price[] = $item['line_subtotal'];
$item_tax[] = $item['line_subtotal_tax'];
$item_id = $item['product_id'];
$product = new WC_Product($item_id);
$item_sku[] = $product->get_sku();
$total_tax[] = $item->get_total_tax();

}
$order_discount_total = $order_data['discount_total'];
$order_discount_tax = $order_data['discount_tax'];
$order_shipping_total = $order_data['shipping_total'];
$order_shipping_tax = $order_data['shipping_tax'];
$order_total = $order_data['total'];
$order_total_tax = $order_data['total_tax'];
$order_payment_method = $order_data['payment_method'];
$order_payment_method_title = $order_data['payment_method_title'];
$order_customer_id = $order_data['customer_id'];


$a = 0;
$b = $item_name[1];
$c = $item_name[2];
$d = $item_name[3];
$e = $item_name[4];
$f = $item_name[5];
$g = $item_name[6];
$h = $item_name[7];
$i = $item_name[8];
$j = $item_name[9];

$item0 = $item_sku[0] . ',' . $item_name[0] . ',' . $item_qty[0] . ',' . $item_price[0];
if (isset($b)) {
$item1 = ', ' . $item_sku[1] . ',' . $item_name[1] . ',' . $item_qty[1] . ',' . $item_price[1];
}
if (isset($c)) {
$item2 =  ', ' . $item_sku[2] . ',' . $item_name[2] . ',' . $item_qty[2] . ',' . $item_price[2];
}
if (isset($d)) {
$item3 =  ', ' . $item_sku[3] . ',' . $item_name[3] . ',' . $item_qty[3] . ',' . $item_price[3];
}
if (isset($e)) {
$item4 =  ', ' . $item_sku[4] . ',' . $item_name[4] . ',' . $item_qty[4] . ',' . $item_price[4];
}
if (isset($f)) {
$item5 =  ', ' . $item_sku[5] . ',' . $item_name[5] . ',' . $item_qty[5] . ',' . $item_price[5];
}
if (isset($g)) {
$item6 =  ', ' . $item_sku[6] . ',' . $item_name[6] . ',' . $item_qty[6] . ',' . $item_price[6];
}
if (isset($h)) {
$item7 =  ', ' . $item_sku[7] . ',' . $item_name[7] . ',' . $item_qty[7] . ',' . $item_price[7];
}
if (isset($i)) {
$item8 =  ', ' . $item_sku[8] . ',' . $item_name[8] . ',' . $item_qty[8] . ',' . $item_price[8];
}
if (isset($j)) {
$item9 =  ', ' . $item_sku[9] . ',' . $item_name[9] . ',' . $item_qty[9] . ',' . $item_price[9];
}
$transaction_key = get_post_meta( $order_id, '_transaction_id', true );
$transaction_key = empty($transaction_key) ? $_GET['key']: $transaction_key;

// set the Company Sending Fields//

$ParcelsLength = "26";
$ParcelsHeight = "5";
$ParcelsWidth = "23";
$ParcelsMass = "1";
$StoreCode = "LBB";
$SendAccNo = "CPT3685";
$SendSite = "CPT3685ONL";
$SendCompany = "LITTLE BRAND BOX";
$SendAdd1 = "101 Bree Castle House";
$SendAdd2 = "68 Bree Street";
$SendAdd3 = "WC";
$SendAdd4 = "Cape Town";
$SendAdd5 = "8000";
$SendContactPerson = "Zak";
$SendWorkTel = "0214236868";
$ParcelDescription = "HAIR HEALTH PRODUCTS";

curl_setopt_array($curl, array(
CURLOPT_URL => "https://control.wooapi.co.za/api/dawnwingapi/update/",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => false,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "POST",

CURLOPT_POSTFIELDS => array(
'id' => $id,
'PostID' => $id,
'WaybillNo' => $order_number,
'SendAccNo' => $SendAccNo,
'SendSite' => $SendSite,
'SendCompany' => $SendCompany,
'SendAdd1' => $SendAdd1,
'SendAdd2' => $SendAdd2,
'SendAdd3' => $SendAdd3,
'SendAdd4' => $SendAdd4,
'SendAdd5' => $SendAdd5,
'SendContactPerson' => $SendContactPerson,
'SendHomeTel' => null,
'SendWorkTel' => $SendWorkTel,
'SendCell' => null,
'RecCompany' => '',
'RecAdd1' => $order->get_shipping_address_1(),
'RecAdd2' => $order->get_shipping_address_2(),
'RecAdd3' => $order->get_shipping_city(),
'RecAdd4' => $order->get_shipping_state(),
'RecAdd5' => $order->get_shipping_postcode(),
'RecAdd6' => '',
'RecAdd7' => $company,
'RecContactPerson' => $order->get_shipping_first_name().' '.$order->get_shipping_last_name(),
'RecHomeTel' => '',
'RecWorkTel' => $phone,
'RecCell' => $phone,
'SpecialInstructions' => $order_note, 
'ServiceType' => $_shipping_type, 
'TotQTY' => '1',
'TotMass' => '1',
'Insurance' => 'false',
'InsuranceValue' => '0',
'CustomerRef' => $order_number, 
'StoreCode' => $StoreCode,
'SecurityStamp' => null,
'RequiredDocs' => null,
'WaybillInstructions' => null,
'InstructionCode' => '',
'IsSecureDelivery' => 'false',
'VerificationNumbers' => null,
'GenerateSecurePin' => 'false',
'ColectionNo' => null,
'invoiceRef' => null,
'ParcelsWaybillNo' => $order_number, 
'ParcelsLength' => $ParcelsLength, 
'ParcelsHeight' => $ParcelsHeight,
'ParcelsWidth' => $ParcelsWidth, 
'ParcelsMass' => $ParcelsMass, 
"ParcelDescription"=> $ParcelDescription,
'ParcelNo' => $order_number . '_1',
'ParcelCount' => '1',
'CompleteWaybillAfterSave' => 'true',
'OrderStatus' => $order_status,
'OrderDiscountTotal' => $order_discount_total,
'OrderDiscountTax' => $order_discount_tax,
'OrderShippingTotal' => $order_shipping_total,
'OrderShippingTax' => $order_shipping_tax,
'OrderTotal' => $order_total,
'OrderTotalTax' => $order_total_tax,
'OrderPaymentMethod' => $order_payment_method,
'OrderPaymentMethodTitle' => $order_payment_method_title,
'OrderCustomerID' => $order_customer_id,
'WayBill' => $dawnwingwaybill,
'OrderItems' => $item0 . ' ' . $item1 . ' ' . $item2 . ' ' . $item3 . ' ' . $item4 . ' ' . $item5 . ' ' . $item6 . ' ' . $item7 . ' ' . $item8 . ' ' . $item9,
'Company' => $siteURL,
//'OrderModified' => $order_date_modified,
'OrderCreated' => $order_date_created),
CURLOPT_HTTPHEADER => array(
'X-API-KEY: 531DBCBF062810F1EA11FC5F355DDB8E'),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;

}; 



/* Send Order to Dawnwing and generate waybill when status is marked as shipped */


add_action('woocommerce_order_status_shipped', 'wdm_send_order_to_ext');
function wdm_send_order_to_ext( $order_id ){
    // get order object and order details
    $order = new WC_Order( $order_id ); 
    $email = $order->billing_email;
    $phone = $order->billing_phone;
	$company = $order->shipping_company;
    $shipping_type = (array)$order->get_shipping_method();
	$order_number = $order->order_number;
	if(!empty( $order->get_items( 'shipping' ))){
		$shipping_method_instance_id ='';
		foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
			$shipping_method_instance_id .= $shipping_item_obj->get_instance_id(); 
		}
	}
	$instance_id = $shipping_method_instance_id;
	if( $instance_id == 1 )
	{
	$_shipping_type = "ONX";
	}
	else{
	$_shipping_type = "ECON";
	}
    
    $shipping_cost = $order->get_total_shipping();

    // set the address fields//
    $user_id = $order->user_id;
    $address_fields = array('country',
        'title',
        'first_name',
        'last_name',
        'company',
        'address_1',
        'address_2',
        'address_3',
        'address_4',
        'city',
        'state',
        'postcode');

    $address = array();
    if(is_array($address_fields)){
        foreach($address_fields as $field){
            $address['billing_'.$field] = get_user_meta( $user_id, 'billing_'.$field, true );
            $address['shipping_'.$field] = get_user_meta( $user_id, 'shipping_'.$field, true );
        }
    }
    
  
    // get product details//
    $items = $order->get_items();
    
    $item_name = array();
    $item_qty = array();
    $item_price = array();
    $item_sku = array();
        
    foreach( $items as $key => $item){
        $item_name[] = $item['name'];
        $item_qty[] = $item['qty'];
        $item_price[] = $item['line_total'];
        
        $item_id = $item['product_id'];
        $product = new WC_Product($item_id);
        $item_sku[] = $product->get_sku();
    }
    
$customer_note = $order->get_customer_note();
    $transaction_key = get_post_meta( $order_number, '_transaction_id', true );
    $transaction_key = empty($transaction_key) ? $_GET['key']  : $transaction_key;
    

    
 
        // setup the data which has to be sent//
        	
					$datawaybill =	[
						"waybillNo"=> $order_number,
						"sendAccNo"=> "CPT3685",
						"sendSite"=> "CPT3685ONL",
						"sendCompany"=> "LITTLE BRAND BOX",
						"sendAdd1"=> "101 Bree Castle House",
						"sendAdd2"=> "68 Bree Street",
						"sendAdd3"=> "",
						"sendAdd4"=> "Cape Town",
						"sendAdd5"=> "8000",
						"sendContactPerson"=> "Zak",
						"sendHomeTel"=> null,
						"sendWorkTel"=> "0214236868",
						"sendCell"=> null,
						"recCompany"=> "",
						"recAdd1"=> $order->get_shipping_address_1(),
						"recAdd2"=> $order->get_shipping_address_2(),
						"recAdd3"=> $order->get_shipping_city(),
						"recAdd4"=> $order->get_shipping_state(),
						"recAdd5"=> $order->get_shipping_postcode(),
						"recAdd7"=> $company,
						"recContactPerson"=> $order->get_shipping_first_name().' '.$order->get_shipping_last_name(),
						"recHomeTel"=> "",
						"recWorkTel"=> $phone,
						"recCell"=> $phone,
						"specialInstructions"=> $order->get_customer_note(),
						"serviceType"=> $_shipping_type,
						"totQTY"=> 1,
						"totMass"=> 1,
						"insurance"=> false,
						"insuranceValue"=> 0,
						"customerRef"=> $order_number,
						"storeCode"=> "LBB",
						"securityStamp"=> null,
						"requiredDocs"=> [],
						"waybillInstructions"=> [],
						"instructionCode"=> "",
						"isSecureDelivery"=> false,
						"verificationNumbers"=> null,
						"generateSecurePin"=> false,
						"collectionNo"=> null,
						"invoiceRef"=> null,
						"parcels"=> [
						[
						"waybillNo"=> $order_number,
						"length"=> 26,
						"height"=> 5,
						"width"=> 23,
						"mass"=> 1,
						"parcelDescription"=> "HAIR HEALTH PRODUCTS",
						"parcelNo"=> $order_number . "_1",
						"parcelCount"=> 1
						]
						],
						"completeWaybillAfterSave"=> true
						];

	      // send API request via cURL
		   $ch = curl_init();

		   // set the complete URL, to process the order on the external system. Let’s consider http ->//example.com/buyitem.php is the URL, which invokes the API //
		   curl_setopt($ch, CURLOPT_URL, 'http://swatws.dawnwing.co.za/dwwebservices/V2/live/api/waybill');
		   curl_setopt($ch, CURLOPT_POST, 1);
		   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datawaybill));
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		   curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 
	   	   'Content-Type: application/json',
           'Authorization: Bearer eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJJZCI6IjQwIiwiZXhwIjoxNjE1ODcxOTM2LCJpc3MiOiJodHRwOi8vNDEuMC42OS4xOTcvIiwiYXVkIjoiaHR0cDovLzQxLjAuNjkuMTk3LyJ9.83zUvdf_c7BGoPT2B9RKUV-5n-fDZijBCLiizBU2MHI_VvAEC8ZXlckz48lC0-C6OGNTEswZRxJzjPidQw06IA' ) );
           $response = curl_exec( $ch );
	   
		   curl_close ($ch);
		
		   // the handle response    
		   if (strpos($response,'ERROR') !== false) {
			   print_r('eror');
	   } else {   
		   $datacompletewaybill = array(
		   'waybillNo' => $order_number,
			   'storeCode' => "LBB",
			   'securityStamp' => '',
			   'generateLabel' => true,
		   );
   
			  // send API request via cURL
	   $ch = curl_init();
   
	   // set the complete URL, to process the order on the external system. Let’s consider http ->//example.com/buyitem.php is the URL, which invokes the API //
	   curl_setopt($ch, CURLOPT_URL, 'http://swatws.dawnwing.co.za/dwwebservices/V2/live/api/waybill/completewaybill');
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datacompletewaybill));
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	   curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 
	   'Content-Type: application/json',
       'Authorization: Bearer eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJJZCI6IjQwIiwiZXhwIjoxNjE1ODcxOTM2LCJpc3MiOiJodHRwOi8vNDEuMC42OS4xOTcvIiwiYXVkIjoiaHR0cDovLzQxLjAuNjkuMTk3LyJ9.83zUvdf_c7BGoPT2B9RKUV-5n-fDZijBCLiizBU2MHI_VvAEC8ZXlckz48lC0-C6OGNTEswZRxJzjPidQw06IA' ) );
       $response = curl_exec( $ch );
	   
	   curl_close ($ch);
			   $response_array = json_decode($response);
			   if(!empty($response_array)):
			   update_post_meta($order_id, 'my_field_order_url', $response_array->data[0] );
			   
			   endif;
   }
   
   }
   
   
   /**
	* Display field value on the order edit page
	*/
   add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
   function my_custom_checkout_field_display_admin_order_meta( $order ){
	   $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
	   if($order->get_status() == 'shipped' || $order->get_status() == 'completed' ):
	   echo '<p class="my_field_order_url" style="display:none;">' . get_post_meta( $order_id, 'my_field_order_url', true ) . '</p>';
	   endif; ?>
<script>
jQuery(document).ready(function() {
    var htmls = jQuery('p.my_field_order_url').html();
    var lengths = jQuery('p.my_field_order_url').length;
    if (htmls !== '' && lengths > 0) {
        jQuery('<div id="yith-order-traking-url" class="postbox"><div class="inside"><a class="wayb" href="' +
            htmls + '" target="_blank">Download Waybill</a></div></div>').insertAfter(
            '#yith-order-tracking-information');
    }
    jQuery('div#postcustomstuff table tbody tr td').each(function() {
        if (jQuery(this).find('input').val() === 'my_field_order_url') {
            jQuery(this).remove();
        }
        if (jQuery(this).find('textarea').val() ===
            '<?=get_post_meta( $order_id, 'my_field_order_url', true )?>') {
            jQuery(this).remove();
        }
    });
});
</script>

<?php
	   
	   
   }

/* Custom CSS to change the field labels of Company, Address Line 1 and Address Line 2 */

add_action('admin_head', 'my_custom_css');

function my_custom_css() {
  echo '<style>
  p.form-field._shipping_address_1_field label,p.form-field._shipping_address_2_field label,p.form-field._shipping_company_field label {
	font-size: 0;
}

p.form-field._shipping_company_field label:after {
	content: \'Building / Apartment / Complex Name\';
	font-size: 14px;
}

p.form-field._shipping_address_1_field label:after {
	content: \'Street Name\';
	font-size: 14px;
}

p.form-field._shipping_address_2_field label:after {
	content: \'Suburb\';
	font-size: 14px;
}
  </style>';
}

