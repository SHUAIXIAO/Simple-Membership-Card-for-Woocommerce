<?php
include"function.php";


$code = (string)$_POST["code"];
$amount = (string)$_POST["amount"];

echo(pay($code,$amount));

?>
<html>
	<head>
		<br><br><a href="../u/payment.html"> Back </a>
	</head>
<html>