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
        if (isset($data['currencyCode'])) {
            $this->setCurrencyCode($data['currencyCode']);
        }
        if (isset($data['amount'])) {
            $this->setAmount($data['amount']);
        }
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
}
