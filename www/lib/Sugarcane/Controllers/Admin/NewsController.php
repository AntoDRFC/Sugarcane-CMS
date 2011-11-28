<?php

/**
 * News Admin Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class Admin_NewsController extends Sugarcane_Controllers_Base {
    
    public function preDispatch() {
        $this->view->currentpage = 'news';
        $this->view->page_title = 'News';
        $this->view->css[] = '/css/news.css';
    }
    
    public function indexAction() {
        $newsitems = $this->adminMapper->getNewsItems();
        $this->view->newsitems = $newsitems;
        
        $this->view->js[] = '/admin/js/admin.js';
        
        $this->view->contentView = '/admin/news/index.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function editAction() {
        $news_id  = $this->req->getParam('newsitem');
        
        $this->view->newsitem_id = $news_id;
        $newsitem = $this->dbMapper->getNewsItem($news_id);
        
        if($news_id && !$newsitem) {
            throw new Exception('Error: No news item with the specified id was found');
        } else {
            $this->view->newsitem = $newsitem;
        }
        
        $this->view->js[] = '/admin/js/lib/jquery-ui-1.8.1.custom.min.js';
        $this->view->js[] = '/admin/js/ckeditor/ckeditor.js';
        $this->view->js[] = '/admin/js/AjexFileManager/ajex.js';
        $this->view->js[] = '/admin/js/admin.js';
        
        $this->view->css[] = '/admin/css/black-tie/jquery-ui-1.8.1.custom.css';
        
        $this->view->contentView = '/admin/news/edit.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function savenewsitemAction() {
        $save['news_id']   = $this->req->getPost('news_id');
        $save['title']     = $this->req->getPost('news_title');
        $save['preview']   = $this->req->getPost('preview_text');
        $save['content']   = $this->req->getPost('news_text');
        $save['newsdate']  = Globals::dateUkToMysql($this->req->getPost('news_date'));
        $save['published'] = $this->req->getPost('published');
        
        $upload = new Zend_File_Transfer();
        $dest_dir = $this->config->paths->base . '/images/newsimages/';
        if(!file_exists($dest_dir)){
            if(!mkdir($dest_dir)){
                throw new Exception("Could not create data directory");
            }
        }
        $upload->setDestination($dest_dir);
        
        $allowed_extensions = '123,3g2,3gp,aac,aam,ac3,accdb,ai,aiff,ani,art,asf,ASM,asx,au,avi,blz,bmp,cdr,cmx,csv,cur,dcr,def,DIB,doc,docx,dot,dotx,dox,DRW,dsf,dwf,dwg,dwt,dxf,dxr,ea,emf,eml,eot,eps,ev,ev2,evy,FAX,fla,flac,flp,flv,FPX,FRM,FXR,ged,gif,gvi,HGL,hjt,hlp,HP,HPG,HPGL,hwp,IAM,ico,IDV,IDW,img,inc,IPT,isf,it,ivr,ivs,jfif,jif,jpe,jpeg,jpg,key,lbi,log,lwp,m4a,m4v,map,mbx,mdb,mdi,mid,midi,mod,mov,mp2,mp3,mp4,mpa,mpeg,mpg,mpp,msg,MSWMM,MWP,nsf,numbers,OBD,odf,odg,odp,ods,odt,ogg,opf,otg,otp,ots,ott,pages,PBM,pcd,pcl,pcx,pdf,pfr,PLT,ply,png,PNTG,pot,ppm,pps,ppt,pptx,prc,prs,PRT,ps,psd,psp,pub,qpw,qt,ra,ram,RLE,rm,rmf,rmvb,rmx,rnd,rp,rpm,RPT,rt,rtf,s3m,sam,sb,scm,sdc,sdd,sdw,SEP,SNP,spl,srs,stc,sti,stw,svg,svr,SVW,swa,swf,swv,sxc,SXC,sxd,sxg,sxi,SXM,SXP,sxw,tga,tgz,tif,tiff,tmb,tsp,txt,vac,VCF,VDA,viv,vivo,vob,vox,vsd,VST,wav,wax,wb1,wb2,wb3,wbmp,wdb,wdf,wk,wk1,wk2,wk3,wk4,wk5,wki,wks,wku,wm,wma,wmf,wmv,wmx,wmz,wp5,wp6,wp7,wpd,wpf,wpl,wps,wri,wrk,wrl,ws,wvx,wpp,XBM,xdm,XLC,xls,xlsm,xlsx,XLT,XLW,xml,xpm,xyp,sib,zip,';
        $upload->addValidator('Extension', false, array('extension' => $allowed_extensions, 'messages' => array(Zend_Validate_File_Extension::FALSE_EXTENSION => 'Invalid extension for file %value%')));
        
        // upload the file to the server
        $files = $upload->getFileInfo();
        foreach ($files as $file => $info) {
            if($upload->isUploaded($info['name']) && $upload->isValid($info['name'])) {
                $upload->receive();
                $save['news_image'] = '/images/newsimages/' . $info['name'];
            }
        }
        
        if($this->dbMapper->saveRecord($save, 'news', 'news_id')) {
            $this->_redirect('/admin/news/');
        } else {
            throw new Exception('Failed to save news item');
        }
    }
    
    public function deleteAction() {
        $news_id = $this->req->getParam('newsitem');

        if($this->dbMapper->deleteRecord('news', 'news_id', $news_id)) {
            $this->_redirect('/admin/news/');
        } else {
            throw new Exception('Failed to delete news item, please go back and try again.');
        }
    }
    
}