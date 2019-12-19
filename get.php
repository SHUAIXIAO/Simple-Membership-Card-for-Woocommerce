<?php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
$woocommerce = new Client(
    'http://order.mewah.com.au', // Your store URL
    'ck_b5e23b35c1e54999a3a4ca299efe9b50b25fa8dd', // Your consumer key
    'cs_853692310534bb6af867f4ad49c95e1fac1c6a71', // Your consumer secret
    [
        'wp_api' => true, // Enable the WP REST API integration
        'version' => 'wc/v2' // WooCommerce WP REST API version
        
    ]
);

$coupon_code = 'aweeddwe';

$result = (print_r(json_encode($woocommerce->get('coupons')),true)); //将json存储成数组 (change JSon example into array)

$pattern = '/id\"\:[\d]*,\"code\"\:\"'.$coupon_code.'/';
$isMatched = preg_match($pattern, $result, $matches);// First Regular Expression, extract id and code info from array. 
$result1= (print_r(($matches[0]),true));

$str = $result1;
$isMatched1 = preg_match('/(?<=\:)[\d]*(?=,)/', $str, $matches1);// Second Regular Expression based on the first one, extract id from first regular expression.

print_r($matches1[0]);//print ID

?>