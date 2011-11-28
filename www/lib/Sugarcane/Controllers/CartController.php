<?php

/**
 * Cart Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class CartController extends Sugarcane_Controllers_Base {
    
    public function preDispatch() {
        $this->view->css[] = '/css/cart.css';
        
        $this->view->metaTitle = 'Circuit Records - My Basket';
    }
    
    public function indexAction() {
        if(isset($_SESSION['basket'])) {
            $this->view->basket = $_SESSION['basket'];
        } else {
            $this->view->basket = array();
        }
        
        $this->view->page_title = 'My Basket';
        
        $this->view->css[]       = '/css/contact.css';
        $this->view->contentView = '/cart/index.phtml';
        $this->renderView();
    }
    
    public function addtocartAction() {
        $track   = (int) $this->req->getParam('track', null);
        $release = (int) $this->req->getParam('release', null);
        
        if($track) {
            $trackInfo = $this->dbMapper->getTrack($track);
            
            $_SESSION['basket']['tracks'][$track] = $trackInfo;
        }
        
        if($release) {
            $releaseInfo = $this->dbMapper->getReleaseById($release);
            
            $_SESSION['basket']['releases'][$release] = $releaseInfo;
        }
        
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function removefromcartAction() {
        $track   = (int) $this->req->getParam('track', null);
        $release = (int) $this->req->getParam('release', null);
        
        if($track) {
            unset($_SESSION['basket']['tracks'][$track]);
        }
        
        if($release) {
            unset($_SESSION['basket']['releases'][$release]);
        }
        
        $this->_redirect('/cart/');
    }
    
}