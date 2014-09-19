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
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The given currency does not comply with the ISO 4217 standard
     */
    public function testInvalidCurrency()
    {
       $order = new OmniKassaOrder();
       $order->setCurrency('NL');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The requested currency "NLG" is not available
     */
    public function testUnknownCurrency()
    {
       $order = new OmniKassaOrder();
       $order->setCurrency('NLG');
    }
    
    public function testValidCurrency()
    {
        $order = new OmniKassaOrder();
        $order->setCurrency('EUR');
        $this->assertEquals('987', $order->getCurrency());
    }
    
    public function testInterfaceVersion()
    {
        $order = new OmniKassaOrder();
        $this->assertEquals('HP_1.0', $order->getInterfaceVersion());
    }
    
}
