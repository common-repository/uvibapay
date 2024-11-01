<?php 
use \Uviba\UviPay;
include __DIR__.'/../UviPay/init.php';
class WC_Gateway_CustomWPressPlugin extends WC_Payment_Gateway {
public $notices = array();
		public function __construct(){

add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
			$this->id = 'customwordpresspayplugin';
//$this->icon  = woo_UvibaPayments_IMG.'/uviba_payment_gateway.png';
//$this->icon  = 'https://api.uviba.com/pay/images/uviba_payment_gateway_wordpress.png';
$this->pvar=$this->id;
$this->has_fields = false;
$this->method_title = "Uviba Payments";
$this->title = "Uviba Payments"; 
$this->method_description = "Uviba Payments works by adding payment fields on the checkout and then sending the details to Uviba for verification. <a href='https://pay.uviba.com' >Sign up</a> for a Uviba account, and get your Uviba Payments account keys.";

$this->init_form_fields();
$this->init_settings();
add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

 add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

//global $woocommerce;
// var_dump($woocommerce->cart);
// echo  $amount2 = floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_total() ) );



$test_title			= '';	
			$test_description	= '';
			if ( 'test' == $this->settings['test_mode'] ) {
				$test_title 		= ' [TEST MODE]';
				$test_description 	= '<br/><br/><u>Test Mode is <strong>ACTIVE</strong>, use following Credit Card details:-</u><br/>'."\n"
									 .'Test Card Name: <strong><em>any name</em></strong><br/>'."\n"
									 .'Test Card Number: <strong>4111 1111 1111 1111</strong><br/>'."\n"
									 .'Test Card CVV: <strong>424</strong><br/>'."\n"
									 .'Test Card Expiry: <strong>04/'.date('y', strtotime('+1 year')).'</strong>';
				 
			} //END--test_mode=yes
			$this->title 			= $this->settings['title'].$test_title; // Title as displayed on Frontend
			$this->description 		= $this->settings['description'].$test_description; // Description as displayed on Frontend
if ( $this->settings['show_logo'] != "no" ) { // Check if Show-Logo has been allowed
				//$this->icon 		= get_site_url().'/wp-content/plugins/UvibaPay/public/images/uviba_payment_gateway.png';
				//$this->icon  = 'https://api.uviba.com/pay/images/uviba_payment_gateway_wordpress.png';
				//Logo make it common, no logo
				//customwordpresspayplugin_IMG . 'logo_' . $this->settings['show_logo'] . '.png';
			}
 $this->msg['message']	= '';
            $this->msg['class'] 	= '';
			
			add_action('init', array(&$this, 'check_customwordpresspayplugin_response'));
            add_action('woocommerce_api_' . strtolower(get_class($this)), array($this, 'check_customwordpresspayplugin_response')); //update for woocommerce >2.0

            if ( version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
                    add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) ); //update for woocommerce >2.0
                 } else {
                    add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) ); // WC-1.6.6
                }
            add_action('woocommerce_receipt_'.$this->pvar, array(&$this, 'receipt_page'));	 

$test_mode = $this->settings['test_mode'];

if($test_mode=='test'){
	$show_er=false;
	if(!isset($this->settings['test_public_key'],$this->settings['test_private_key'])){
		$show_er=true;
	}else if(empty(trim($this->settings['test_public_key'])) || empty(trim($this->settings['test_private_key']))){
		$show_er=true;
	}else if(substr( $this->settings['test_public_key'], 0, 7 ) != "pk_test" || substr( $this->settings['test_private_key'], 0, 7 ) != "sk_test"){
		$show_er=true;
	}

	if($show_er===true){
		$this->add_admin_notice( 'keys', 'notice notice-error', sprintf( __( 'Uviba is in test mode however your test keys may not be valid. Test keys start with pk_test for public keys and sk_test for private keys (secret keys). Please go to your settings and, <a href="%s">set your UvibaPay account keys</a>.', 'woo_customwordpresspayplugin' ), $this->get_setting_link() ), true );
	}
}else{
	$show_er=false;
	if(!isset($this->settings['live_public_key'],$this->settings['live_private_key'])){
		$show_er=true;
	}else if(empty(trim($this->settings['live_public_key'])) || empty(trim($this->settings['live_private_key']))){
		$show_er=true;
	}else if(substr( $this->settings['live_public_key'], 0, 7 ) != "pk_live" || substr( $this->settings['live_private_key'], 0, 7 ) != "sk_live"){
		$show_er=true;
	}

	if($show_er===true){
		$this->add_admin_notice( 'keys', 'notice notice-error', sprintf( __( 'Uviba is in live mode however your live keys may not be valid. Live keys start with pk_live for public keys and sk_live for private keys (secret keys). Please go to your settings and, <a href="%s">set your UvibaPay account keys</a>.', 'woo_customwordpresspayplugin' ), $this->get_setting_link() ), true );
	}
	
}



		}

 

		public function init_form_fields(){
			 $currency = get_woocommerce_currency();

			$this->form_fields = array(
    // 'test_mode' => array(
    //     'title' => __( 'Enable/Disable', 'woocommerce' ),
    //     'type' => 'checkbox',
    //     'label' => __( 'Enable Test Mode', 'woocommerce' ),
    //     'default' => 'yes'
    // ),
				// Activate the Gateway
	'enabled' => array(
		'title' 		=> __('Enable/Disable:', 'woo_customwordpresspayplugin'),
		'type' 			=> 'checkbox',
		'label' 		=> __('Enable Uviba Payments', 'woo_customwordpresspayplugin'),
		'default' 		=> 'yes',
		'description' 	=> 'Show in the Payment List as a payment option'
	),
	// Title as displayed on Frontend
	'title' => array(
		'title' 		=> __('Title:', 'woo_customwordpresspayplugin'),
		'type'			=> 'text',
		'default' 		=> __('Credit Card (secure)', 'woo_customwordpresspayplugin'),
		'description' 	=> __('This controls the title which the user sees during checkout.', 'woo_customwordpresspayplugin'),
		'desc_tip' 		=> true
	),
// Description as displayed on Frontend
	'description' => array(
		'title' 		=> __('Description:', 'woo_customwordpresspayplugin'),
		'type' 			=> 'textarea',
		'default' 		=> __("Pay securely with:\n - Credit or Debit Cards.", 'woo_customwordpresspayplugin'),
		'description' 	=> __('This controls the description which the user sees during checkout.', 'woo_customwordpresspayplugin'),
		'desc_tip' 		=> true
	),
	'test_mode' => array(
		'title' 		=> __('Mode:', 'woo_customwordpresspayplugin'),
		'type' 			=> 'select',
		'label' 		=> __('UvibaPay Test/Live Mode.', 'woo_customwordpresspayplugin'),
		'options' 		=> array('test'=>'Test Mode','secure'=>'Live Mode'),
		'default' 		=> 'test',
		'description' 	=> __('Mode of UvibaPay activities'),
		'desc_tip' 		=> true
     ),
	'button_text' => array(
        'title' => __( 'Button Text', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'Text of Uviba checkout button.', 'woocommerce' ),
        'default' => __( 'Pay Now', 'woocommerce' ),
        'desc_tip'      => true,
    ),
	'redirect_page' => array(
					'title' 			=> __('Return Page'),
					'type' 			=> 'select',
					'options' 		=> $this->customwordpresspayplugin_get_pages('Select Page'),
					'description' 	=> __('URL of success page', 'woo_customwordpresspayplugin'),
					'desc_tip' 		=> true
                ),
    'one_dollar' => array(
        'title' => __( 'How much '.$currency.' is one dollar', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'Currency Converter, $1= X '.$currency, 'woocommerce' ),
        'default' => __( '1.00', 'woocommerce' ),
        'desc_tip'      => true,
    'custom_attributes' => array( 'required' => 'required'),

    ),
     'test_public_key' => array(
        'title' => __( 'Test Public key', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'You can get this key from https://pay.uviba.com.', 'woocommerce' ),
            'custom_attributes' => array( 'required' => 'required' ),

        'default' => __( '', 'woocommerce' ),
        'desc_tip'      => true,
    ),
    'test_private_key' => array(
        'title' => __( 'Test Private Key', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'You can get this key from https://pay.uviba.com.', 'woocommerce' ),
            'custom_attributes' => array( 'required' => 'required' ),

        'default' => __( '', 'woocommerce' ),
        'desc_tip'      => true,
    ),
    'live_public_key' => array(
        'title' => __( 'Live Public key', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'You can get this key from https://pay.uviba.com/dashboard.', 'woocommerce' ),
        'default' => __( '', 'woocommerce' ),
         'custom_attributes' => array( 'required' => 'required' ),
        'desc_tip'      => true,
    ),
    'live_private_key' => array(
        'title' => __( 'Live Private Key', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'You can get this key from https://pay.uviba.com.', 'woocommerce' ),
          'custom_attributes' => array( 'required' => 'required' ),
        'default' => __( '', 'woocommerce' ),
        'desc_tip'      => true,
    ),
    'button_text' => array(
        'title' => __( 'Button Text', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'Text of Uviba checkout button.', 'woocommerce' ),
        'default' => __( 'Pay Now', 'woocommerce' ),
        'desc_tip'      => true,
    ),
     'image_url_of_store' => array(
        'title' => __( 'Image Url of Store', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'This will be shown when user click pay button.', 'woocommerce' ),
        'default' => __( '', 'woocommerce' ),
        'desc_tip'      => true,
    ),
      'store_description' => array(
        'title' => __( 'Description of your store', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'This will be shown when user click pay button.', 'woocommerce' ),
        'default' => __( 'Your store description', 'woocommerce' ),
        'desc_tip'      => true,
    ),
    'store_name' => array(
        'title' => __( 'Name of your store', 'woocommerce' ),
        'type' => 'text',
        'description' => __( 'This will be shown when user click pay button.', 'woocommerce' ),
        'default' => __('Your Store Name', 'woocommerce' ),
        'desc_tip'      => true,
    ),
    'payments_card' => array(
		'title' 		=> __('Enable/Disable:', 'woo_customwordpresspayplugin'),
		'type' 			=> 'checkbox',
		'label' 		=> __('Card payments', 'woo_customwordpresspayplugin'),
		'default' 		=> 'yes',
	),
	'payments_ach' => array(
		'title' 		=> __('Enable/Disable:', 'woo_customwordpresspayplugin'),
		'type' 			=> 'checkbox',
		'label' 		=> __('Bank payments', 'woo_customwordpresspayplugin'),
		'default' 		=> 'yes',
	),
	'payments_paypal' => array(
		'title' 		=> __('Enable/Disable:', 'woo_customwordpresspayplugin'),
		'type' 			=> 'checkbox',
		'label' 		=> __('Paypal payments', 'woo_customwordpresspayplugin'),
		'default' 		=> 'yes',
	),
    // 'description' => array(
    //     'title' => __( 'Customer Message', 'woocommerce' ),
    //     'type' => 'textarea',
    //     'default' => ''
    // )
); 

if(!isset($this->settings['one_dollar'])){
	//$this->settings['one_dollar'] 1 dollar = X onlarin
	$this->settings['one_dollar']=1;
}
if($this->settings['one_dollar']==0){
	$this->settings['one_dollar']=1;
}


		}




		 

public function add_payment_method() {
        return array(
            'result'   => 'failure',
            'redirect' => wc_get_endpoint_url( 'payment-methods' ),
        );
    }


public function admin_options(){
			echo '<h3>'.__('Uviba Payment', 'woo_customwordpresspayplugin').'</h3>';
			echo '<p>'.__('Uviba Payments works by adding payment fields on the checkout and then sending the details to Uviba for verification. <a href=\'https://pay.uviba.com\' >Sign up</a> for a Uviba account, and get your Uviba Payments account keys.', 'woo_customwordpresspayplugin').'</p>';
			echo '<p><small><strong>'.__('Confirm your Mode: Is it LIVE or TEST.').'</strong></small></p>';
			echo '<table class="form-table">';
			// Generate the HTML For the settings form.
			$this->generate_settings_html();
			echo '</table>';
		} //END-admin_options

        /**
         *  There are no payment fields, but we want to show the description if set.
         **/
		function payment_fields(){
			if( $this->description ) {
				echo wpautop( wptexturize( $this->description ) );
			}
		} //END-payment_fields
		
        /**
         * Receipt Page
         **/
        public static $receipt_page_called=false;
		function receipt_page($order){
			if(self::$receipt_page_called===true){
				return;
			}else{
				self::$receipt_page_called=true;
			}
			echo '<p><strong>' . __('Thank you for your order.', 'woo_customwordpresspayplugin').'</strong><br/>' . __('The payment page will open soon.', 'woo_customwordpresspayplugin').'</p>';
			echo $this->generate_customwordpresspayplugin_form($order);
		} //END-receipt_page
    
        /**
         * Generate button link
         **/
		function generate_customwordpresspayplugin_form($order_id){
			global $woocommerce;
			$order = new WC_Order( $order_id );

			// Redirect URL
			if ( '' == $this->redirect_page  || 0 == $this->redirect_page ) {
				$redirect_url = get_site_url() . "/";
			} else {
				$redirect_url = get_permalink( $this->redirect_page );
			}
			// Redirect URL : For WooCoomerce 2.0
			if ( version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
				$redirect_url = add_query_arg( 'wc-api', get_class( $this ), $redirect_url );
			}

            $productinfo = "Order $order_id";

			$txnid = $order_id.'_'.date("ymds");
			// hash-string = key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||<SALT>
			$str = "$this->txnid|$order->order_total|$productinfo|$order->billing_first_name|$order->billing_email|$order_id||||||||||";
			$hash = strtolower(hash('sha512', $str));
			$service_provider = '';
//die($redirect_url);
			$customwordpresspayplugin_args = array(
				 
				'hash' 			=> $hash,
				'txnid' 		=> $txnid,
				'amount' 		=> $order->order_total,
				'firstname'		=> $order->billing_first_name,
				'email' 		=> $order->billing_email, 
				'phone' 		=> substr( $order->billing_phone, -10 ),
				'productinfo'	=> $productinfo,
				'surl' 			=> $redirect_url,
				'furl' 			=> $redirect_url,
				'lastname' 		=> $order->billing_last_name,
				'address1' 		=> $order->billing_address_1,
				'address2' 		=> $order->billing_address_2,
				'city' 			=> $order->billing_city,
				'state' 		=> $order->billing_state,
				'country' 		=> $order->billing_country,
				'zipcode' 		=> $order->billing_postcode,
				'curl'			=> $redirect_url,
				'pg' 			=> 'NB',
				'udf1' 			=> $order_id,
				'service_provider'	=> $service_provider
			);
			$customwordpresspayplugin_args_array = array();
			foreach($customwordpresspayplugin_args as $key => $value){
				$customwordpresspayplugin_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
			}

if(!isset($this->settings['one_dollar'])){
	//$this->settings['one_dollar'] 1 dollar = X onlarin
	$one_dollar=1;
}else{
	$one_dollar = $this->settings['one_dollar'];
}
if($this->settings['one_dollar']==0){
	$one_dollar=1;
}

if(!ctype_digit($this->settings['one_dollar'])){
	$one_dollar=1;
}


	$currency = get_woocommerce_currency();
// $total_amount = max( 0, apply_filters( 'woocommerce_calculated_total', round( WC()->cart->cart_contents_total + WC()->cart->fee_total + WC()->cart->tax_total, WC()->cart->dp ), WC()->cart ) );


if(!isset($this->settings['image_url_of_store'])){
	$this->settings['image_url_of_store']='';
}
if(!isset($this->settings['button_text'])){
	$this->settings['button_text']='Pay by Card';
}
$total_amount_in_dollar = round($customwordpresspayplugin_args['amount']*100/$this->settings['one_dollar']);
$test_mode = $this->settings['test_mode'];

if($test_mode=='test'){
	//$public_key = $this->settings['payments_card'];
	$public_key = $this->settings['test_public_key'];
}else{
	$public_key = $this->settings['live_public_key'];
}

if($test_mode=='test'){
	$private_key = $this->settings['test_private_key'];
}else{
	$private_key = $this->settings['live_private_key'];
}

$not_allowed_methods='';
if(!isset($this->settings['payments_card'])){
	$this->settings['payments_card']='yes';
}
if(!isset($this->settings['payments_ach'])){
	$this->settings['payments_ach']='yes';
}
if(!isset($this->settings['payments_paypal'])){
	$this->settings['payments_paypal']='yes';
}

if($this->settings['payments_card']=='no'){
	$not_allowed_methods.='card,';
}
if($this->settings['payments_ach']=='no'){
	$not_allowed_methods.='ach,';
}
if($this->settings['payments_paypal']=='no'){
	$not_allowed_methods.='paypal,';
}
$fcontents = file_get_contents('https://pay.uviba.com/get_connected_puadvertiser/'.$private_key);
$fcontents = utf8_encode($fcontents);
$get_connected_puadvertiser = json_decode($fcontents,true); 
//var_dump($get_connected_advertiser);
$request_full_url = 'https://api.uviba.com';
//if someone advertising then let them handle everything.
if($get_connected_puadvertiser['url']!='no_ad'){
	$request_full_url=$get_connected_puadvertiser['url'].'/req_p_main_subdomain_api_1';
}
$class_name='uvi';
if($get_connected_puadvertiser['class_name']!='no_ad'){
	$class_name=$get_connected_puadvertiser['class_name'];
}
$class_name_BB=ucfirst($class_name.'Pay');;//big first letter and big Pay
$class_name_SB=($class_name.'Pay');//small first letter and big Pay
//current_uvi_form_id
	return '<form  action="'.$redirect_url.'" method="post"  >'
.implode('', $customwordpresspayplugin_args_array).
 '<script 
    src="'.$request_full_url.'/js/checkout.js" 
    class="'.$class_name_SB.'-button" 
    data-button-text="'.$this->settings['button_text'].'"
    data-public_key="'.$public_key.'"
    data-amount=" '.($total_amount_in_dollar).'"
    data-amount_string="'.$currency.''.sprintf("%.2f",round($customwordpresspayplugin_args['amount'],2)).'"
    data-description="'.$this->settings['store_description'].'"
    data-name="'.$this->settings['store_name'].'" 
	data-image="'.$this->settings['image_url_of_store'].'"
	data-not_allowed_methods="'.$not_allowed_methods.'"
	data-version="1"
	data-wordpress="1"
    data-submit-ajax="0">
 </script>
</form><a style="
    position: relative;
    top: 2px;
    left: 8px;
" class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel order &amp; restore cart', 'woo_customwordpresspayplugin').'</a>
<script type="text/javascript">
'.$class_name_BB.'.onButtonReady(function(form_id){
		
try{
	'.$class_name_SB.'_openPaymentModal(1);
}catch(e){}

	});</script>
';				
		 
		} //END-generate_customwordpresspayplugin_form

        /**
         * Process the payment and return the result
         **/
        function process_payment($order_id){
			global $woocommerce;
            $order = new WC_Order($order_id);
			//die('process_payment');
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1.0', '>=' ) ) { // For WC 2.1.0
			  	$checkout_payment_url = $order->get_checkout_payment_url( true );
			} else {
				$checkout_payment_url = get_permalink( get_option ( 'woocommerce_pay_page_id' ) );
			}

			return array(
				'result' => 'success', 
				'redirect' => add_query_arg(
					'order', 
					$order->id, 
					add_query_arg(
						'key', 
						$order->order_key, 
						$checkout_payment_url						
					)
				)
			);
		} //END-process_payment



		public function charge_user($amount){
			$response_data = array();
			$response_data['charge']=array();
			$response_data['error_body']=array();
			 //path/to/UviPay library
			$test_mode = $this->settings['test_mode'];

			if($test_mode=='test'){
				$private_key = $this->settings['test_private_key'];
			}else{
				$private_key = $this->settings['live_private_key'];
			}
			UviPay::setPrivateKey($private_key);


			$process = UviPay::charge(
			array(
			'amount'=>$amount,
			'currency'=>get_woocommerce_currency(),
			//If request was post
			'wp_wocommerce'=>1,
			)
			);
			if($process['status']){
			//success
			$response_data['charge']=$process;
			}else{
			//error
			$response_data['error_body']=$process['error']['message'];
			}
return $response_data;

		}
        /**
         * Check for valid gateway server callback
         **/
        function check_customwordpresspayplugin_response(){
            global $woocommerce;
            // $order = new WC_Order( 27 );
            // var_dump($order->order_total);
            // die(max( 0, apply_filters( 'woocommerce_calculated_total', round( WC()->cart->cart_contents_total + WC()->cart->fee_total + WC()->cart->tax_total, WC()->cart->dp ), WC()->cart ) ));
//die($order->get_checkout_payment_url( true ));
          //  var_dump($_REQUEST);exit;
			if( isset($_REQUEST['udf1']) ){
$trans_authorised=true;
				$order_id = $_REQUEST['udf1'];
				if($order_id != ''){
					try{
if(!isset($this->settings['one_dollar'])){
	//$this->settings['one_dollar'] 1 dollar = X onlarin
	$one_dollar=1;
}else{
	$one_dollar = $this->settings['one_dollar'];
}
if($this->settings['one_dollar']==0){
	$one_dollar=1;
}
						$order = new WC_Order( $order_id );
						$charge_data = $this->charge_user(round($order->order_total*100/$one_dollar));
						if(empty($charge_data['charge'])){
							//error happened
							$error_mes = '';
							$error_dev_mes = 'Transaction failed.';
							if(isset($charge_data['error_body'])){
								/*if(isset($charge_data['error_body']['message'])){
									$error_mes=$charge_data['error_body']['message'];
								}
								if($charge_data['error_body']['error']){
									//var_dump($charge_data['error_body']['error']);
									if(isset($charge_data['error_body']['error']->message)){
										$error_dev_mes=$charge_data['error_body']['error']->message;
									}
								}*/
								$error_mes=$error_dev_mes=$charge_data['error_body'];

							}
							
							$this->msg['class'] = 'error';
							$this->msg['message'] = "Thank you for the order. However, the transaction has been declined. ".$error_mes;
							$order->add_order_note('Transaction ERROR: '.$error_dev_mes.'<br/> ('.$_REQUEST['txnid'].')<br/>');
							$order->update_status('failed');
						}else{
							//charge was successful
							$this->msg['message'] = "Thank you for the order. Your account has been charged and your transaction is successful.";
							$this->msg['class'] = 'success';
							$order->payment_complete();
							//Uviba
							$order->add_order_note('Payment successfully completed.<br/> ('.$_REQUEST['txnid'].')');
							$order->reduce_order_stock();
							$woocommerce->cart->empty_cart();
							//$order->update_status('completed');
							$order->update_status('processing');
						}
						 
					}catch(Exception $e){
                        // $errorOccurred = true;
                        $msg = "Error";
					}
				}


				if ( function_exists( 'wc_add_notice' ) ) {
					wc_add_notice( $this->msg['message'], $this->msg['class'] );

				} else {
					if( 'success' == $this->msg['class'] ) {
						$woocommerce->add_message( $this->msg['message']);
					}else{
						$woocommerce->add_error( $this->msg['message'] );

					}
					$woocommerce->set_messages();
				}	
				//die($this->msg['message']);
				// @see: https://wordpress.org/support/topic/enabling-default-woocommerce-redirects/#post-9728440
				if('success' == $this->msg['class']) {
					if ( '' == $this->redirect_page || 0 == $this->redirect_page ) {
						$redirect_url = $this->get_return_url( $order );
					} else {
						$redirect_url = get_permalink( $this->redirect_page );
					}
				} else {
					$redirect_url = wc_get_checkout_url();

				}
				
				//wc_print_notices();
				
				wp_redirect( $redirect_url );
                exit;
	
			}

        }



	function customwordpresspayplugin_get_pages($title = false, $indent = true) {
			$wp_pages = get_pages('sort_column=menu_order');
			$page_list = array();
			if ($title) $page_list[] = $title;
			foreach ($wp_pages as $page) {
				$prefix = '';
				// show indented child pages?
				if ($indent) {
                	$has_parent = $page->post_parent;
                	while($has_parent) {
                    	$prefix .=  ' - ';
                    	$next_page = get_post($has_parent);
                    	$has_parent = $next_page->post_parent;
                	}
            	}
            	// add to page list array array
            	$page_list[$page->ID] = $prefix . $page->post_title;
        	}
        	return $page_list;
		}


		public function get_setting_link() {
			$use_id_as_section = function_exists( 'WC' ) ? version_compare( WC()->version, '2.6', '>=' ) : false;

			//$section_slug = $use_id_as_section ? 'customwordpresspayplugin' : strtolower( 'WC_Gateway_CustomWPressPlugin' );
			$section_slug = $use_id_as_section ? $this->id : strtolower( 'WC_Gateway_CustomWPressPlugin' );

			return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $section_slug );
		}

		public function add_admin_notice( $slug, $class, $message, $dismissible = false ) {
			$this->notices[ $slug ] = array(
				'class'       => $class,
				'message'     => $message,
				'dismissible' => $dismissible,
			);
		}

	    public static $admin_notices=false;
		public function admin_notices() {
			if(self::$admin_notices===true){
				return;
			}else{
				self::$admin_notices=true;
			}
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			foreach ( (array) $this->notices as $notice_key => $notice ) {
				echo '<div class="' . esc_attr( $notice['class'] ) . '" style="position:relative;">';

				if ( $notice['dismissible'] ) {
				?>
					<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-uviba-hide-notice', $notice_key ), 'wc_uviba_hide_notices_nonce', '_wc_uviba_notice_nonce' ) ); ?>" class="woocommerce-message-close notice-dismiss" style="position:absolute;right:1px;padding:9px;text-decoration:none;"></a>
				<?php
				}

				echo '<p>';
				echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) );
				echo '</p></div>';
			}
		}


	    }