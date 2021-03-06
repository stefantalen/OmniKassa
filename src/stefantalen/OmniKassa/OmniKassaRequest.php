<?php

namespace stefantalen\OmniKassa;

use stefantalen\OmniKassa\OmniKassaOrder;

class OmniKassaRequest extends OmniKassaOrder
{
    /**
     * @var string $interfaceVersion
     */
    protected $interfaceVersion = 'HP_1.0';
    
    /**
     * @var string $normalReturnUrl
     */
    protected $normalReturnUrl;
    
    /**
     * @var string $automaticResponseUrl
     */
    protected $automaticResponseUrl;
    
    /**
     * @var string $customerLanguage
     */
    protected $customerLanguage;
    
    /**
     * @var array $paymentMeanBrandList
     */
    protected $paymentMeanBrandList;
    
    /**
     * @var string $expirationDate
     */
    protected $expirationDate;
    
    /**
     * @var string $actionUrl
     */
    protected $actionUrl = "https://payment-webinit.omnikassa.rabobank.nl/paymentServlet";

    
    public function __construct()
    {
        $this->paymentMeanBrandList = array();
    }
    
    /**
     * Get the interface version
     *
     * @return string
     */
    public function getInterfaceVersion()
    {
        return $this->interfaceVersion;
    }
    
    /**
     * Encodes the given URL according to RFC 3986 and checks the length
     *
     * @param string $url The URL
     * @param string $property The property that is being checked
     *
     * @return string
     *
     * @throws \LengthException if the encode url is too long
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
     * Set the URL where the user should return after te payment
     *
     * @param string $url The URL where the user returns after the payment
     *
     * @return OmniKassaRequest
     */
    public function setNormalReturnUrl($url)
    {
        $this->normalReturnUrl = $this->validateUrl($url, 'normalReturnUrl');
        return $this;
    }
    
    /**
     * Get the URL where the user returns after the payment
     *
     * @return string
     */
    public function getNormalReturnUrl()
    {
        return $this->normalReturnUrl;
    }
    
    /**
     * Set the URL where the cronjob returns after the payment
     *
     * @param string $url The URL where the cronjob returns after the payment
     *
     * @return OmniKassaRequest
     */
    public function setAutomaticResponseUrl($url)
    {
        $this->automaticResponseUrl = $this->validateUrl($url, 'automaticResponseUrl');
        return $this;
    }
    
    /**
     * Get the URL where the user returns after the payment
     *
     * @return string 
     */
    public function getAutomaticResponseUrl()
    {
        return $this->automaticResponseUrl;
    }
    
    
    /**
     * Set the language of the payment portal
     *
     * @param string $language The language in which the payment portal should be shown
     *
     * @return OmniKassaRequest
     *
     * @throws \InvalidArgumentException if the language does not comply to the ISO 639-1 standard
     * @throws \InvalidArgumentException if the language is not available
     */
    public function setCustomerLanguage($language)
    {
        // Only checking lower case characters since that is according to the standard
        if (!preg_match('/^[a-z]{2}$/', $language)) {
            throw new \InvalidArgumentException(
                'The given language code does not comply with the ISO 639-1 Alpha2 standard'
            );
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
     *
     * @return string
     */
    public function getCustomerLanguage()
    {
        return $this->customerLanguage;
    }
    
    /**
     * Set the paymentMeanBrandList
     *
     * @param array $list An array of payment methods
     *
     * @return OmniKassaRequest
     *
     * @throws \InvalidArgumentException if the parameter is not an array
     */
    public function setPaymentMeanBrandList($list)
    {
        if (!is_array($list)) {
            throw new \InvalidArgumentException('setPaymentMeanBrandList() requires the first argument to be an array');
        }
        // Resetting the array
        $this->paymentMeanBrandList = array();
        
        foreach ($list as $paymentMethod) {
            $this->addPaymentMeanBrand($paymentMethod);
        }
        return $this;
    }

    /**
     * Add an element to the paymentMeanBrandList
     *
     * @param string $paymentMethod A payment method
     *
     * @return OmniKassaRequest
     *
     * @throws \InvalidArgumentException if the payment method is not available
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
            throw new \InvalidArgumentException(sprintf(
                'The payment method "%s" is not available. Available options are: %s',
                $paymentMethod,
                implode(', ', $availableMethods)
            ));
        }
        $this->paymentMeanBrandList[] = $paymentMethod;
        return $this;
    }
    
    /**
     * Get the paymentMeanBrandList array
     *
     * @return array
     */
    public function getPaymentMeanBrandList()
    {
        return $this->paymentMeanBrandList;
    }
    
    /**
     * Set the expiration date in ISO 8601 format
     *
     * @param \DateTime $expirationDate The date the payment expires
     *
     * @return OmniKassaRequest
     *
     * @throws \InvalidArgumentException is the date is not in the future
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
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
    
    /**
     * Get a formatted string containing all data according to the OmniKassa requirement
     *
     * @return string
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
            'amount' => $this->amount,
            'currencyCode' => $this->currency,
            'merchantId' => $this->merchantId,
            'normalReturnUrl' => $this->normalReturnUrl,
            'automaticResponseUrl' => $this->automaticResponseUrl,
            'transactionReference' => $this->transactionReference,
            'orderId' => $this->orderId,
            'keyVersion' => $this->keyVersion
        );
        foreach ($data as $key => $value) {
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
        foreach ($optionalData as $key => $value) {
            if (null !== $value) {
                $data[$key] = $value;
            }
        }
        return implode(
            '|',
            array_map(
                function ($v, $k) {
                    return sprintf('%s=%s', $k, $v);
                },
                $data,
                array_keys($data)
            )
        );
    }
    
    /**
     * Get the seal
     *
     * @return string
     *
     * @throws \BadMethodCallException if no secret key is specified
     */
    public function getSeal()
    {
        if (null === $this->secretKey) {
            throw new \BadMethodCallException('A secret key must be provided');
        }
        return hash('sha256', utf8_encode($this->getData(). $this->secretKey));
    }
    
    /**
     * Get the action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->actionUrl;
    }
    
    /**
     * @inheritDoc
     */
    public function enableTestMode()
    {
        parent::enableTestMode();
        $this->actionUrl = "https://payment-webinit.simu.omnikassa.rabobank.nl/paymentServlet";
        return $this;
    }
}
