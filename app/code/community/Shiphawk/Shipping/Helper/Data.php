<?php

class Shiphawk_Shipping_Helper_Data extends
    Mage_Core_Helper_Abstract
{
    /**
     * Get api key
     *
     * @return mixed
     */
    public function getApiKey()
    {
        return Mage::getStoreConfig('carriers/shiphawk_shipping/api_key');
    }

    /**
     * Get callback url for shipments
     *
     * @return mixed
     */
    public function getCallbackUrl($api_key)
    {
        return Mage::getUrl('shiphawk/index/tracking', array('api_key' => $api_key));
    }

    public function getRateFilter()
    {
        return Mage::getStoreConfig('carriers/shiphawk_shipping/rate_filter');
    }

    /**
     * Get api url
     *
     * @return mixed
     */
    public function getApiUrl()
    {
        //return 'https://sandbox.shiphawk.com/api/v1/';
        return Mage::getStoreConfig('carriers/shiphawk_shipping/gateway_url');
    }

    /**
     * Get Shiphawk attributes codes
     *
     * @return array
     */
    public function getAttributes()
    {
        $shiphawk_attributes = array('shiphawk_length','shiphawk_width', 'shiphawk_height', 'shiphawk_origin_zipcode', 'shiphawk_origin_firstname', 'shiphawk_origin_lastname'
        ,'shiphawk_origin_addressline1','shiphawk_origin_phonenum','shiphawk_origin_city','shiphawk_origin_state','shiphawk_type_of_product','shiphawk_type_of_product_value'
        ,'shiphawk_quantity', 'shiphawk_item_value','shiphawk_item_is_packed','shiphawk_origin_location');

        return $shiphawk_attributes;
    }

    public function isShipHawkShipping($shipping_code) {
        $result = strpos($shipping_code, 'shiphawk_shipping');
        return $result;
    }

    public function getSipHawkCode($shiphawk_book_id, $shipping_description) {
        $result = array();
        foreach ($shiphawk_book_id as $rate_id=>$method_data) {
            if( strpos($shipping_description, $method_data['name']) !== false ) {
                $result = array($rate_id => $method_data);
                return $result;
            }
        }
        return null;
    }


}