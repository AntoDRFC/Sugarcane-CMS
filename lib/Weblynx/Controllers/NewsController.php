<?php

/**
 * News Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class NewsController extends Weblynx_Controllers_Base {
    
    public function preDispatch() {
        $this->view->currentpage = 'news';
        $this->view->page_title = 'News';
        $this->view->metaTitle = 'Key Fund - News';
        $this->view->css[] = '/css/news.css';
    }
    
    public function indexAction() {
        $newsitems = $this->dbMapper->getLatestNewsItems(10);
        $this->view->newsitems = $newsitems;
        
        $this->view->contentView = '/news/list.phtml';
        $this->renderView();
    }
    
    public function viewAction() {
        $news_id  = $this->req->getParam('article');
        $newsitem = $this->dbMapper->getNewsItem($news_id);
        
        if(!$news_id || !$newsitem) {
            throw new Exception('No news item found');
        } else {
            $this->view->location = $this->config->paths->base;
            $this->view->newsitem = $newsitem;
        }
        
        $this->view->contentView = '/news/view.phtml';
        $this->renderView();
    }
    
}