<?php

/**
 * Admin Login Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class Admin_LoginController extends Sugarcane_Controllers_Base
{
    public function indexAction()
    {
        $this->view->hideNav     = true;
        $this->view->contentView = '/admin/login/index.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function dologinAction()
    {
        $user = htmlentities($this->req->getParam('sc_username'));
        $pass = md5(htmlentities($this->req->getParam('sc_password')));
        
        $user = $this->adminMapper->getUserByCredentials($user, $pass);

        if($user) {
            $_SESSION['user']     = $user;
            $_SESSION['loggedin'] = true;
            header('Location: /admin/index/');
            exit;
        } else {
            $_SESSION['loginfail'] = 'Login failed, username or password incorrect';
            header('Location: /admin/login/');
        }
    }
    
    public function logoutAction()
    {
        session_destroy();
        header('Location: /admin/login');
        exit;
    }
}