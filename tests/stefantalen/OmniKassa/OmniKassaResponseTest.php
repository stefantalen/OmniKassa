<?php

namespace stefantalen\OmniKassa\Tests;

use stefantalen\OmniKassa\OmniKassaResponse;

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
            'Data' => "amount=55|currencyCode=978|merchantId=002020000000001|normalReturnUrl=http://localhost/omnikassa/example/return.php|automaticResponseUrl=http://localhost/omnikassa/example/response.php|transactionReference=201409240242071|orderId=20140924024207|keyVersion=1|paymentMeanBrandList=IDEAL",
            "InterfaceVersion" => "",
            "Seal" => "bfd0067510d0e53a579bb0b32611c4cfefd90d918aab2bd0e91c04f53107a17a",
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
        
        $this->assertEquals('EUR', $response->getCurrency());
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
}
