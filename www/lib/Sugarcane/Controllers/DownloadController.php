<?php

/**
 * Download Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class DownloadController extends Sugarcane_Controllers_Base {
    
    public function indexAction() {
        
        $code = $this->req->getParam('code');
        if($code) {
            $this->view->page_title = 'Download';
            $purchaseInfo         = $this->dbMapper->getPurchaseInfoByCode($code);
            $downloadableReleases = $this->dbMapper->getDownloadableItems($code, 'release');
            $downloadableTracks   = $this->dbMapper->getDownloadableItems($code, 'track');
            
            $this->view->purchaseInfo         = $purchaseInfo;
            $this->view->downloadableReleases = $downloadableReleases;
            $this->view->downloadableTracks   = $downloadableTracks;
            
//            var_dump($downloadableItems);
            $this->view->contentView = '/download/listdownloads.phtml';
        } else {
            $this->view->page_title = 'Download';
            $this->view->css[] = '/css/contact.css';
            $this->view->contentView = '/download/index.phtml';
        }
        
        $this->view->metaTitle = 'Circuit Records - Download';
        
        $this->renderView();
    }
    
    public function downloadfileAction() {
        $filename = $this->req->getParam('filename');
        
        $file = $this->config->paths->base . DIRECTORY_SEPARATOR . 'downloads' . DIRECTORY_SEPARATOR . 'tracks' . DIRECTORY_SEPARATOR . $filename;
        
        header("Content-Description: File Transfer");
        header("Content-Transfer-Encoding: binary");
        header("Content-disposition: attachment; filename= ".$filename);
        header('Content-type: audio/mp3');
        readfile($file);
    }
    
    public function freeAction() {
        $track_id = $this->req->getParam('track');
        $track = $this->dbMapper->getTrack($track_id);
        $this->view->track = $track;
        
        // check the price is free
        if($track['price'] > 0) {
            throw new Exception('That track is not free');
        }
        
        $this->view->page_title = 'Free Download';
        $this->view->css[] = '/css/contact.css';
        $this->view->contentView = '/download/freedownloadform.phtml';
        $this->renderView();
    }
    
    public function savedetailsAction() {
        $firstname = htmlentities($this->req->getParam('firstname', null), ENT_QUOTES, 'utf-8');
        $lastname  = htmlentities($this->req->getParam('lastname', null), ENT_QUOTES, 'utf-8');
        $postcode  = htmlentities($this->req->getParam('postcode', null), ENT_QUOTES, 'utf-8');
        $country   = htmlentities($this->req->getParam('country', null), ENT_QUOTES, 'utf-8');
        $email     = htmlentities($this->req->getParam('email', null), ENT_QUOTES, 'utf-8');
        
        $country = ($country) ? $country : 'United Kingdom';        
        
        // which files are required
        $required_fields = array('firstname' => 'Please enter your first name',
                                 'lastname'  => 'Please enter your surname',
                                 'postcode'  => 'Please enter your postcode',
                                 'email'     => 'Please enter your email');
        
        // check the required fields have been filled in
        $errors = array();
        foreach($required_fields as $required_field=>$error) {
            if($$required_field == '') {
                $errors[] = $error;
            }
        }
        
        if(!count($errors)) {
            $track_id = $this->req->getParam('track_id', null);
            $track = $this->dbMapper->getTrack($track_id);
            $this->view->track = $track;

            // recheck the price is free incase somebody dicked around with the hidden input
            if($track['price'] > 0) {
                throw new Exception('That track is not free');
            }
            
            $payment_info['first_name']     = $firstname;
            $payment_info['last_name']      = $lastname;
            $payment_info['postcode']       = $postcode;
            $payment_info['country']        = $country;
            $payment_info['email']          = $email;
            $payment_info['transaction_id'] = 'Free Track';
            $payment_info['payment_date']   = date('d-m-Y');
            $payment_info['payment_status'] = 'Completed';
            
            // save the customer details
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
            
            // fine to proceed
            $toSave['purchase_id']   = $purchase_id;
            $toSave['download_code'] = $code;
            $toSave['download_type'] = 'track';
            $toSave['link_id']       = $track_id;
            
            $this->dbMapper->saveRecord($toSave, 'download_codes', 'code_id');
            
            // all done, now send an email to the buyer with their download code
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
            $mail->addTo($email);
            $mail->send();
            
            $this->_redirect('/view/paypal-thank-you');
        } else {
            $this->session->formdata['data']   = $this->req->getParams();
            $this->session->formdata['errors'] = $errors;
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
}