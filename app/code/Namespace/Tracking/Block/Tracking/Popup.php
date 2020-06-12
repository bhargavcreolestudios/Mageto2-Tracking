<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Namespace\Tracking\Block\Tracking;

use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

/**
 * @api
 * @since 100.0.2
 */
class Popup extends \Magento\Shipping\Block\Tracking\Popup
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    protected $jsonHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context,$registry,$dateTimeFormatter, $data);
    }

    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        /* @var $info \Magento\Shipping\Model\Info */
        $info = $this->_registry->registry('current_shipping_info');

        return $info->getTrackingInfo();
    }

    /**
     * Format given date and time in current locale without changing timezone
     *
     * @param string $date
     * @param string $time
     * @return string
     */
    public function formatDeliveryDateTime($date, $time)
    {
        return $this->formatDeliveryDate($date) . ' ' . $this->formatDeliveryTime($time);
    }

    /**
     * Format given date in current locale without changing timezone
     *
     * @param string $date
     * @return string
     */
    public function formatDeliveryDate($date)
    {
        $format = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
        return $this->dateTimeFormatter->formatObject($this->_localeDate->date(new \DateTime($date)), $format);
    }

    /**
     * Format given time [+ date] in current locale without changing timezone
     *
     * @param string $time
     * @param string $date
     * @return string
     */
    public function formatDeliveryTime($time, $date = null)
    {
        if (!empty($date)) {
            $time = $date . ' ' . $time;
        }

        $format = $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT);
        return $this->dateTimeFormatter->formatObject($this->_localeDate->date(new \DateTime($time)), $format);
    }

    /**
     * Is 'contact us' option enabled?
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getContactUsEnabled()
    {
        return (bool)$this->_scopeConfig->getValue(
            'contacts/contacts/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getStoreSupportEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_support/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getContactUs()
    {
        return $this->getUrl('contact');
    }

    public function track($trackingNumber) {
        $timestamp = time();
        $appID = XXXX;
        $key = 'XXXXXXXXX';
        $secret = 'XXXXXXXXXXXXXXXXXXXXXXXXXXX';
    
        $sign = "key:". $key ."id:". $appID. ":timestamp:". $timestamp;
        $authtoken = rawurlencode(base64_encode(hash_hmac('sha256', $sign, $secret, true)));
        $ch = curl_init();
    
        $header = array(
            "x-appid: $appID",
            "x-timestamp: $timestamp",
            "x-sellerid:17080",
            "x-version:3", // for auth version 3.0 only
            "Authorization: $authtoken"
        );
    
        curl_setopt($ch, CURLOPT_URL, 'https://api.test.com/track/'.$trackingNumber);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $shipmentData = $this->decodeSomething($server_output);
        curl_close($ch);
        return $shipmentData;
    }

    public function decodeSomething($jsonData)
    {
        return $this->jsonHelper->jsonDecode($jsonData);
    }
}
