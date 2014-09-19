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
        
    public function setMerchantId($id)
    {
        if(strlen($id) !== 15) {
            throw new \LengthException('The Merchant ID should contain 15 characters');
        }
        $this->merchantId = $merchantId;
    }
    
    public function setSecretKey($key)
    {
        $this->secretKey = $key;
    }
}
