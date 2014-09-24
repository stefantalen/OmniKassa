<?php

namespace stefantalen\OmniKassa\Tests;

use stefantalen\OmniKassa\OmniKassaRequest;

class OmniKassaRequestTest extends \PHPUnit_Framework_TestCase
{
    protected $request;
    
    public function setUp()
    {
        $this->request = new OmniKassaRequest();
    }
    
    public function testInterfaceVersion()
    {
        $this->assertEquals('HP_1.0', $this->request->getInterfaceVersion());
    }

    /**
     * @expectedException \LengthException
     * @expectedExceptionMessage The normalReturnUrl cannot be longer than 512 characters
     */
    public function testLongNormalReturnUrl()
    {
        $this->request->setNormalReturnUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adipisicing');
    }
    
    public function testNormalReturnUrl()
    {
        $this->request->setNormalReturnUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi');
        $this->assertEquals('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi', $this->request->getNormalReturnUrl());
        $this->assertEquals(504, strlen($this->request->getNormalReturnUrl()));
    }
    
    public function testAutomaticResponseUrl()
    {
        $this->request->setAutomaticResponseUrl('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi');
        $this->assertEquals('http://www.company.com/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-sed-do-eiusmod-tempor-incididunt-ut-labore-et-dolore-magna-aliqua-ut-enim-ad-minim-veniam-quis-nostrud-exercitation-ullamco-laboris-nisi-ut-aliquip-ex-ea-commodo-consequat-duis-aute-irure-dolor-in-reprehenderit-in-voluptate-velit-esse-cillum-dolore-eu-fugiat-nulla-pariatur-excepteur-sint-occaecat-cupidatat-non-proident-sunt-in-culpa-qui-officia-deserunt-mollit-anim-id-est-laborum-lorem-ipsum-dolor-sit-amet-consectetur-adi', $this->request->getAutomaticResponseUrl());
        $this->assertEquals(504, strlen($this->request->getAutomaticResponseUrl()));
    }
    
    
    /**
     * @dataProvider invalidLanguages
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The given language code does not comply with the ISO 639-1 Alpha2 standard
     */
    public function testInvalidCustomerLanguage($language)
    {
       $this->request->setCustomerLanguage($language);
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
       $this->request->setCustomerLanguage('fy');
    }
    
    
    public function testValidLanguage()
    {
        $this->request->setCustomerLanguage('nl');
        $this->assertEquals('NL', $this->request->getCustomerLanguage());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage setPaymentMeanBrandList() requires the first argument to be an array
     */    
    public function testNonArrayArgumentPaymentMeanBrandList()
    {
        $this->assertEquals(array(), $this->request->getPaymentMeanBrandList());
        $this->request->setPaymentMeanBrandList('lorem');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The payment method "CONTANT" is not available. Available options are: IDEAL, MINITIX, VISA, MASTERCARD, MAESTRO, VPAY, BCMC, INCASSO, ACCEPTGIRO, REMBOURS 
     */
    public function testInvalidAddPayment()
    {
        $this->request->addPaymentMeanBrand('CONTANT');
    }
    
    public function testAddPayment()
    {
        $this->assertCount(0, $this->request->getPaymentMeanBrandList());
        $this->request->addPaymentMeanBrand('IDEAL');
        $this->assertCount(1, $this->request->getPaymentMeanBrandList());
        $this->assertEquals(array('IDEAL'), $this->request->getPaymentMeanBrandList());
        
        return $this->request;
    }
    
    /**
     * @depends testAddPayment
     */
    public function testSetPayment($request)
    {
        $this->assertCount(1, $request->getPaymentMeanBrandList());
        $request->setPaymentMeanBrandList(array(
            'VISA',
            'MAESTRO',
        ));
        $this->assertCount(2, $request->getPaymentMeanBrandList());
        $this->assertEquals(array('VISA', 'MAESTRO'), $request->getPaymentMeanBrandList());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The expiration date should be in the future
     */
    public function testInvalidExpirationDate()
    {
        $this->request->setExpirationDate(new \DateTime());
    }
    
    public function testValidExpirationDate()
    {
        $this->assertEquals(null, $this->request->getExpirationDate());
        $this->assertInstanceOf(
            'stefantalen\OmniKassa\OmniKassaRequest',
            $this->request->setExpirationDate(
                \DateTime::createFromFormat('d-m-Y H:i:s T', '01-01-2100 13:37:00 +0300')
            )
        );
        $this->assertEquals('2100-01-01T13:37:00+0300', $this->request->getExpirationDate());
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No amount specified
     */
    public function testInValidOmniKassaRequestDataCurrency()
    {
        $this->request
            ->getData();
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No merchantId specified
     */
    public function testInValidOmniKassaRequestDataMerchantId()
    {
        $this->request
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->getData()
        ;
    }
    
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No normalReturnUrl specified
     */
    public function testInValidOmniKassaRequestDataNormalReturnUrl()
    {
        $this->request
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No automaticResponseUrl specified
     */
    public function testInValidOmniKassaRequestDataAutomaticResponseUrl()
    {
        $this->request
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No transactionReference specified
     */
    public function testInValidOmniKassaRequestDataTransactionReference()
    {
        $this->request
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->setAutomaticResponseUrl('http://www.company.com/response')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No orderId specified
     */
    public function testInValidOmniKassaRequestDataOrderId()
    {
        $this->request
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->setAutomaticResponseUrl('http://www.company.com/response')
            ->setTransactionReference('ORD0000001A1')
            ->getData()
        ;
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No keyVersion specified
     */
    public function testInValidOmniKassaRequestDataKeyVersion()
    {
        $this->request
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->setAutomaticResponseUrl('http://www.company.com/response')
            ->setTransactionReference('ORD0000001A1')
            ->setOrderId('ORD0000001')
            ->getData()
        ;
    }
    
    public function testValidOmniKassaRequestData()
    {
        $this->request
            ->setCurrency('EUR')
            ->setAmount('25.99')
            ->setMerchantId('002020000000001')
            ->setNormalReturnUrl('http://www.company.com/return')
            ->setAutomaticResponseUrl('http://www.company.com/response')
            ->setTransactionReference('ORD0000001A1')
            ->setOrderId('ORD0000001')
            ->setKeyVersion('1')
        ;
        $this->assertEquals('amount=2599|currencyCode=978|merchantId=002020000000001|normalReturnUrl=http://www.company.com/return|automaticResponseUrl=http://www.company.com/response|transactionReference=ORD0000001A1|orderId=ORD0000001|keyVersion=1', $this->request->getData());
        return $this->request;
    }
    
    /**
     * @depends testValidOmniKassaRequestData
     */
    public function testValidOmniKassaRequestWithOptionalCustomerLanguageData(OmniKassaRequest $request)
    {
        $request->setCustomerLanguage('en');
        $this->assertEquals('amount=2599|currencyCode=978|merchantId=002020000000001|normalReturnUrl=http://www.company.com/return|automaticResponseUrl=http://www.company.com/response|transactionReference=ORD0000001A1|orderId=ORD0000001|keyVersion=1|customerLanguage=EN', $request->getData());
        return $request;
    }
    
    /**
     * @depends testValidOmniKassaRequestWithOptionalCustomerLanguageData
     */
    public function testValidOmniKassaRequestWithOptionalPaymentData(OmniKassaRequest $request)
    {
        $request->addPaymentMeanBrand('IDEAL');
        $this->assertEquals('amount=2599|currencyCode=978|merchantId=002020000000001|normalReturnUrl=http://www.company.com/return|automaticResponseUrl=http://www.company.com/response|transactionReference=ORD0000001A1|orderId=ORD0000001|keyVersion=1|customerLanguage=EN|paymentMeanBrandList=IDEAL', $request->getData());
        
        $request->addPaymentMeanBrand('MINITIX');
        $this->assertEquals('amount=2599|currencyCode=978|merchantId=002020000000001|normalReturnUrl=http://www.company.com/return|automaticResponseUrl=http://www.company.com/response|transactionReference=ORD0000001A1|orderId=ORD0000001|keyVersion=1|customerLanguage=EN|paymentMeanBrandList=IDEAL,MINITIX', $request->getData());
        return $request;
    }
    
    /**
     * @depends testValidOmniKassaRequestData
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage A secret key must be provided
     */
    public function testInvalidSeal(OmniKassaRequest $request)
    {
        $request->getSeal();
    }
    
    public function testValidSeal()
    {
        $request = new OmniKassaRequest();
        $request
            ->setCurrency('EUR')
            ->setAmount('0.55')
            ->setMerchantId('002020000000001')
            ->setNormalReturnUrl('http://www.normalreturnurl.nl')
            ->setAutomaticResponseUrl('http://www.autoresponse.nl')
            ->setTransactionReference('534654')
            ->setOrderId('201208345')
            ->setKeyVersion('1')
        ;
        $request->setSecretKey('002020000000001_KEY1');
        $this->assertEquals(
            '6fa2fcf410bd00ff0bccc52ee91a59f46d3983d328aea0426a8edffc6deeff09',
            $request->getSeal()
        );
        
        return $request;
    }
    
    public function testActionUrl()
    {
        $this->assertEquals('https://payment-webinit.omnikassa.rabobank.nl/paymentServlet', $this->request->getActionUrl());
    }
    
    /**
     * @depends testValidSeal
     */
    public function testEnableTestMode(OmniKassaRequest $request)
    {
        $this->assertEquals('https://payment-webinit.omnikassa.rabobank.nl/paymentServlet', $request->getActionUrl());
        $this->assertInstanceOf('stefantalen\OmniKassa\OmniKassaRequest', $request->enableTestMode());
        $this->assertEquals('https://payment-webinit.simu.omnikassa.rabobank.nl/paymentServlet', $request->getActionUrl());
        return $request;
    }
}
