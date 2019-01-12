<?php
$xml = file_get_contents("php://input");
$data = (array)simplexml_load_string($xml);
if($data['return_code'] == "SUCCESS") {
    $basic_url = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    $url = $basic_url . 'api/pay_results';
}