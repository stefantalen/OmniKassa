<?php

namespace stefantalen\OmniKassa\Tests;

use stefantalen\OmniKassa\OmniKassaOrder;

class OmniKassaOrderTest extends \PHPUnit_Framework_TestCase
{
    protected $order;
    
    public function setUp()
    {
        $this->order = new OmniKassaOrder();
    }
    /**
     * @dataProvider invalidMerchantIds
     * @expectedException \LengthException
     */
    public function testMerchantId($merchantId)
    {
        $this->order->setMerchantId($merchantId);
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
       $this->order->setCurrency('NL');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The requested currency "NLG" is not available
     */
    public function testUnknownCurrency()
    {
       $this->order->setCurrency('NLG');
    }
    
    public function testValidCurrency()
    {
        $this->order->setCurrency('EUR');
        $this->assertEquals('987', $this->order->getCurrency());
    }
    
    public function testInterfaceVersion()
    {
        $this->assertEquals('HP_1.0', $this->order->getInterfaceVersion());
    }
    
}
