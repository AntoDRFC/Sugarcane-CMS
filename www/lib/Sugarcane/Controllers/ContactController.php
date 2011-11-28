<?php

/**
 * Contact Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class ContactController extends Sugarcane_Controllers_Base {
    
    public function senddetailsAction() {
        $name       = htmlentities($this->req->getParam('name', null), ENT_QUOTES, 'utf-8');
        $email      = htmlentities($this->req->getParam('email', null), ENT_QUOTES, 'utf-8');
        $mobile     = htmlentities($this->req->getParam('mobile', null), ENT_QUOTES, 'utf-8');
        $enquiry    = htmlentities($this->req->getParam('enquiry', null), ENT_QUOTES, 'utf-8');
        
        // which files are required
        $required_fields = array('name'      => 'Please enter your name',
                                 'email'     => 'Please enter your email address',
                                 'enquiry'   => 'Please enter a message',);
        
        // check the required fields have been filled in
        $errors = array();
        foreach($required_fields as $required_field=>$error) {
            if($$required_field == '') {
                $errors[] = $error;
            }
        }
        
        if(!count($errors)) {
            $email_enquiry = ($enquiry) ? sprintf('<p>Message:<br />%s</p>', $enquiry) : '';
            
            $customfields = '';
            
            $tr = new Zend_Mail_Transport_Sendmail('-f' . $this->config->settings->email->from);
            Zend_Mail::setDefaultTransport($tr);
            
            $htmlmessage  = sprintf('<p>The following enquiry has been submitted from the %s website:,</p>
                                     <p>Name: %s<br />
                                     Email: %s<br />
                                     Mobile: %s</p>
                                     %s', $this->config->settings->email->company, $name, $email, $mobile, $email_enquiry);
            
            $mail = new Zend_Mail();
            $mail->setSubject($this->config->settings->email->subject);
            $mail->setBodyHtml($htmlmessage);
            $mail->setFrom($this->config->settings->email->from);
            $mail->addTo($this->config->settings->email->to);
            $mail->send();
            
            $this->_redirect('/view/thankscontactus/');
        } else {
            $this->session->formdata['data']   = $this->req->getParams();
            $this->session->formdata['errors'] = $errors;
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
}