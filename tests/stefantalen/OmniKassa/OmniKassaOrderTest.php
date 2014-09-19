<?php

namespace stefantalen\OmniKassa\Tests;

use stefantalen\OmniKassa\OmniKassaOrder;

class OmniKassaOrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidMerchantIds
     * @expectedException \LengthException
     */
    public function testMerchantId($merchantId)
    {
        $order = new OmniKassaOrder();
        $order->setMerchantId($merchantId);
    }
    
    public function invalidMerchantIds()
    {
        return array(
            array('00202000000000'),
            array('0020200000000010')
        );
    }
}