<?php

namespace stefantalen\OmniKassa\Tests;

use stefantalen\OmniKassa\OmniKassaResponse;
use stefantalen\OmniKassa\OmniKassaEvents;

class OmniKassaResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The array should contain a "Data" key
     */
    public function testNoDataInArray()
    {
        $response = new OmniKassaResponse(array());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The array should contain a "Seal" key
     */
    public function testNoSealInArray()
    {
        $response = new OmniKassaResponse(array('Data' => ''));
    }
    
    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage A secret key must be provided
     */
    public function testNoSecretKeyForSeal()
    {
        $response = new OmniKassaResponse(array(
            'Data' => '',
            'Seal' => ''
        ));
        $response->validate();
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage This response is not valid
     */
    public function testInvalidResponse()
    {
        $response = new OmniKassaResponse(array(
            'Data' => '',
            'Seal' => ''
        ));
        $response
            ->setSecretKey('002020000000001_KEY1')
            ->validate();
    }
    
    public function testValidIdealPayment()
    {
        $postRequest = array(
            'Data' => 'amount=55|currencyCode=978|merchantId=002020000000001|normalReturnUrl=http://localhost/omnikassa/example/return.php|automaticResponseUrl=http://localhost/omnikassa/example/response.php|transactionReference=201409240242071|orderId=20140924024207|keyVersion=1|paymentMeanBrandList=IDEAL',
            'InterfaceVersion' => 'HP_1.0',
            'Seal' => 'bfd0067510d0e53a579bb0b32611c4cfefd90d918aab2bd0e91c04f53107a17a',
        );
        
        $postResponse = array(
            'Data' => 'amount=55|captureDay=0|captureMode=AUTHOR_CAPTURE|currencyCode=978|merchantId=002020000000001|orderId=20140924024207|transactionDateTime=2014-09-24T14:43:31+02:00|transactionReference=201409240242071|keyVersion=1|authorisationId=0020000006791167|paymentMeanBrand=IDEAL|paymentMeanType=CREDIT_TRANSFER|responseCode=00',
            'Seal' => '0c4979ac014c5d816f3287d461c9c572872a30de9da280b24ba6e3f8a776917e',
            'InterfaceVersion' => 'HP_1.0',
            'Encode' => ''
        );
        $response = new OmniKassaResponse($postResponse);
        $response
            ->setSecretKey('002020000000001_KEY1')
            ->validate();
        
        $this->assertSame('EUR', $response->getCurrency());
        $this->assertSame('0.55', $response->getAmount());
        $this->assertSame('201409240242071', $response->getTransactionReference());
        $this->assertSame('20140924024207', $response->getOrderId());
        $this->assertSame(OmniKassaEvents::SUCCESS, $response->getResponseCode());
        return $response;
    }
    
    /**
     * @depends testValidIdealPayment
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The requested currency code "123" is not available
     */
    public function testInvalidCurrency(OmniKassaResponse $response)
    {
        $response->setCurrencyCode('123');
    }

    /**
     * @depends testValidIdealPayment
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The amount can only contain numerics
     */
    public function testStringAmount(OmniKassaResponse $response)
    {
        $response->setAmount('abcde');
    }
    
    /**
     * @depends testValidIdealPayment
     * @dataProvider validAmounts
     */
    public function testValidAmount($currency, $input, $output, OmniKassaResponse $response)
    {
        $response->setCurrencyCode($currency);
        $response->setAmount($input);
        $this->assertSame($output, $response->getAmount());
    }
    
    public function validAmounts()
    {
        return array(
            array('978', '100', '1.00'),
            array('978', '99', '0.99'),
            array('978', '2499', '24.99'),
            array('978', '249900', '2499.00'),
            array('392', '2499', '2499'),
            array('392', '249900', '249900'),
        );
    }

    /**
     * @depends testValidIdealPayment
     * @dataProvider invalidTransactionDateTimes
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The transactionDateTime should be in ISO 8601 format
     */
    public function testInvalidTransactionDateTime($input, OmniKassaResponse $response)
    {
        $response->setTransactionDateTime($input);
    }
    
    public function invalidTransactionDateTimes()
    {
        return array(
            array('201-09-24T14:43:31+02:00'),
            array('2014-09-24 14:43:31+02:00'),
            array('2014-09-24 14:43:31'),
            array('24-09-2014 14:43:31'),
            array('2014-09-24 14:43:31'),
        );
    }

    /**
     * @depends testValidIdealPayment
     * @dataProvider validTransactionDateTimes
     */
    public function testValidTransactionDateTime($input, OmniKassaResponse $response)
    {
        $this->assertInstanceOf('stefantalen\OmniKassa\OmniKassaResponse', $response->setTransactionDateTime($input));
        $this->assertInstanceOf('\DateTime', $response->getTransactionDateTime());
    }
    
    public function validTransactionDateTimes()
    {
        return array(
            array('2014-09-24T14:43:31+02:00'),
            array('2015-12-31T14:43:31+10:00'),
        );
    }
    
    public function testCancelledIdealPayment()
    {
        $postRequest = array(
            'Data' => 'amount=200|currencyCode=978|merchantId=002020000000001|normalReturnUrl=http://localhost/omnikassa/example/return.php|automaticResponseUrl=http://localhost/omnikassa/example/response.php|transactionReference=201409250153521|orderId=20140925015352|keyVersion=1|paymentMeanBrandList=IDEAL',
            'InterfaceVersion' => 'HP_1.0',
            'Seal' => 'a5ab1535ce23234fbfa720e210590ad7d7ae611dba6955a62d69ba004482a37d',
        );
        
        $postResponse = array(
            'Data' => 'amount=200|captureDay=0|captureMode=AUTHOR_CAPTURE|currencyCode=978|merchantId=002020000000001|orderId=20140925015352|transactionDateTime=2014-09-25T13:54:14+02:00|transactionReference=201409250153521|keyVersion=1|authorisationId=0020000006791167|paymentMeanBrand=IDEAL|paymentMeanType=CREDIT_TRANSFER|responseCode=17',
            'Seal' => '6d05142a2a153ac147bdaeb20427eb985495c42eb3839a1d431cc0c97154c8df',
            'InterfaceVersion' => 'HP_1.0',
            'Encode' => ''
        );
        $response = new OmniKassaResponse($postResponse);
        $response
            ->setSecretKey('002020000000001_KEY1')
            ->validate();
        
        $this->assertSame('EUR', $response->getCurrency());
        $this->assertSame('2.00', $response->getAmount());
        $this->assertSame('201409250153521', $response->getTransactionReference());
        $this->assertSame('20140925015352', $response->getOrderId());
        $this->assertSame(OmniKassaEvents::CANCELLED, $response->getResponseCode());
    }
}
