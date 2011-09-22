<?php

/**
 * Paypal Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class PaypalController extends Weblynx_Controllers_Base {
    
    public function indexAction() {
        $save['request'] = serialize($_POST);
        
        $payment_info['first_name']     = $this->req->getPost('first_name');
        $payment_info['last_name']      = $this->req->getPost('last_name');
        $payment_info['email']          = $this->req->getPost('payer_email');
        $payment_info['transaction_id'] = $this->req->getPost('txn_id');
        $payment_info['payment_date']   = $this->req->getPost('payment_date');
        $payment_info['payment_status'] = $this->req->getPost('payment_status');
        
        // build up the address
        $street = $this->req->getPost('address_street');
        $city   = $this->req->getPost('address_city');
        $state  = $this->req->getPost('address_state');
        $payment_info['address']  = $street . '<br>' . $city . '<br>' . $state;
        $payment_info['postcode'] = $this->req->getPost('address_zip');
        $payment_info['country']  = $this->req->getPost('address_country');
        
        $this->dbMapper->saveRecord($save, 'paypal', 'request_id');
        $purchase_id = $this->dbMapper->saveRecord($payment_info, 'purchases', 'purchase_id');
        
        // make a random download code
        $unique = 0;
        while($unique == 0) {
            $code = substr(md5(time()), 0, 8);
            
            // check code is unique
            $downloadableItems = $this->dbMapper->isCodeUnique($code);
            
            // if unique set the unique variable to true
            if(!$downloadableItems['codes_found']) {
                $unique = true;
            }
        }
        
        // get the number of items
        $items = $this->req->getPost('num_cart_items');
        
        for($i=1; $i<=$items; $i++) {
            $itemInfo = explode('_', $this->req->getPost('item_number' . $i));
            
            // check the values haven't been messed around with
            if($itemInfo[0] == 'REL') {
                $releaseInfo  = $this->dbMapper->getReleaseById($itemInfo[1]);
                $realPrice    = $releaseInfo['full_price'];
                $downloadType = 'release';
            } else {
                $trackInfo = $this->dbMapper->getTrack($itemInfo[1]);
                $realPrice    = $trackInfo['price'];
                $downloadType = 'track';
            }
            
            $pricePaid = $this->req->getPost('mc_gross_' . $i);
            if($pricePaid != $realPrice) {
                throw new Exception('FAILED! Some tampering has been going on');
            }
            
            // fine to proceed
            $toSave['purchase_id']   = $purchase_id;
            $toSave['download_code'] = $code;
            $toSave['download_type'] = $downloadType;
            $toSave['link_id']       = (int) $itemInfo[1];
            
            $this->dbMapper->saveRecord($toSave, 'download_codes', 'code_id');
        }
        
        // all done, now send an email to the buyer with their download code
        $emailCodeTo = $this->req->getParam('custom');
        
        $tr = new Zend_Mail_Transport_Sendmail('-f' . $this->config->settings->email->from);
        Zend_Mail::setDefaultTransport($tr);
        
        $htmlmessage  = sprintf('<p>Dear %s %s,</p>
                                 <p>Thank you for buying from Circuit Records. We and our artists appreciate it...</p>
                                 <p>This is your download code <strong>%s</strong></p>
                                 <p>To retrieve your music visit the <a href="%s/download/">DOWNLOAD</a> page on the Circuit Records site and input your code or alternatively <a href="%s/download/?code=%s">click here</a> to download now</p>
                                 <p>Thanks<br />
                                 The Circuit Records Team</p>', $payment_info['first_name'], $payment_info['last_name'], $code, $this->config->urls->siteurl, $this->config->urls->siteurl, $code);
        
        $mail = new Zend_Mail();
        $mail->setSubject('Circuit Records - Your download code');
        $mail->setBodyHtml($htmlmessage);
        $mail->setFrom($this->config->settings->email->from);
        $mail->addTo($emailCodeTo);
        $mail->send();
    }
    
    protected function whatthefuckAction() {
        echo 'Paypal Return:<br>';
        $string = 'a:42:{s:8:"mc_gross";s:4:"0.49";s:22:"protection_eligibility";s:10:"Ineligible";s:14:"address_status";s:11:"unconfirmed";s:12:"item_number1";s:6:"TRA_13";s:8:"payer_id";s:13:"S7YAQPN6KNQQ2";s:3:"tax";s:4:"0.00";s:14:"address_street";s:14:"1 Main Terrace";s:12:"payment_date";s:25:"14:13:15 May 16, 2011 PDT";s:14:"payment_status";s:7:"Pending";s:7:"charset";s:12:"windows-1252";s:11:"address_zip";s:7:"W12 4LQ";s:11:"mc_shipping";s:4:"0.00";s:11:"mc_handling";s:4:"0.00";s:10:"first_name";s:4:"Test";s:20:"address_country_code";s:2:"GB";s:12:"address_name";s:9:"Test User";s:14:"notify_version";s:3:"3.1";s:6:"custom";s:12:"anto@f2s.com";s:12:"payer_status";s:10:"unverified";s:15:"address_country";s:14:"United Kingdom";s:14:"num_cart_items";s:1:"1";s:12:"mc_handling1";s:4:"0.00";s:12:"address_city";s:13:"Wolverhampton";s:11:"verify_sign";s:56:"An5ns1Kso7MWUdW4ErQKJJJ4qi4-ARHAly0IaNdPE0RqacsoTac5DAb8";s:11:"payer_email";s:32:"buyer_1302860634_per@antodev.com";s:12:"mc_shipping1";s:4:"0.00";s:6:"txn_id";s:17:"8MJ08396G6654320G";s:12:"payment_type";s:7:"instant";s:9:"last_name";s:4:"User";s:13:"address_state";s:13:"West Midlands";s:10:"item_name1";s:30:"the beaus - cappuccino swagger";s:14:"receiver_email";s:27:"payments@higherrhythm.co.uk";s:9:"quantity1";s:1:"1";s:14:"pending_reason";s:10:"unilateral";s:8:"txn_type";s:4:"cart";s:10:"mc_gross_1";s:4:"0.49";s:11:"mc_currency";s:3:"GBP";s:17:"residence_country";s:2:"GB";s:8:"test_ipn";s:1:"1";s:19:"transaction_subject";s:12:"anto@f2s.com";s:13:"payment_gross";s:0:"";s:12:"ipn_track_id";s:22:"4rVBK5eDg.Z7Q-2hi9fWRQ";}';
        echo $string;
        echo '<br><br>';
        echo 'Decode:<br>';
        var_dump(unserialize($string));
    }
    
}