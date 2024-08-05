<?php

namespace Modules\Gateways\Library;

class Payment
{
    private $hook;
    private $currencyCode;
    private $paymentMethod;
    private $payerId;
    private $receiverId;
    private $additionalData;
    private $paymentAmount;
    private $externalRedirectLink;
    private $attribute;
    private $attributeId;
    private $paymentPlatform;

    public function __construct($hook, $currencyCode, $paymentMethod, $paymentPlatform, $payerId = null, $receiverId = null, $additionalData = [], $paymentAmount = 0, $externalRedirectLink = null, $attribute = null, $attributeId = null)
    {
        $this->hook = $hook;
        $this->currencyCode = $currencyCode;
        $this->paymentMethod = $paymentMethod;
        $this->payerId = $payerId;
        $this->receiverId = $receiverId;
        $this->additionalData = $additionalData;
        $this->paymentAmount = $paymentAmount;
        $this->externalRedirectLink = $externalRedirectLink;
        $this->attribute = $attribute;
        $this->attributeId = $attributeId;
        $this->paymentPlatform = $paymentPlatform;
    }

    public function getHook()
    {
        return $this->hook;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function getPayerId()
    {
        return $this->payerId;
    }

    public function getReceiverId()
    {
        return $this->receiverId;
    }

    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    public function getExternalRedirectLink()
    {
        return $this->externalRedirectLink;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function getAttributeId()
    {
        return $this->attributeId;
    }

    public function getPaymentPlatForm()
    {
        return $this->paymentPlatform;
    }
}
