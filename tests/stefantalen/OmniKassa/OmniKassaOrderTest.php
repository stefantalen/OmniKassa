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
        $this->assertEquals('978', $this->order->getCurrency());
    }
    
    public function testInterfaceVersion()
    {
        $this->assertEquals('HP_1.0', $this->order->getInterfaceVersion());
    }
    
    /**
     * @expectedException \LengthException
     * @expectedExceptionMessage The normalReturnUrl cannot be longer than 512 characters
     */
    public function testLongNormalReturnUrl()
    {
        $this->order->setNormalReturnUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adipisicing');
    }
    
    public function testNormalReturnUrl()
    {
        $this->order->setNormalReturnUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi');
        $this->assertEquals('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi', $this->order->getNormalReturnUrl());
        $this->assertEquals(504, strlen($this->order->getNormalReturnUrl()));
    }
    
    public function testAutomaticResponseUrl()
    {
        $this->order->setAutomaticResponseUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi');
        $this->assertEquals('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi', $this->order->getAutomaticResponseUrl());
        $this->assertEquals(504, strlen($this->order->getAutomaticResponseUrl()));
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
            array('EUR', '1', '100'),
            array('EUR', '0.99', '99'),
            array('EUR', '24.99', '2499'),
            array('EUR', '2499', '249900'),
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
    }
    
    /**
     * @dataProvider invalidLanguages
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The given language code does not comply with the ISO 639-1 Alpha2 standard
     */
    public function testInvalidCustomerLanguage($language)
    {
       $this->order->setCustomerLanguage($language);
    }
    
    public function invalidLanguages()
    {
        return array(
            array('nld'),
            array('NL'),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The requested language "fy" is not available
     */
    public function testUnknownCustomerLanguage()
    {
       $this->order->setCustomerLanguage('fy');
    }
    
    
    public function testValidLanguage()
    {
        $this->order->setCustomerLanguage('nl');
        $this->assertEquals('NL', $this->order->getCustomerLanguage());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage setPaymentMeanBrandList() requires the first argument to be an array
     */    
    public function testNonArrayArgumentPaymentMeanBrandList()
    {
        $this->assertEquals(array(), $this->order->getPaymentMeanBrandList());
        $this->order->setPaymentMeanBrandList('lorem');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The payment method "CONTANT" is not available. Available options are: IDEAL, MINITIX, VISA, MASTERCARD, MAESTRO, VPAY, BCMC, INCASSO, ACCEPTGIRO, REMBOURS 
     */
    public function testInvalidAddPayment()
    {
        $this->order->addPaymentMeanBrand('CONTANT');
    }
    
    public function testAddPayment()
    {
        $this->assertCount(0, $this->order->getPaymentMeanBrandList());
        $this->order->addPaymentMeanBrand('IDEAL');
        $this->assertCount(1, $this->order->getPaymentMeanBrandList());
        $this->assertEquals(array('IDEAL'), $this->order->getPaymentMeanBrandList());
        
        return $this->order;
    }
    
    /**
     * @depends testAddPayment
     */
    public function testSetPayment($order)
    {
        $this->assertCount(1, $order->getPaymentMeanBrandList());
        $order->setPaymentMeanBrandList(array(
            'VISA',
            'MAESTRO',
        ));
        $this->assertCount(2, $order->getPaymentMeanBrandList());
        $this->assertEquals(array('VISA', 'MAESTRO'), $order->getPaymentMeanBrandList());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The expiration date should be in the future
     */
    public function testInvalidExpirationDate()
    {
        $this->order->setExpirationDate(new \DateTime());
    }
    
    public function testValidExpirationDate()
    {
        $this->assertEquals(null, $this->order->getExpirationDate());
        $this->assertInstanceOf(
            'stefantalen\OmniKassa\OmniKassaOrder',
            $this->order->setExpirationDate(
                \DateTime::createFromFormat('d-m-Y H:i:s T', '01-01-2100 13:37:00 +0300')
            )
        );
        $this->assertEquals('2100-01-01T13:37:00+0300', $this->order->getExpirationDate());
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
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No currency specified
     */
    public function testInValidOmniKassaOrderDataCurrency()
    {
        $this->order
            ->getData();
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No amount specified
     */
    public function testInValidOmniKassaOrderDataAmount()
    {
        $this->order
            ->setCurrency('EUR')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No merchantId specified
     */
    public function testInValidOmniKassaOrderDataMerchantId()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No orderId specified
     */
    public function testInValidOmniKassaOrderDataOrderId()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No normalReturnUrl specified
     */
    public function testInValidOmniKassaOrderDataNormalReturnUrl()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setOrderId('ORD0000001')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No automaticResponseUrl specified
     */
    public function testInValidOmniKassaOrderDataAutomaticResponseUrl()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setOrderId('ORD0000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No transactionReference specified
     */
    public function testInValidOmniKassaOrderDataTransactionReference()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setOrderId('ORD0000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->setAutomaticResponseUrl('http://www.company.com/response')
            ->getData()
        ;
    }
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No keyVersion specified
     */
    public function testInValidOmniKassaOrderDataKeyVersion()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setOrderId('ORD0000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->setAutomaticResponseUrl('http://www.company.com/response')
            ->setTransactionReference('ORD0000001A1')
            ->getData()
        ;
    }
    
    public function testValidOmniKassaOrderData()
    {
        $this->order
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setOrderId('ORD0000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->setAutomaticResponseUrl('http://www.company.com/response')
            ->setTransactionReference('ORD0000001A1')
            ->setKeyVersion('1')
        ;
        $this->assertEquals('currency=987|amount=2599|merchantId=002020000000001|orderId=ORD0000001|normalReturnUrl=http%3A%2F%2Fwww.company.com%2Freturn|automaticResponseUrl=http%3A%2F%2Fwww.company.com%2Fresponse|transactionReference=ORD0000001A1|keyVersion=1', $this->order->getData());
        return $this->order;
    }
    
    /**
     * @depends testValidOmniKassaOrderData
     */
    public function testValidOmniKassaOrderWithOptionalCustomerLanguageData(OmniKassaOrder $order)
    {
        $order->setCustomerLanguage('en');
        $this->assertEquals('currency=987|amount=2599|merchantId=002020000000001|orderId=ORD0000001|normalReturnUrl=http%3A%2F%2Fwww.company.com%2Freturn|automaticResponseUrl=http%3A%2F%2Fwww.company.com%2Fresponse|transactionReference=ORD0000001A1|keyVersion=1|customerLanguage=EN', $order->getData());
        return $order;
    }
    
    /**
     * @depends testValidOmniKassaOrderWithOptionalCustomerLanguageData
     */
    public function testValidOmniKassaOrderWithOptionalPaymentData(OmniKassaOrder $order)
    {
        $order->addPaymentMeanBrand('IDEAL');
        $this->assertEquals('currency=987|amount=2599|merchantId=002020000000001|orderId=ORD0000001|normalReturnUrl=http%3A%2F%2Fwww.company.com%2Freturn|automaticResponseUrl=http%3A%2F%2Fwww.company.com%2Fresponse|transactionReference=ORD0000001A1|keyVersion=1|customerLanguage=EN|paymentMeanBrandList=IDEAL', $order->getData());
        
        $order->addPaymentMeanBrand('MINITIX');
        $this->assertEquals('currency=987|amount=2599|merchantId=002020000000001|orderId=ORD0000001|normalReturnUrl=http%3A%2F%2Fwww.company.com%2Freturn|automaticResponseUrl=http%3A%2F%2Fwww.company.com%2Fresponse|transactionReference=ORD0000001A1|keyVersion=1|customerLanguage=EN|paymentMeanBrandList=IDEAL%2CMINITIX', $order->getData());
        return $order;
    }
}
