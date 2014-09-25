<?php
require_once('../vendor/autoload.php');

use stefantalen\OmniKassa\OmniKassaResponse;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $response = new OmniKassaResponse($_POST);
    $response
        ->setSecretKey('002020000000001_KEY1')
        ->validate();
    var_dump($response);
} else {
    echo 'No transaction took place.';
}
?>