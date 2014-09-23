<?php
require_once('../vendor/autoload.php');

use stefantalen\OmniKassa\OmniKassaOrder;

function getPath()
{
    $directories = explode('/', $_SERVER['REQUEST_URI']);
    array_pop($directories);
    return 'http://'. $_SERVER['HTTP_HOST'] . implode('/', $directories);
}


$order = new OmniKassaOrder();
$order
    ->setCurrency('EUR')
    ->setAmount('0.55')
    ->setMerchantId('000000000000000')
    ->setNormalReturnUrl(getPath() .'/return.php')
    ->setAutomaticResponseUrl(getPath() .'/response.php')
    ->setTransactionReference(date('Ymdhis').'1')
    ->setOrderId(date('Ymdhis'))
    ->setKeyVersion('1')
    ->setSecretKey('000000000000000_KEY1')
    ->enableTestMode()
;

?>
<form method="post" action="<?= $order->getActionUrl() ?>">
    <input type="hidden" name="Data" value="<?= $order->getData() ?>">
    <input type="hidden" name="InterfaceVersion" value="<?= $order->getInterfaceVersion() ?>">
    <input type="hidden" name="Seal" value="<?= $order->getSeal() ?>">
    <input type="submit" value="Naar betaling" />
</form>
