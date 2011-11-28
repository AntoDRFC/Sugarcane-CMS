<?php

/**
 * News Team Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class Admin_TeamController extends Sugarcane_Controllers_Base
{    
    public function preDispatch()
    {
    }
    
    public function indexAction()
    {
        $team = $this->dbMapper->getTeamMembers();
        $this->view->team = $team;
        
        $this->view->js[] = '/admin/js/admin.js';
        
        $this->view->contentView = '/admin/team/index.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function editAction()
    {
        $teammember_id  = $this->req->getParam('member');
        
        $this->view->teammember_id = $teammember_id;
        $teammember = $this->adminMapper->getTeamMember($teammember_id);
        
        if($teammember_id && !$teammember) {
            throw new Exception('Error: No member with the specified id was found');
        } else {
            $this->view->teammember = $teammember;
        }
        
        $this->view->js[] = '/admin/js/admin.js';
        
        $this->view->contentView = '/admin/team/edit.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function saveteammemberAction()
    {
        $save['teammember_id'] = $this->req->getPost('teammember_id');
        $save['position']      = $this->req->getPost('position');
        $save['first_name']    = $this->req->getPost('first_name');
        $save['surname']       = $this->req->getPost('surname');
        
        if($this->dbMapper->saveRecord($save, 'team_members', 'teammember_id')) {
            $this->_redirect('/admin/team/');
        } else {
            throw new Exception('Failed to save team member');
        }
    }
    
    public function deleteAction()
    {
        $teammember_id = $this->req->getParam('member');

        if($this->dbMapper->deleteRecord('team_members', 'teammember_id', $teammember_id)) {
            $this->_redirect('/admin/team/');
        } else {
            throw new Exception('Failed to delete team member, please go back and try again.');
        }
    }
    
}