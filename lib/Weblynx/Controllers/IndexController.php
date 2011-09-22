<?php

/**
 * Index Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version
 */

class IndexController extends Weblynx_Controllers_Base {
    
    public function preDispatch() {
        $this->view->page = 'home';
    }
    
    public function indexAction() {
        $this->renderpageAction();
        //$this->_redirect('/view/index/');
    }
    
    public function renderpageAction() {
        $currentpage = $this->req->getParam('currentpage', 'index');
        
        $page_content = $this->dbMapper->getPageByPermalink($currentpage);
        if(!empty($page_content)) {
            $this->view->page_title = $page_content['page_title'];
            $this->view->content    = $page_content['page_content'];
            
            // time for the pages meta-data
            $meta = $this->dbMapper->getPagesMetaData($page_content['page_id']);
            $this->view->metaTitle       = $meta['meta_title'];
            $this->view->metaKeywords    = $meta['meta_keywords'];
            $this->view->metaDescription = $meta['meta_description'];
            
            // if this is a subpage, we need to get all the other subpages
            $subpage = ($page_content['parent'] == 0) ? $page_content['page_id'] : $page_content['parent'];
            
            // lets see if there are any subpages
            $subpages = $this->dbMapper->getPagesByParentId($subpage);
            $this->view->subpages = $subpages;
        } else {
            throw new Exception(404);
        }
        
        // Start of custom function calls set by permalink
        if($currentpage == 'index') {
            $latestposts = $this->getLatestBlogPosts(2);
            $this->view->latestposts = $latestposts;
            
            $newsitems = $this->getLatestNewsItems(2);
            $this->view->newsitems = $newsitems;
        }
        // End of custom function calls set by permalink
        
        $this->view->css[] = '/css/index.css';

        $this->renderView($page_content['template']);
    }
    
    /**
     * Get me x amount of latest releases
     */
    protected function getLatestBlogPosts($numberOfItems) {
        $posts = $this->dbMapper->getLatestBlogPosts($numberOfItems);
        
        if(count($posts)) {
            return $posts;
        } else {
            return array();
        }
    }
    
    /**
     * Get me x amount of latest news items
     */
    protected function getLatestNewsItems($numberOfItems) {
        $news = $this->dbMapper->getLatestNewsItems($numberOfItems);
        
        if(count($news)) {
            return $news;
        } else {
            return array();
        }
    }
    
}