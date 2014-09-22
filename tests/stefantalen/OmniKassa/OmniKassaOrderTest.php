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
    
    /**
     * @expectedException \LengthException
     * @expectedExceptionMessage The normalReturnUrl cannot be longer than 512 characters
     */
    public function testLongNormalReturnUrl()
    {
        $this->order->setNormalReturnUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-');
    }
    
    public function testNormalReturnUrl()
    {
        $this->order->setNormalReturnUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi');
        $this->assertEquals('http%3A%2F%2Fwww.company.com%2Florem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi', $this->order->getNormalReturnUrl());
        $this->assertEquals(512, strlen($this->order->getNormalReturnUrl()));
    }
    
    public function testAutomaticResponseUrl()
    {
        $this->order->setAutomaticResponseUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi');
        $this->assertEquals('http%3A%2F%2Fwww.company.com%2Florem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi', $this->order->getAutomaticResponseUrl());
        $this->assertEquals(512, strlen($this->order->getAutomaticResponseUrl()));
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
        $this->assertEquals($result, $this->order->getAmount());
    }
    
    public function validAmounts()
    {
        return array(
            array('EUR', '24.99', '2499'),
            array('EUR', '2499', '249900'),
            array('JPY', '2499', '2499'),
            array('JPY', '249900', '249900'),
        );
    }
}
