<?php

/**
 * Artists Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class ArtistsController extends Weblynx_Controllers_Base {
    
    public function preDispatch() {
        $this->view->currentpage = 'artists';
        $this->view->page_title = 'Artists';
        $this->view->metaTitle = 'Circuit Records - Artists';
        $this->view->css[] = '/css/artists.css';
    }
    
    public function indexAction() {
        $artists = $this->dbMapper->getArtists();
        $this->view->artists = $artists;
        
        $this->view->contentView = '/artists/list.phtml';
        $this->renderView();
    }
    
    public function viewAction() {
        $artist_id = $this->req->getParam('artist');
        $artist    = $this->dbMapper->getArtist($artist_id);
                
        if(!$artist_id || !$artist) {
            throw new Exception('No artist found.');
        } else {
            $this->view->location = $this->config->paths->base;
            $this->view->artist   = $artist;
        }
        
        // get the releases for this artist
        $releases = $this->dbMapper->getReleasesByArtist($artist_id);
        $this->view->releases = $releases;
        
        $this->view->contentView = '/artists/view.phtml';
        $this->renderView();
    }
    
}