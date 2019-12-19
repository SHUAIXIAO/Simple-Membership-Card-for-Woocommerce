<?php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
$woocommerce = new Client(
    '', // Your store URL
    '', // Your consumer key
    '', // Your consumer secret
    [
        'wp_api' => true, // Enable the WP REST API integration
        'version' => 'wc/v2' // WooCommerce WP REST API version
        
    ]
);


/*函数库*/
/*判断卡是否存在*/
 function check($coupon_code) {
 global $woocommerce;
 $result =print_r(json_encode($woocommerce->get('coupons',[ 'code' => $coupon_code])),true);
//echo($result);
 $pattern = '/id\"\:[\d]*,\"code\"\:\"'.$coupon_code.'\"/';
 $isMatched = preg_match($pattern, $result, $matches);// First Regular Expression, extract id and code info from array. 
 $result1= (print_r(($matches[0]),true));	
  if ($isMatched == true) { return true;} else { return false;}
 	
}

/*创建充值卡*/
function create($amount, $coupon_code, $description)
  {
     global $woocommerce;
   if ( check($coupon_code) == false) { 
     $data = [
              'code' => $coupon_code,
              'amount' => $amount,
              'description' => $description,
             ];
     ($woocommerce->post('coupons', $data));
     echo("Card No. $coupon_code has been created and topped up $amount");
   } else {
   	 echo('Card Exists!');
   }
  }

/*查询ID*/
function getID($coupon_code){
	 global $woocommerce;
     $result =print_r(json_encode($woocommerce->get('coupons',[ 'code' => $coupon_code])),true);
     $pattern = '/id\"\:[\d]*,\"code\"\:\"'.$coupon_code.'\"/';
	 $isMatched = preg_match($pattern, $result, $matches);// First Regular Expression, extract id and code info from array. 
     if ($isMatched == false) { exit("No Such Card, Please check the details!");}
     else{
     $result1= (print_r(($matches[0]),true));	
     $pattern1 ='/(?<=\:)[\d]*(?=,)/';
     $isMatched1 = preg_match($pattern1, $result1, $matches1);// Second Regular Expression based on the first one, extract id from first regular expression.
     //get ID 
     $coupon_id = $matches1[0];
     return $coupon_id; 
     }
	}


/*查询余额*/
function getamount($coupon_code)
 {
     global $woocommerce;
    if ( check($coupon_code) == true) { 
     //第一次匹配
	     $result =print_r(json_encode($woocommerce->get('coupons',[ 'code' => $coupon_code])),true); //将json存储成数组 (change JSon example into array)
	     $pattern = '/code\"\:\"'.$coupon_code.'\",\"amount\"\:\"[\d]*\.\d\d\",\"d/';
	     $isMatched = preg_match($pattern, $result, $matches);// First Regular Expression, extract id and code info from array. 
	    //第二次匹配
	     $result1= (print_r(($matches[0]),true));	
	     $pattern1 = '/[\d]*\.\d\d/';
	     $isMatched1 = preg_match($pattern1, $result1, $matches1);// Second Regular Expression based on the first one, extractid from first regular expression.
	    //get amount
	     $amount = $matches1[0];
	     //返回
	     return $amount;
    } else {
    	 exit("No Such Card, Please check the details!");
    }
}

/*充值*/
function topup($coupon_code,$addamount){
      global $woocommerce;
   if ( check($coupon_code) == true) { 
      $Amount = getamount($coupon_code);
      $Total = (float)$Amount + (float)$addamount;
      $strtotal = (string)$Total;
      $ID = getID($coupon_code);
      $put = 'coupons/'.$ID;
      $data = [
               'amount' => $strtotal
              ];
      $woocommerce->put($put, $data); 
      echo("Card No. $coupon_code has been topped AUD $addamount, total amount now is AUD $strtotal");
   } else {
   	  echo("No Such Card, Please crate it first!");
   }
     
}


/*返回卡信息*/
function getInfo($coupon_code){
	global $woocommerce;
	if ( check($coupon_code) == true) { 
      $Amount = getamount($coupon_code);
      
       $ID = getID($coupon_code);
       $result =print_r(json_encode($woocommerce->get('coupons',[ 'code' => $coupon_code])),true); //将json存储成数组 (change JSon example into array)
	   $pattern = '/(?<=\"description\"\:\").*(?=\",\"date_expires\")/';
	   $isMatched = preg_match($pattern, $result, $matches);// First Regular Expression, extract id and code info from array. 
	   $description = unicodeDecode((print_r(($matches[0]),true)));	
      
      
      echo("<p> Card No.: $coupon_code  </br> Total amount: AUD $Amount <br/ >Description:  $description </p>");
   } else {
   	  echo("No Such Card, Please crate it first!");
   }
	
	
}

/*消费*/
function pay($coupon_code,$payamount){
      global $woocommerce;
      if ( check($coupon_code) == true) { 
	       $Amount = getamount($coupon_code);
	       if ((float)$payamount <= (float)$Amount ) {
	       $Total = round(((float)$Amount - (float)$payamount),2);
	       $strtotal = (string)$Total;
	       $ID = getID($coupon_code);
	       
	       $put = 'coupons/'.$ID;
	       $data = [
	               'amount' => $strtotal
	              ];
	
	       $woocommerce->put($put, $data); 
	     
	       echo("Card No. ".$coupon_code." consumed AUD".$payamount.". The new amount is: AUD".$strtotal);
	    	}
	      else {
	     	echo("Not enough money on your card,please topup first!");
	      }
	     } else {
	     	echo("No Such Card, Please crate it first!");
	     }
		}
/*汉字解码*/
function unicodeDecode($unicode_str){
    $json = '{"str":"'.$unicode_str.'"}';
    $arr = json_decode($json,true);
    if(empty($arr)) return '';
    return $arr['str'];
}


?>
