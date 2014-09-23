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
     * @var $transactionReference string
     */
    protected $transactionReference;
    
    /**
     * @var $keyVersion string
     */
    protected $keyVersion;
    
    /**
     * @var $amount string
     */
    protected $amount;
    
    /**
     * @var $orderId string
     */
    protected $orderId;
    
    /**
     * @var $customerLanguage string
     */
    protected $customerLanguage;
    
    /**
     * @var $paymentMeanBrandList array
     */
    protected $paymentMeanBrandList;
    
    /**
     * @var expirationDate string
     */
    protected $expirationDate;
    
    /**
     * @var captureDay int
     */
    protected $captureDay;
    
    /**
     * @var captureMode string
     */
    protected $captureMode;
    
    public function __construct()
    {
        $this->paymentMeanBrandList = array();
    }
    
    /**
     * Set the merchant id provided by OmniKassa
     *
     * @return OmniKassaOrder
     *
     * @throws \LengthException if the length of the ID is not 15 characters
     */
    public function setMerchantId($id)
    {
        if (strlen($id) !== 15) {
            throw new \LengthException('The Merchant ID should contain 15 characters');
        }
        $this->merchantId = $id;
        return $this;
    }
    
    /**
     * Set the secret key provided by OmniKassa
     *
     * @param $key string The secrey key
     *
     * @return OmniKassaOrder
     */
    public function setSecretKey($key)
    {
        $this->secretKey = $key;
        return $this;
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
            'EUR' => '978',
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
        // Check string size according to RFC 3986
        if (strlen(rawurlencode($url)) > 512) {
            throw new \LengthException(sprintf('The %s cannot be longer than 512 characters', $property));
        }
        return $url;
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
     * The transaction reference
     * @param $reference string The reference
     * @return \LengthException|\InvalidArgumentException|OmniKassaOrder
     */
    public function setTransactionReference($reference)
    {
        if (strlen($reference) > 32) {
            throw new \LengthException('The transactionReference has a maximum of 32 characters');
        }
        if (!preg_match('/^[a-z0-9]+$/i', $reference)) {
            throw new \InvalidArgumentException('The transactionReference can only contain alphanumeric characters');
        }
        $this->transactionReference = $reference;
        return $this;
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
    
    /**
     * Set the amount of the order
     * @param $amount string
     * @return \LogicException|\InvalidArgumentException|OmniKassaOrder
     */
    public function setAmount($amount)
    {
        // A currency must be set
        if (null === $this->currency) {
            throw new \LogicException('Please set a currency first');
        }
        // Check if the amount is a valid value
        if (!preg_match('/^([0-9]+)(\.{1}[0-9]{1,2})?$/', $amount, $matches)) {
            throw new \InvalidArgumentException('The amount can only contain numerics and one dot');
        }
        
        // Add decimals to value the currency is not Japanese Yen
        if ($this->currency !== '392') {
            if (isset($matches[2])) {
                $amount = '' . intval($matches[1] . substr($matches[2], 1));
            } else {
                $amount = $matches[1] . '00';
            }
        }
        // Check the maximum value
        if ($amount > 999999999999) {
            throw new \InvalidArgumentException('The amount cannot be over 9.999.999.999,99' );
        }
        $this->amount = $amount;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * Set the order ID to give the transaction a reference
     *
     * @param $orderId string The order ID
     *
     * @return OmniKassaOrder
     *
     * @throws \LengthException when the orderId has more than 32 characters
     * @throws \InvalidArgumentException when the orderId has invalid characters
     */
    public function setOrderId($orderId)
    {
        if (strlen($orderId) > 32) {
            throw new \LengthException('The orderId has a maximum of 32 characters');
        }
        if (!preg_match('/^[a-z0-9]+$/i', $orderId)) {
            throw new \InvalidArgumentException('The orderId can only contain alphanumeric characters');
        }
        $this->orderId = $orderId;
        return $this;
    }
    
    /**
     * @param $language string The language in which the payment portal should be shown
     * @return OmniKassaOrder
     */
    public function setCustomerLanguage($language)
    {
        // Only checking lower case characters since that is according to the standard
        if (!preg_match('/^[a-z]{2}$/', $language)) {
            throw new \InvalidArgumentException('The given language code does not comply with the ISO 639-1 Alpha2 standard');
        }
        $languages = array(
            'cs', // Czech
            'cy', // Welsh
            'de', // German
            'en', // English
            'es', // Spanish
            'fr', // French
            'nl', // Dutch
            'sk', // Swedish
        );
        if (!in_array($language, $languages)) {
            throw new \InvalidArgumentException(sprintf('The requested language "%s" is not available', $language));
        }
        // Converting the given language to upper case because OmniKassa expects this
        $this->customerLanguage = strtoupper($language);
        
        return $this;
    }
    
    /**
     * Get the customer language
     * @return string
     */
    public function getCustomerLanguage()
    {
        return $this->customerLanguage;
    }
    
    /**
     * Set the paymentMeanBrandList
     * @param $list array An array of payment methods
     * @return \InvalidArgumentException|OmniKassaOrder
     */
    public function setPaymentMeanBrandList($list)
    {
        if (!is_array($list)) {
            throw new \InvalidArgumentException('setPaymentMeanBrandList() requires the first argument to be an array');
        }
        // Resetting the array
        $this->paymentMeanBrandList = array();
        
        foreach($list as $paymentMethod) {
            $this->addPaymentMeanBrand($paymentMethod);
        }
        return $this;
    }

    /**
     * Add an element to the paymentMeanBrandList
     * @param $list array An array of payment methods
     * @return \InvalidArgumentException|OmniKassaOrder
     */
    public function addPaymentMeanBrand($paymentMethod)
    {
        $availableMethods = array(
            'IDEAL',
            'MINITIX',
            'VISA',
            'MASTERCARD',
            'MAESTRO',
            'VPAY',
            'BCMC',
            'INCASSO',
            'ACCEPTGIRO',
            'REMBOURS',
        );
        if (!in_array($paymentMethod, $availableMethods)) {
            throw new \InvalidArgumentException(sprintf('The payment method "%s" is not available. Available options are: %s', $paymentMethod, implode(', ', $availableMethods)));
        }
        $this->paymentMeanBrandList[] = $paymentMethod;
        return $this;
    }
    
    /**
     * Get the paymentMeanBrandList array
     */
    public function getPaymentMeanBrandList()
    {
        return $this->paymentMeanBrandList;
    }
    
    /**
     * Set the expiration date in ISO 8601 format
     * @param $expirationDate \DateTime The date the payment expires
     * @return \InvalidArgumentException|OmniKassaOrder
     */
    public function setExpirationDate(\DateTime $expirationDate)
    {
        if ($expirationDate <= new \DateTime()) {
            throw new \InvalidArgumentException('The expiration date should be in the future');
        }
        $this->expirationDate = $expirationDate->format(\DateTime::ISO8601);
        return $this;
    }
    
    /**
     * Get the expiration date
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
    
    /**
     * Set the number of days after authorization of a creditcard transaction in which a validation of the transaction will be executed.
     *
     * @param int $days The number of days
     *
     * @return OmniKassaOrder
     *
     * @throws \InvalidArgumentException if the number is below 1 or higher than 99
     */
    public function setCaptureDay($days)
    {
        if (!is_int($days) || $days <= 0 || $days > 99) {
           throw new \InvalidArgumentException('The capture day should be an integer value between 1 and 100');
        }
        $this->captureDay = $days;
        return $this;
    }
    
    /**
     * Set the number of days after authorization of a creditcard transaction in which a validation of the transaction will be executed.
     *
     * @return int
     */
    public function getCaptureDay()
    {
        return $this->captureDay;
    }
    
    /**
     * Set the capture mode
     *
     * @todo Implementation of this function
     *
     * @throws \Exception Because this function is not yet implemented the user gets this exception
     */
    public function setCaptureMode($mode)
    {
        throw new \Exception('This function is not yet implemented');
    }
    
    /**
     * Get the capture mode
     *
     * @return string
     */
    public function getCaptureMode()
    {
        return $this->captureMode;
    }
    
    /**
     * Get all the data
     *
     * @return string Formatted string containing all data according to the OmniKassa requirement
     *
     * @throws \BadMethodCallException if no currency is specified
     * @throws \BadMethodCallException if no amount is specified
     * @throws \BadMethodCallException if no merchantId is specified
     * @throws \BadMethodCallException if no orderId is specified
     * @throws \BadMethodCallException if no normalReturnUrl is specified
     * @throws \BadMethodCallException if no automaticResponseUrl is specified
     * @throws \BadMethodCallException if no transactionReference is specified
     * @throws \BadMethodCallException if no keyVersion is specified
     *
     */
    public function getData()
    {
        // Required fields
        $data = array(
            'currency' => $this->currency,
            'amount' => $this->amount,
            'merchantId' => $this->merchantId,
            'orderId' => $this->orderId,
            'normalReturnUrl' => $this->normalReturnUrl,
            'automaticResponseUrl' => $this->automaticResponseUrl,
            'transactionReference' => $this->transactionReference,
            'keyVersion' => $this->keyVersion            
        );
        foreach ($data as $key => $value)
        {
            if (null == $value) {
                throw new \BadMethodCallException(sprintf('No %s specified', $key));
            }
        }
        $optionalData = array(
            'customerLanguage' => $this->customerLanguage,
            'expirationDate' => $this->expirationDate,
            'captureDay' => $this->captureDay,
            'captureMode' => $this->captureMode
        );
        if (sizeof($this->paymentMeanBrandList) > 0) {
            $optionalData['paymentMeanBrandList'] = implode(',', $this->paymentMeanBrandList);
        }
        foreach ($optionalData as $key => $value)
        {
            if (null !== $value) {
                $data[$key] = $value;
            }
        }
        return http_build_query($data,'','|');
    }
}
