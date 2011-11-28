<?php

/**
 * Admin Header Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class Admin_HeaderController extends Sugarcane_Controllers_Base
{
    public function preDispatch()
    {
        $loggedIn = isset($_SESSION['loggedin']) ? true : false;
        
        if(!$loggedIn) {
            header('Location: /admin/login/');
        }
    }
    
    public function indexAction()
    {
        $headers = $this->dbMapper->getPageHeaders();
        $this->view->headers = $headers;
        
        if(!empty($_SESSION['message'])) {
            $this->view->message = $_SESSION['message'];
            unset($_SESSION['message']);
        }
                
        $this->view->contentView = '/admin/header/index.phtml';
        $this->renderView('admin.phtml');
    }
        
    public function saveAction() {
        $this->adminMapper->purgeHeaders();
        
        $upload = new Zend_File_Transfer_Adapter_Http();
        $dest_dir = $this->config->paths->base . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'header';
        
        if(!file_exists($dest_dir)){
            if(!mkdir($dest_dir)){
                throw new Exception("Could not create upload folder for the header images to go into.");
            }
        }
        $upload->setDestination($dest_dir);

        $allowed_extensions = 'jpg,jpeg,bmp,gif,png,tiff';
        $upload->addValidator('Extension', false, array('extension' => $allowed_extensions, 'messages' => array(Zend_Validate_File_Extension::FALSE_EXTENSION => 'Invalid extension for file %value%')));

        $files = $upload->getFileInfo();
        
        $headerCaptions = $this->req->getParam('header_caption');
        $currentImages  = $this->req->getParam('current_image');
        
        for($i=0;$i<count($headerCaptions); $i++) {
            $save['caption']  = $headerCaptions[$i];
            $save['ordering'] = $i+1;
            
            if(empty($files['header_picture_'.$i.'_']['name'])) {
                $save['picture'] = $currentImages[$i];
            } else {
                $file = $files['header_picture_'.$i.'_']['name'];
                
                if($upload->isUploaded($file) && $upload->isValid($file)) {
                    $upload->receive($file);
                    $save['picture'] = $file;
                } else {
                    throw new Exception('Error Reading Uploaded File.');
                }
            }
            
            $this->dbMapper->saveRecord($save, 'page_headers', 'header_id');
        }
        
        $_SESSION['message'] = 'Header images saved';
        
        $this->_redirect('/admin/header/');
    }
}