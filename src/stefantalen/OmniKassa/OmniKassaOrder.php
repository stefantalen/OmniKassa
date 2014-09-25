<?php

namespace stefantalen\OmniKassa;

class OmniKassaOrder
{
    /**
     * @var $currencyCodes
     */
    protected $currencyCodes = array(
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
     * @var $captureDay int
     */
    protected $captureDay;
    
    /**
     * @var $captureMode string
     */
    protected $captureMode;
        
    /**
     * @var $testMode boolean
     */
    protected $testMode = false;
    
    /**
     * Set the merchant id provided by OmniKassa
     *
     * @param $id string The id
     *
     * @return OmniKassaOrder
     *
     * @throws \BadMethodCallException if test mode is enabled
     * @throws \LengthException if the length of the ID is not 15 characters
     */
    public function setMerchantId($id)
    {
        if ($this->testMode) {
            throw new \BadMethodCallException('The Merchant ID cannot be set in test mode');
        }
        if (strlen($id) !== 15) {
            throw new \LengthException('The Merchant ID should contain 15 characters');
        }
        $this->merchantId = $id;
        return $this;
    }
    
    /**
     * Set the secret key provided by OmniKassa
     *
     * @param $key string The secret key
     *
     * @return OmniKassaOrder
     
     * @throws \BadMethodCallException if test mode is enabled
     */
    public function setSecretKey($key)
    {
        if ($this->testMode) {
            throw new \BadMethodCallException('The secret key cannot be set in test mode');
        }
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
        if (!array_key_exists($currencyCode, $this->currencyCodes)) {
            throw new \InvalidArgumentException(sprintf('The requested currency "%s" is not available', $currencyCode));
        }
        $this->currency = $this->currencyCodes[$currencyCode];
        
        return $this;
    }
    
    public function getCurrency()
    {
        return array_search($this->currency, $this->currencyCodes);
    }
    
    /**
     * Set the transaction reference
     *
     * @param $reference string The reference
     *
     * @return OmniKassaOrder
     *
     * @throws \LengthException if the reference is longer than 32 characters
     * @throws \InvalidArgumentException if the reference contains non-alphanumeric characters
     */
    public function setTransactionReference($reference)
    {
        if (strlen($reference) > 32) {
            throw new \LengthException('The transactionReference has a maximum of 32 characters');
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/i', $reference)) {
            throw new \InvalidArgumentException('The transactionReference can only contain alphanumeric characters');
        }
        $this->transactionReference = $reference;
        return $this;
    }
    
    /**
     * Get the transaction reference
     *
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->transactionReference;
    }
    
    /**
     * The version number of the secret key, can be found on the OmniKassa website
     * @param $version string The version number
     *
     * @return OmniKassaOrder
     *
     * @throws \BadMethodCallException if test mode is enabled
     * @throws \LengthException if the key is longer than 10 characters
     */
    public function setKeyVersion($version)
    {
        if ($this->testMode) {
            throw new \BadMethodCallException('The keyVersion cannot be set in test mode');
        }
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
            throw new \InvalidArgumentException('The amount cannot be over 9.999.999.999,99');
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
     * Get the order id
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
    
    /**
     * Set the number of days after authorization of a creditcard transaction in which a validation of
     * the transaction will be executed.
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
     * Set the number of days after authorization of a creditcard transaction in which a validation of
     * the transaction will be executed.
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
     * Enable test mode
     *
     * @return OmniKassaOrder
     */
    public function enableTestMode()
    {
        $this->actionUrl = "https://payment-webinit.simu.omnikassa.rabobank.nl/paymentServlet";
        $this->setMerchantId('002020000000001');
        $this->setSecretKey('002020000000001_KEY1');
        $this->setKeyVersion('1');
        $this->testMode = true;
        return $this;
    }
}
