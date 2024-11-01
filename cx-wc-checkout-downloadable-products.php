<?php
	/**
	Plugin Name: WooCommerce Checkout For Downloadable Products
	Version: 7.0.2
	Author: CyberXoft
	Plugin URI: http://www.cyberxoft.com/product/woocheckout-for-downloadable-products/
	Author URI: http://www.cyberxoft.com/
	Text Domain: cx-wc-wocf-page
	Description: With this plugin you can remove unnecessary fields from WooCommerce checkout only for Virtual and Downloadable products, leaving behind only the First Name, Last Name, Email Address and the custom form elements that you must have added. But if there is any Physical product in the cart along with Virtual or Downloadable products then this plugin will not remove any field from the checkout form. It works out of the box, and there are no settings for you to configure. <strong>I also offer Upto 66% Discount on all WordPress Themes and Plugins sold on my website, <a href="http://www.cyberxoft.com/product-category/wp-themes/">check that...</a></strong>
	*/
	
	if ( !defined('CX_WOCF_DONATE_LINK') ) {
		define('CX_WOCF_DONATE_LINK', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K373C4BNSJYY2');
	}
	
	/**
	 * Additional links on the plugin page
	 */
	function cx_wocf_plugin_row_meta($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$links[] = '<a href="' . CX_WOCF_DONATE_LINK . '" target="_blank">'.__('Please Donate...', 'cx-wc-wocf-page').'</a>';
			$links[] = '<a href="http://www.cyberxoft.com/product-category/wp-themes/" target="_blank">'.__('Get Upto 66% Discount on WordPress Themes and Plugins...', 'cx-wc-wocf-page').'</a>';
		}
		
		return $links;
	}
	
	function cx_custom_override_checkout_fields($fields){
		global $woocommerce;
		
		$hasPhysicalProduct = false;
		if (!empty($woocommerce->cart->cart_contents)){
			//looping the cart
			$cart = $woocommerce->cart->get_cart();
			
			foreach ($cart as $key => $values){
				$_product = get_product($values['variation_id'] ? $values['variation_id'] : $values['product_id']);
				
				if (!empty($_product) && $_product->exists() && $values['quantity'] > 0){
					if ($_product->virtual == 'no' && $_product->downloadable == 'no'){
						$hasPhysicalProduct = true;
					}
				}
			}
		}
		
		if ($hasPhysicalProduct == false){
			$removeFields = [
				'billing'=>[
					//'billing_first_name',
					//'billing_last_name',
					'billing_company',
					//'billing_email',
					'billing_phone',
					'billing_country',
					'billing_address_1',
					'billing_address_2',
					'billing_city',
					'billing_state',
					'billing_postcode'
				],
				
				'order'=>[
					'order_comments'
				]
			];
			
			foreach($removeFields as $fieldset=>$arr){
				foreach($arr as $field){
					unset($fields[$fieldset][$field]);
				}
			}
		}
		
		return $fields;
	}	
	
	add_filter('plugin_row_meta', 'cx_wocf_plugin_row_meta',10,2);
	add_filter('woocommerce_checkout_fields' , 'cx_custom_override_checkout_fields');