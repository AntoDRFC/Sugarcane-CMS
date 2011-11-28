<?php

/**
 * Newsletter Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class NewsletterController extends Sugarcane_Controllers_Base {
    
    public function indexAction() {
        $this->view->metaTitle = 'Circuit Records - Newsletter';
        $this->view->page_title = 'Newsletter';
        
        $this->view->contentView = '/newsletter/signup.phtml';
        $this->renderView();
    }
    
}