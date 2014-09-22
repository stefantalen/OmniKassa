<?php

namespace stefantalen\OmniKassa;

use stefantalen\OmniKassa\OmniKassaRequest;

class OmniKassaOrder
{

    /**
     * @var $merchantId string
     */
    protected $merchantId;
    
    /**
     * @var $secretKey string
     */
    protected $secretKey;
    
    /**
     * @var $currency string
     */
    protected $currency;
    
    /**
     * @var $interfaceVersion string
     */
    protected $interfaceVersion = 'HP_1.0';
    
    /**
     * @var $normalReturnUrl string
     */
    protected $normalReturnUrl;
    
    /**
     * @var $automaticResponseUrl string
     */
    protected $automaticResponseUrl;
    
    /**
     * @var $keyVersion string
     */
    protected $keyVersion;
        
    public function setMerchantId($id)
    {
        if (strlen($id) !== 15) {
            throw new \LengthException('The Merchant ID should contain 15 characters');
        }
        $this->merchantId = $merchantId;
    }
    
    public function setSecretKey($key)
    {
        $this->secretKey = $key;
    }
    
    /**
     * @param $currencyCode string
     * @return OmniKassaOrder
     */
    public function setCurrency($currencyCode)
    {
        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
            throw new \InvalidArgumentException('The given currency does not comply with the ISO 4217 standard');
        }
        $currencyCodes = array(
            'EUR' => '987',
            'USD' => '840',
            'CHF' => '756',
            'GBP' => '824',
            'CAD' => '124',
            'JPY' => '392',
            'AUD' => '036',
            'NOK' => '578',
            'SEK' => '752',
            'DKK' => '208',
        );
        if (!array_key_exists($currencyCode, $currencyCodes)) {
            throw new \InvalidArgumentException(sprintf('The requested currency "%s" is not available', $currencyCode));
        }
        $this->currency = $currencyCodes[$currencyCode];
        
        return $this;
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }
        
    public function getInterfaceVersion()
    {
        return $this->interfaceVersion;
    }
    
    /**
     * Encodes the given URL according to RFC 3986 and checks the length
     * @param $url string The URL
     * @param $property string The property that is being checked
     * @return \LengthException|string
     */
    protected function validateUrl($url, $property)
    {
        // Encode string according to RFC 3986
        $encodedUrl = rawurlencode($url);
        if (strlen($encodedUrl) > 512) {
            throw new \LengthException(sprintf('The %s cannot be longer than 512 characters', $property));
        }
        return $encodedUrl;
    }
    
    /**
     * @param $url string The URL where the user returns after the payment
     * @return LengthException|OmniKassaOrder
     */
    public function setNormalReturnUrl($url)
    {
        $this->normalReturnUrl = $this->validateUrl($url, 'normalReturnUrl');
        return $this;
    }
    
    /**
     * @return string The URL where the user returns after the payment
     */
    public function getNormalReturnUrl()
    {
        return $this->normalReturnUrl;
    }
    
    /**
     * @param $url string The URL where the cronjob returns after the payment
     * @return LengthException|OmniKassaOrder
     */
    public function setAutomaticResponseUrl($url)
    {
        $this->automaticResponseUrl = $this->validateUrl($url, 'automaticResponseUrl');
        return $this;
    }
    
    /**
     * @return string The URL where the user returns after the payment
     */
    public function getAutomaticResponseUrl()
    {
        return $this->automaticResponseUrl;
    }
    
    /**
     * The version number of the secret key, can be found on the OmniKassa website
     * @param $version string The version number
     * @return \LengthException|OmniKassaOrder
     */
    public function setKeyVersion($version)
    {
        if (strlen($version) > 10) {
            throw new \LengthException('The keyVersion has a maximum of 10 characters');
        }
        $this->keyVersion = $version;
        return $this;
    }
}
