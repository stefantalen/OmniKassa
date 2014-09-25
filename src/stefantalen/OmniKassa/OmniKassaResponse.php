<?php

namespace stefantalen\OmniKassa;

use stefantalen\OmniKassa\OmniKassaOrder;

class OmniKassaResponse extends OmniKassaOrder
{
    /**
     * @var $data string
     */
    protected $data;
    /**
     * @var $seal string
     */
    protected $seal;
    
    /**
     * Handle the POST array
     *
     * @param $postArray The POST array
     *
     * @throws \InvalidArgumentException if the Data key does not exist
     * @throws \InvalidArgumentException if the Seal key does not exist
     *
     */
    public function __construct($postArray = array())
    {
        // Check if the required fields are present in the array
        if (!isset($postArray['Data'])) {
            throw new \InvalidArgumentException('The array should contain a "Data" key');
        }
        if (!isset($postArray['Seal'])) {
            throw new \InvalidArgumentException('The array should contain a "Seal" key');
        }
        $this->data = $postArray['Data'];
        $this->seal = $postArray['Seal'];
    }
    
    /**
     * Validate the response
     *
     * @throws \UnexpectedValueException if the response is not valid
     *
     */
    public function validate()
    {
        if ($this->seal === $this->getSeal()) {
            $this->handleData($this->data);
        } else {
            throw new \UnexpectedValueException('This response is not valid');
        }
    }
    
    /**
     * Set the currency based in the code
     *
     * @return OmniKassaResponse
     *
     */
    public function setCurrencyCode($code)
    {
        if (!in_array($code, $this->currencyCodes)) {
            throw new \InvalidArgumentException(sprintf('The requested currency code "%s" is not available', $code));
        }
        $this->currency = $code;
        return $this;
    }
    
    /**
     * Convert the Data string
     *
     * @param $dataString string The Data string provided by OmniKassa
     */
    protected function handleData($dataString)
    {
        $dataArray = explode('|', $dataString);
        $data = array();
        foreach ($dataArray as $d) {
            list($k, $v) = explode('=', $d);
            $data[$k] = $v;
        }
        $this
            ->setCurrencyCode($data['currencyCode'])
            ->setAmount($data['amount'])
            ->setMerchantId($data['merchantId'])
            ->setTransactionReference($data['transactionReference'])
            ->setKeyVersion($data['keyVersion'])
            ->setOrderId($data['orderId'])
        ;
    }
    
    /**
     * Get the seal
     *
     * @return string the seal
     *
     * @throws \BadMethodCallException if no secret key is specified
     */
    protected function getSeal()
    {
        if (null === $this->secretKey) {
            throw new \BadMethodCallException('A secret key must be provided');
        }
        return hash('sha256', utf8_encode($this->data. $this->secretKey));
    }
    
     /**
     * Set the amount of the order
     * @param $amount string
     * @return \LogicException|\InvalidArgumentException|OmniKassaResponse
     */
    public function setAmount($amount)
    {
        // A currency must be set
        if (null === $this->currency) {
            throw new \LogicException('Please set a currency first');
        }
        // Check if the amount is a valid value
        if (!preg_match('/^[0-9]*$/', $amount)) {
            throw new \InvalidArgumentException('The amount can only contain numerics');
        }
        
        // Add decimals to value the currency is not Japanese Yen
        if ($this->currency !== '392') {
            if ($amount >= 100) {
                $amount = preg_replace('/^([0-9]*)([0-9]{2})$/', '$1.$2', $amount);
            } else {
                $amount = '0.'. $amount;
            }
        }
        $this->amount = $amount;
        return $this;
    }
}
