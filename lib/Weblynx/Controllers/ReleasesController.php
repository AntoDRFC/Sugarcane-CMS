<?php

/**
 * Releases Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class ReleasesController extends Weblynx_Controllers_Base {
    
    public function preDispatch() {
        $this->view->currentpage = 'releases';
        $this->view->page_title = 'Releases';
        $this->view->metaTitle = 'Circuit Records - Releases';
        $this->view->css[] = '/css/releases.css';
    }
    

    public function indexAction() {
        $releases = $this->dbMapper->getReleases();
        $this->view->releases = $releases;
        
        $this->view->contentView = '/releases/list.phtml';
        $this->renderView();
    }

    
    public function viewAction() {
        $release_id = $this->req->getParam('release');
        $release    = $this->dbMapper->getReleaseById($release_id);
                
        if(!$release_id || !$release) {
            throw new Exception('No release found.');
        } else {
            $this->view->location = $this->config->paths->base;
            $this->view->release   = $release;
        }
        
        // get the tracks for this release
        $tracks = $this->dbMapper->getTracks($release_id);
        $this->view->tracks = $tracks;
        
        $this->view->css[] = '/css/jplayer.blue.monday.css';
        
        $this->view->headJs[] = '/js/jquery-1.4.4.js';
        $this->view->js[] = '/js/jquery.jplayer.min.js';
        $this->view->js[] = '/js/jquery-ui-1.8.12.custom.min.js';
        $this->view->js[] = '/js/releases.js';
        
        $this->view->css[] = '/css/redmond/jquery-ui-1.8.12.custom.css';
        
        $this->view->contentView = '/releases/view.phtml';
        $this->renderView();
    }
    
    public function postnewsletterAction() {
        // URL of Form
        $url = "http://www.sugarcanenews.co.uk/box.php";
        
        $email = htmlentities($this->req->getPost('email'));
        $name  = htmlentities($this->req->getPost('name'));
        
        //create the final string to be posted
        $post_string = sprintf("p=9&nlbox[1]=4&funcml=add&email=%s&name=%s", $email, $name);
        
        //create cURL connection
        $curl_connection = curl_init($url);
        
        //set options
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl_connection, CURLOPT_POST, 1);
        
        //set data to be posted
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
        
        //perform our request
        $result = curl_exec($curl_connection);
        
        //show information regarding the request
        print_r(curl_getinfo($curl_connection));
        //echo curl_errno($curl_connection) . '-' . 
        //                curl_error($curl_connection);
        
        //close the connection
        curl_close($curl_connection);
    }
    
}