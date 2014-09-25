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
    
    public function testValidMerchantId()
    {
        $this->assertInstanceOf('stefantalen\OmniKassa\OmniKassaOrder',$this->order->setMerchantId('002020000000001'));
    }
    
    public function testSecretKey()
    {
        $this->assertInstanceOf('stefantalen\OmniKassa\OmniKassaOrder', $this->order->setSecretKey('002020000000001_KEY1'));
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
        $this->assertEquals('EUR', $this->order->getCurrency());
    }
    
    /**
     * @expectedException \LengthException
     * @expectedExceptionMessage The transactionReference has a maximum of 32 characters
     */
    public function testLongTransactionReference()
    {
        $this->order->setTransactionReference('abcdefghijklmnopqrstuvwxyz1234567');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The transactionReference can only contain alphanumeric characters
     */
    public function testInvalidTransactionReference()
    {
        $this->order->setTransactionReference('aBcDeFgIijklmnopqrstuvwxyz!');
    }
    
    public function testValidTransactionReference()
    {
        $this->assertSame(null, $this->order->getTransactionReference());
        $this->order->setTransactionReference('aBcDeFgIijklmnopqrstuvwxyz');
        $this->assertSame('aBcDeFgIijklmnopqrstuvwxyz', $this->order->getTransactionReference());
    }
    
    /**
     * @expectedException \LengthException
     */
    public function testKeyVersion()
    {
        $this->order->setKeyVersion('11111111111');
    }
    
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Please set a currency first
     */
    public function testNoCurrencyAmount()
    {
        $this->order->setAmount(106.55);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The amount can only contain numerics and one dot
     */
    public function testInvalidAmount()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('9.9aa')
        ;
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The amount cannot be over 9.999.999.999,99
     */
    public function testAmountTooHigh()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('10000000000.23')
        ;
    }
    
    /**
     * @dataProvider validAmounts
     */
    public function testAmount($currency, $amount, $result)
    {
        $this->order
            ->setCurrency($currency)
            ->setAmount($amount)
        ;
        $this->assertSame($result, $this->order->getAmount());
    }
    
    public function validAmounts()
    {
        return array(
            array('EUR', '1.00', '100'),
            array('EUR', '0.99', '99'),
            array('EUR', '24.99', '2499'),
            array('EUR', '2499.00', '249900'),
            array('JPY', '2499', '2499'),
            array('JPY', '249900', '249900'),
        );
    }
    
    /**
     * @expectedException \LengthException
     * @expectedExceptionMessage The orderId has a maximum of 32 characters
     */
    public function testLongOrderId()
    {
        $this->order->setOrderId('abcdefghijklmnopqrstuvwxyz1234567');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The orderId can only contain alphanumeric characters
     */
    public function testInvalidOrderId()
    {
        $this->order->setOrderId('aBcDeFgIijklmnopqrstuvwxyz!');
    }
    
    public function testValidOrderId()
    {
        $this->assertInstanceOf('stefantalen\OmniKassa\OmniKassaOrder', $this->order->setOrderId('abcdefghijklmnopqrstuvwxyz123456'));
        $this->assertSame('abcdefghijklmnopqrstuvwxyz123456', $this->order->getOrderId());
    }
    
    /**
     * @dataProvider invalidCaptureDays
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The capture day should be an integer value between 1 and 100
     */
    public function testInvalidCaptureDay($days)
    {
        $this->order->setCaptureDay($days);
    }
    
    public function invalidCaptureDays()
    {
        return array(
            array(0),
            array(100),
            array('test'),
            array('10'),
            array(10.5),
        );
    }
    
    public function testValidCaptureDay()
    {
        $this->assertInstanceOf('stefantalen\OmniKassa\OmniKassaOrder', $this->order->setCaptureDay(7));
        $this->assertEquals(7, $this->order->getCaptureDay());
    }
    
    public function testEnableTestMode()
    {
        $this->assertInstanceOf('stefantalen\OmniKassa\OmniKassaOrder', $this->order->enableTestMode());
        return $this->order;
    }
    
    /**
     * @depends testEnableTestMode
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage The Merchant ID cannot be set in test mode
     */
    public function testMerchantIdTestMode(OmniKassaOrder $order)
    {
        $order->setMerchantId('123456789abcdef');
    }
    
    /**
     * @depends testEnableTestMode
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage The secret key cannot be set in test mode
     */
    public function testSecretKeyTestMode(OmniKassaOrder $order)
    {
        $order->setSecretKey('4');
    }
    
    /**
     * @depends testEnableTestMode
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage The keyVersion cannot be set in test mode
     */
    public function testKeyVersionTestMode(OmniKassaOrder $order)
    {
        $order->setKeyVersion('4');
    }
}
