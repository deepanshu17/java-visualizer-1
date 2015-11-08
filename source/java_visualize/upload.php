<?php
	$target_dir = "upload/";
	$servercode = $_POST["usercode"];
?>

var code = YOUR_CODE;
$.post(SERVER,{code_in_the_server:code},function(data){
if(data) console.log("success!");
else console.log("fuck up");
}