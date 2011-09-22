<?php

/**
 * PageBuilder Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version
 */

class PagebuilderController extends Weblynx_Controllers_Base {
    
    public function preDispatch() {
        $this->view->page    = 'pagebuilder';
        $this->view->navType = $this->config->settings->nav;
        
        $loggedIn = isset($_SESSION['loggedin']) ? true : false;
        
        if(!$loggedIn) {
            //header('Location: /admin/login.php');
        }
    }
    
    public function indexAction() {
        $pages = $this->dbMapper->getPages();
        
        // we need to re-order the pages into the right parent/subpage array
        $page_array = array();
        foreach($pages as $page) {
            if($page['parent'] == 0) {
                $page_array[$page['page_id']] = $page;
            } else {
                $page_array[$page['parent']]['subpages'][] = $page;
            }
        }
        
        $this->view->pages = $page_array;
        
        $this->view->js[] = '/js/jquery-1.4.2.min.js';
        $this->view->js[] = '/js/jquery-ui-1.8.4.custom.min.js';
        $this->view->js[] = '/js/pagebuilder.js';
        $this->view->js[] = '/js/tooltip.js';
        
        $this->view->css[]       = '/css/pagebuilder.css';
        $this->view->contentView = '/pagebuilder/index.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function createAction() {
        $page_id = (int) $this->req->getParam('page');
        
        $this->view->page_id = $page_id;
        if($page_id) {
            $page = $this->dbMapper->getPage($page_id);
            $parent = $page['parent'];
            
            $this->view->editpage = $page;
        } else {
            $parent = (int) $this->req->getParam('parent');
            if($parent == 1) {
                throw new Exception('You cannot add a subpage to the homepage');
            }
        }
        
        $type = $this->req->getParam('type');
        if($type == 'link') {
            $save_page['menu_text'] = $this->req->getParam('menu_text');
            $save_page['permalink'] = $this->req->getParam('url');
            $save_page['type']      = 'link';
            $save_page['parent']    = $parent;
            
            $lastpage = $this->dbMapper->getLastPage($save_page['parent']);
            $save_page['ordering'] = isset($lastpage['ordering']) ? $lastpage['ordering']+1 : 1;
            
            if($this->dbMapper->saveRecord($save_page, 'pages', 'page_id')) {
                $this->_redirect('/pagebuilder/');
            } else {
                throw new Exception('Failed to add link');
            }
        }
        
        $templates = explode(',', $this->config->settings->templates);
        $templateArray = array();
        foreach($templates as $template) {
            $template = explode(':', $template);
            $templateArray[$template[1]] = $template[0];
        }
        $this->view->templates = $templateArray;
        
        $this->view->parent = $parent;
        
        $this->view->css[] = '/css/pagebuilder.css';
        
        $this->view->js[] = '/js/ckeditor/ckeditor.js';
        $this->view->js[] = '/js/AjexFileManager/ajex.js';
        $this->view->js[] = '/js/pagebuilder.js';

        $this->view->contentView = '/pagebuilder/create.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function savepageAction() {
        $save_page['page_id']      = (int) $this->req->getParam('page_id');
        $save_page['page_title']   = htmlentities($this->req->getPost('page_title'));
        $save_page['menu_text']    = htmlentities($this->req->getPost('menu_text'));
        $save_page['page_content'] = $this->req->getPost('page_content');
        $save_page['permalink']    = htmlentities($this->req->getPost('permalink'));
        $save_page['parent']       = (int) $this->req->getParam('parent');
        
        $template = htmlentities($this->req->getPost('template'));
        if($template) {
            $save_page['template'] = $template;
        }
        
        if($save_page['page_id'] == 1) {
            $save_page['permalink'] = 'index';
        }
        
        // here we work out what order this page is
        if(!$save_page['page_id']) {
            $lastpage = $this->dbMapper->getLastPage($save_page['parent']);
            $save_page['ordering'] = isset($lastpage['ordering']) ? $lastpage['ordering']+1 : 1;
        }
                
        if($this->dbMapper->saveRecord($save_page, 'pages', 'page_id')) {
            $this->_redirect('/pagebuilder/');
        } else {
            throw new Exception('Failed to save page');
        }
    }
    
    public function publishAction() {
        $publish_page['page_id']   = $this->req->getParam('page');
        $publish_page['published'] = 1;
        
        if($this->dbMapper->saveRecord($publish_page, 'pages', 'page_id')) {
            $this->_redirect('/pagebuilder/');
        } else {
            throw new Exception('Failed to publish page');
        }
    }
    
    public function disableAction() {
        $disable_page['page_id']   = $this->req->getParam('page');
        $disable_page['published'] = 0;
        
        if($this->dbMapper->saveRecord($disable_page, 'pages', 'page_id')) {
            $this->_redirect('/pagebuilder/');
        } else {
            throw new Exception('Failed to disable page');
        }
    }
    
    public function deletepageAction() {
        $page_id = $this->req->getParam('page');

        if($page_id > 1) {
            if($this->dbMapper->deleteRecord('pages', 'page_id', $page_id)) {
                $this->dbMapper->deleteChildren('pages', 'parent', $page_id);
                $this->_redirect('/pagebuilder/');
            } else {
                throw new Exception('Failed to delete page, please go back and try again.');
            }
        } else {
            throw new Exception('You cannot delete the homepage of a website');
        }
    }
    
    public function savepageorderAction() {
        $pages = $this->req->getPost('pagedata');
        
        $parentOrder = 1;
        foreach($pages['parent'] as $parentpages) {
            echo sprintf('<p>item: %s, order: %d, parent: %d</p>', $parentpages['itemId'], $parentpages['parent'], $parentOrder);
            
            $this->dbMapper->updatePageOrder(str_replace('page_', '', $parentpages['itemId']), $parentOrder, $parentpages['parent']);
//            var_dump($parentpages);
            
            $parentOrder++;
        }
        
        unset($pages['parent']);
        
        foreach($pages as $subpages) {
            $subPageOrder = 1;
            foreach($subpages as $subpage) {
                echo sprintf('<p>item: %s, order: %d, parent: %d</p>', $subpage['itemId'], $subPageOrder, str_replace('page_', '', $subpage['parent']));
                
                $this->dbMapper->updatePageOrder(str_replace('page_', '', $subpage['itemId']), $subPageOrder, str_replace('page_', '', $subpage['parent']));
//                var_dump($subpages);
                $subPageOrder++;
            }
        }
    }
    
    public function previewpageAction() {
        $page_title = htmlentities($this->req->getPost('page_title'));
        $content    = $this->req->getPost('page_content');
        $parent     = (int) $this->req->getParam('parent');
        
        $save_page['menu_text'] = htmlentities($this->req->getPost('menu_text'));
        
        if($parent != 0) {
            $parentpage = $this->dbMapper->getPage($parent);
        }
        
        // time for the pages meta-data
        $meta = $this->dbMapper->getPagesMetaData();
        $this->view->metaTitle       = $meta['meta_title'];
        $this->view->metaKeywords    = $meta['meta_keywords'];
        $this->view->metaDescription = $meta['meta_description'];
        
        $this->view->js[]  = '/js/pagebuilder.js';
        $this->view->css[] = '/css/preview.css';
        
        $currentpage = !empty($parentpage['permalink']) ? $parentpage['permalink'] : '';
        $this->view->nav = $this->buildNavigation(true, $currentpage);
        
        $this->view->page_title = $page_title;
        $this->view->content    = $content;
        
        $this->renderView('preview.phtml');
    }
    
    public function tagsAction() {
        $page_id = (int) $this->req->getParam('page');
        
        $meta = $this->dbMapper->getPagesMetaData($page_id);
        $this->view->metadata = $meta;
        
        $this->view->meta_id = !empty($meta['meta_id']) ? $meta['meta_id'] : '';
        $this->view->page_id = $page_id;
        
        $this->view->css[] = '/css/pagebuilder.css';
        
        $this->view->contentView = '/pagebuilder/tags.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function savetagsAction() {
        $save['meta_id'] = (int) $this->req->getParam('meta_id', null);
        $save['page_id'] = (int) $this->req->getParam('page_id');
        
        $save['meta_title']       = htmlentities($this->req->getParam('meta_title'));
        $save['meta_keywords']    = htmlentities($this->req->getParam('meta_keywords'));
        $save['meta_description'] = htmlentities($this->req->getParam('meta_description'));
        
        if($this->dbMapper->saveRecord($save, 'metadata', 'meta_id')) {
            $this->_redirect('/pagebuilder/');
        } else {
            throw new Exception('Failed to save tags, please go back and try again');
        }
    }
    
    public function uploadimageAction() {
        header("content-type: text/html"); // the return type must be text/html
        
        $upload = new Zend_File_Transfer_Adapter_Http();
        $dest_dir = $this->config->paths->uploaddir;
        if(!file_exists($dest_dir)){
            if(!mkdir($dest_dir)){
                throw new Exception("Could not create upload folder for the uploaded pages images to go into.");
            }
        }
        $upload->setDestination($dest_dir);

        $allowed_extensions = '123,3g2,3gp,aac,aam,ac3,accdb,ai,aiff,ani,art,asf,ASM,asx,au,avi,blz,bmp,cdr,cmx,csv,cur,dcr,def,DIB,doc,docx,dot,dotx,dox,DRW,dsf,dwf,dwg,dwt,dxf,dxr,ea,emf,eml,eot,eps,ev,ev2,evy,FAX,fla,flac,flp,flv,FPX,FRM,FXR,ged,gif,gvi,HGL,hjt,hlp,HP,HPG,HPGL,hwp,IAM,ico,IDV,IDW,img,inc,IPT,isf,it,ivr,ivs,jfif,jif,jpe,jpeg,jpg,key,lbi,log,lwp,m4a,m4v,map,mbx,mdb,mdi,mid,midi,mod,mov,mp2,mp3,mp4,mpa,mpeg,mpg,mpp,msg,MSWMM,MWP,nsf,numbers,OBD,odf,odg,odp,ods,odt,ogg,opf,otg,otp,ots,ott,pages,PBM,pcd,pcl,pcx,pdf,pfr,PLT,ply,png,PNTG,pot,ppm,pps,ppt,pptx,prc,prs,PRT,ps,psd,psp,pub,qpw,qt,ra,ram,RLE,rm,rmf,rmvb,rmx,rnd,rp,rpm,RPT,rt,rtf,s3m,sam,sb,scm,sdc,sdd,sdw,SEP,SNP,spl,srs,stc,sti,stw,svg,svr,SVW,swa,swf,swv,sxc,SXC,sxd,sxg,sxi,SXM,SXP,sxw,tga,tgz,tif,tiff,tmb,tsp,txt,vac,VCF,VDA,viv,vivo,vob,vox,vsd,VST,wav,wax,wb1,wb2,wb3,wbmp,wdb,wdf,wk,wk1,wk2,wk3,wk4,wk5,wki,wks,wku,wm,wma,wmf,wmv,wmx,wmz,wp5,wp6,wp7,wpd,wpf,wpl,wps,wri,wrk,wrl,ws,wvx,wpp,XBM,xdm,XLC,xls,xlsm,xlsx,XLT,XLW,xpm,xyp,sib,zip,';
        $upload->addValidator('Extension', false, array('extension' => $allowed_extensions, 'messages' => array(Zend_Validate_File_Extension::FALSE_EXTENSION => 'Invalid extension for file %value%')));

        $files = $upload->getFileInfo();
        foreach ($files as $file => $info) {
            if($upload->isUploaded($info['name']) && $upload->isValid($info['name'])) {
                $upload->receive($info['name']);
                echo sprintf("{status:'UPLOADED', image_url:'%s/%s'}", $this->config->paths->uploadurl, $info['name']);
            } else {
                echo "{status:'Error Reading Uploaded File.'}";
            }
        }
    }
    
    public function defaultheaderAction() {
        $header = $this->dbMapper->getPagesHeader();
        $this->view->pageHeader = $header;
        
        $this->view->header_id = '';
        $this->view->page_id   = '';
        
        $this->view->extraHeaderText = ' Default ';
        $this->view->postUrl = '/pagebuilder/savedefaultheader/';
        
        $this->view->css[] = '/css/pagebuilder.css';
        
        $this->view->contentView = '/pagebuilder/header.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function headerAction() {
        $page_id = (int) $this->req->getParam('page');
        
        $header = $this->dbMapper->getPagesHeader($page_id);
        $this->view->pageHeader = $header;
        
        $this->view->header_id = !empty($header['header_id']) ? $header['header_id'] : '';
        $this->view->page_id = $page_id;
        
        $this->view->postUrl = '/pagebuilder/saveheader/';
        
        $this->view->css[] = '/css/pagebuilder.css';
        
        $this->view->contentView = '/pagebuilder/header.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function savedefaultheaderAction() {
        $caption = htmlentities($this->req->getParam('header_caption'));
        
        $saveCaption['setting_id']    = 5;
        $saveCaption['setting_title'] = 'page_header_caption';
        $saveCaption['setting_value'] = $caption;
        
        $upload = new Zend_File_Transfer_Adapter_Http();
        $dest_dir = $this->config->paths->base . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'headers';
        
        if(!file_exists($dest_dir)){
            if(!mkdir($dest_dir)){
                throw new Exception("Could not create upload folder for the header images to go into.");
            }
        }
        $upload->setDestination($dest_dir);

        $allowed_extensions = 'jpg,jpeg,bmp,gif,png,tiff';
        $upload->addValidator('Extension', false, array('extension' => $allowed_extensions, 'messages' => array(Zend_Validate_File_Extension::FALSE_EXTENSION => 'Invalid extension for file %value%')));

        $files = $upload->getFileInfo();
        
        if($files['header_picture']['name']) {
            foreach ($files as $file => $info) {
                if($upload->isUploaded($info['name']) && $upload->isValid($info['name'])) {
                    $upload->receive($info['name']);
                    $savePicture['setting_id']    = 4;
                    $savePicture['setting_title'] = 'page_header';
                    $savePicture['setting_value'] = $info['name'];
                    $picSaved = $this->dbMapper->saveRecord($savePicture, 'cms_settings', 'setting_id');
                } else {
                    throw new Exception('Error Reading Uploaded File.');
                }
            }
        } else {
            $picSaved = true;
        }
        
        if($this->dbMapper->saveRecord($saveCaption, 'cms_settings', 'setting_id') && $picSaved) {
            $this->_redirect('/pagebuilder/');
        } else {
            throw new Exception('Failed to save default header, please go back and try again');
        }
    }
}