<?php

/**
 * Applications Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class Admin_ApplicationsController extends Sugarcane_Controllers_Base
{
    public function preDispatch() {
        $this->view->currentpage = 'applications';
        $this->view->page_title = 'Form Approvals';
    }
    
    public function indexAction() {
        $initialEnquiries = $this->adminMapper->getInitialEnquiries();
        $this->view->initialEnquiries = $initialEnquiries;
        
        $this->view->js[] = '/admin/js/admin.js';
        
        $this->view->contentView = '/admin/applications/list.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function viewformAction() {
        $formId  = $this->req->getParam('form_id');
        $this->view->formId = $formId;
        
        $applicationForm = $this->dbMapper->locateApplicationFormById($formId, 'N');
        
        if($formId && !$applicationForm) {
            throw new Exception('Error: No form with the specified id was found.');
        } else {
            $this->view->applicationForm = $applicationForm;
        }
        
        $this->view->js[] = '/admin/js/lib/jquery-ui-1.8.1.custom.min.js';
        $this->view->js[] = '/admin/js/ckeditor/ckeditor.js';
        $this->view->js[] = '/admin/js/AjexFileManager/ajex.js';
        $this->view->js[] = '/admin/js/admin.js';
        
        $this->view->css[] = '/admin/css/black-tie/jquery-ui-1.8.1.custom.css';
        $this->view->css[] = '/css/pagebuilder.css';
        
        $this->view->contentView = '/admin/applications/viewform.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function savecontentAction() {
        $save['content_id'] = $this->req->getPost('content_id');
        $save['content']    = $this->req->getPost('content');
        
        if($this->dbMapper->saveRecord($save, 'editable_regions', 'content_id')) {
            $this->_redirect('/admin/editableregions');
        } else {
            throw new Exception('Failed to save editable region');
        }
    }    
}