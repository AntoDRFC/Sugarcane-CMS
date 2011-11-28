<?php

/**
 * Editable Regions Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class Admin_EditableregionsController extends Sugarcane_Controllers_Base
{
    public function preDispatch() {
        $this->view->currentpage = 'editableregions';
        $this->view->page_title = 'Editable Regions';
    }
    
    public function indexAction() {
        $editableregions = $this->adminMapper->getEditableRegions();
        $this->view->editableregions = $editableregions;
        
        $this->view->js[] = '/admin/js/admin.js';
        
        $this->view->contentView = '/admin/editableregions/list.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function editcontentAction() {
        $regionId  = $this->req->getParam('region');
        $this->view->regionId = $regionId;
        
        $region = $this->dbMapper->getEditableRegion($regionId);
        
        if($regionId && !$region) {
            throw new Exception('Error: No region with the specified id was found.');
        } else {
            $this->view->region = $region;
        }
        
        $this->view->js[] = '/admin/js/lib/jquery-ui-1.8.1.custom.min.js';
        $this->view->js[] = '/admin/js/ckeditor/ckeditor.js';
        $this->view->js[] = '/admin/js/AjexFileManager/ajex.js';
        $this->view->js[] = '/admin/js/admin.js';
        
        $this->view->css[] = '/admin/css/black-tie/jquery-ui-1.8.1.custom.css';
        
        $this->view->contentView = '/admin/editableregions/editcontent.phtml';
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