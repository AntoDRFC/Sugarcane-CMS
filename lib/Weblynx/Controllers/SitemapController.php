<?php

/**
 * Sitemap Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class SitemapController extends Weblynx_Controllers_Base {
    
    public function preDispatch() {
        $this->view->currentpage = 'sitemap';
        $this->view->page_title = 'Sitemap';
        $this->view->metaTitle = 'Circuit Records - Sitemap';
        
        $this->view->siteurl = $this->config->urls->siteurl;
        
        $pages = $this->dbMapper->getPages(0);
        $this->view->pages = $pages;
        
        $artists = $this->dbMapper->getArtists();
        $this->view->artists = $artists;
        
        $releases = $this->dbMapper->getReleases();
        $this->view->releases = $releases;
    }
    public function indexAction() {
        $this->view->contentView = '/sitemap/index.phtml';
        $this->renderView();
    }
    
    public function xmlAction() {
        $xml = $this->view->render('/sitemap/xml.phtml');
        echo $xml;
    }
    
}