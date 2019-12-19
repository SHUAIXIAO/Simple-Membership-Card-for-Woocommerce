<?php


include"function.php";


$code = (string)$_POST["code"];
$amount = (string)$_POST["amount"];
topup($code, $amount);


?>

<html>
	<head>
		<br><br><a href="../u/topup.html"> Back </a>
	</head>
<html>