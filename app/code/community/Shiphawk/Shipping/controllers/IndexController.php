<?php
class Shiphawk_Shipping_IndexController extends Mage_Core_Controller_Front_Action
{
    public function trackingAction() {

        //  var_dump (file_get_contents('php://input'));
        $api_key_from_url = $this->getRequest()->getParam('api_key');
        $data_from_shiphawk = json_decode(file_get_contents('php://input'));
        $api_key = Mage::helper('shiphawk_shipping')->getApiKey();

        //curl -X POST -H Content-Type:application/json -d '{"event":"shipment.status_update","status":"in_transit","updated_at":"2015-01-14T10:43:16.702-08:00","shipment_id":1010226}' http://shiphawk.devigor.wdgtest.com/index.php/shiphawk/index/tracking?api_key=3331b35952ec7d99338a1cc5c496b55c

        if($api_key_from_url == $api_key) {
            try {
            $track_number = $data_from_shiphawk->shipment_id;
            $shipment_track = Mage::getResourceModel('sales/order_shipment_track_collection')->addAttributeToFilter('track_number', $track_number)->getFirstItem();
            $shipment = Mage::getModel('sales/order_shipment')->load($shipment_track->getParentId());

            $data_from_shiphawk = (array) $data_from_shiphawk;

            $shipment_status_updates = Mage::getStoreConfig('carriers/shiphawk_shipping/shipment_status_updates');
            $updates_tracking_url =    Mage::getStoreConfig('carriers/shiphawk_shipping/updates_tracking_url');
            $comment = '';

                if($data_from_shiphawk['event'] == 'shipment.status_update') {
                    $comment = $data_from_shiphawk['updated_at'] . ': Status has been changed to ' . $data_from_shiphawk['status'];
                    $shipment->addComment($comment);
                    if($shipment_status_updates) {
                        $shipment->sendUpdateEmail(true, $comment);
                    }
                }

                if($data_from_shiphawk['event'] == 'shipment.tracking_update') {
                    $comment = $data_from_shiphawk['updated_at'] . 'There is a tracking number available for your shipment - ' . $data_from_shiphawk['tracking_number'] . '<a href="' . $data_from_shiphawk['tracking_url'] . '" target="_blank">Click here to track.</a>';
                    $shipment->addComment($comment);
                    if($updates_tracking_url) {
                        $shipment->sendUpdateEmail(true, $comment);
                    }
                }

            //$shipment->addComment(implode(',', $data_from_shiphawk));

            $shipment->save();
            }catch (Mage_Core_Exception $e) {
                Mage::logException($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e->getMessage());
            }

        }
    }

    /* suggest items type in product page */
    public function searchAction() {

        $search_tag = trim(strip_tags($this->getRequest()->getPost('search_tag')));

        $api_key = Mage::helper('shiphawk_shipping')->getApiKey();
        $api_url = Mage::helper('shiphawk_shipping')->getApiUrl();

        $url_api = $api_url . 'items/search/'.$search_tag.'?api_key='.$api_key;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url_api,
            CURLOPT_POST => false
        ));

        $resp = curl_exec($curl);
        $arr_res = json_decode($resp);
        $responce_array = array();
        $responce = array();

        if(($arr_res->error) || ($arr_res['error'])) {

            Mage::log($arr_res->error, null, 'ShipHawk.log');
            $responce_html = '';
            $responce['shiphawk_error'] = $arr_res->error;
        }else{
            foreach ((array) $arr_res as $el) {
                $responce_array[$el->id] = $el->name.' ('.$el->category.')';
            }

            $responce_html="<ul>";

            foreach($responce_array as $key=>$value) {
                $responce_html .='<li class="type_link" id='.$key.' onclick="setItemid(this)" >'.$value.'</li>';
            }

            $responce_html .="</ul>";
        }
        $responce['responce_html'] = $responce_html;
        curl_close($curl);

        $this->getResponse()->setBody( json_encode($responce) );
    }
}
