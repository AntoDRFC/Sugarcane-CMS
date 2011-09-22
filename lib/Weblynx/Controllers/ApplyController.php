<?php

/**
 * Apply Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class ApplyController extends Weblynx_Controllers_Base
{    
    public function indexAction() {
        $this->view->page_title = 'Initial Enquiry';
        
        // If the user comes to the page insecurely somehow, redirect them
        if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
            $this->_redirect('https://www.thekeyfund.co.uk/apply');
        }
        
        $this->view->contentView = '/apply/form_part1.phtml';
        $this->view->metaTitle = 'Key Fund - Initial Enquiry';
        
        $this->renderView();
    }
    
    public function savepartoneAction() {
        $params = $this->req->getParams();
        
        unset($params['controller']);
        unset($params['action']);
        unset($params['module']);
        
        // unset the fields we don't want to automatically populate
        unset($params['governing_doc']);
        unset($params['state_aid']);
        unset($params['annual_accounts']);
        unset($params['trading_figures']);
        unset($params['bank_declined']);
        unset($params['bank_statements']);
        
        foreach($params as $param=>$value) {
            $data[$param] = htmlentities($value, ENT_QUOTES, 'utf-8');
        }
        
        // which files are required
        $required_fields = array('organisation' => 'Please enter the registered name of applicant organisation',
                                 'address'      => 'Please enter your address',
                                 'postcode'     => 'Please enter your postcode',);
        
        // check the required fields have been filled in
        $errors = array();
        foreach($required_fields as $required_field=>$error) {
            if($data[$required_field] == '') {
                $errors[] = $error;
            }
        }
        
        // create an authKey using the current timestamp and some salt
        $data['authKey'] = substr(md5('4nt0d3v' . time()), 0, 16);

        if(!count($errors)) {
            if($this->dbMapper->saveRecord($data, 'initial_enquiry', 'form_id')) {
                $upload = new Zend_File_Transfer();
                $dest_dir = $this->config->paths->base . '/secure/e9b63fb0c2749f5c0fd5c95b9e53ea32/' . $this->dbMapper->lastInsertId();
                if(!file_exists($dest_dir)){
                    if(!mkdir($dest_dir)){
                        throw new Exception("Could not create secure data directory");
                    }
                }
                $upload->setDestination($dest_dir);
                
                $allowed_extensions = '123,3g2,3gp,aac,aam,ac3,accdb,ai,aiff,ani,art,asf,ASM,asx,au,avi,blz,bmp,cdr,cmx,csv,cur,dcr,def,DIB,doc,docx,dot,dotx,dox,DRW,dsf,dwf,dwg,dwt,dxf,dxr,ea,emf,eml,eot,eps,ev,ev2,evy,FAX,fla,flac,flp,flv,FPX,FRM,FXR,ged,gif,gvi,HGL,hjt,hlp,HP,HPG,HPGL,hwp,IAM,ico,IDV,IDW,img,inc,IPT,isf,it,ivr,ivs,jfif,jif,jpe,jpeg,jpg,key,lbi,log,lwp,m4a,m4v,map,mbx,mdb,mdi,mid,midi,mod,mov,mp2,mp3,mp4,mpa,mpeg,mpg,mpp,msg,MSWMM,MWP,nsf,numbers,OBD,odf,odg,odp,ods,odt,ogg,opf,otg,otp,ots,ott,pages,PBM,pcd,pcl,pcx,pdf,pfr,PLT,ply,png,PNTG,pot,ppm,pps,ppt,pptx,prc,prs,PRT,ps,psd,psp,pub,qpw,qt,ra,ram,RLE,rm,rmf,rmvb,rmx,rnd,rp,rpm,RPT,rt,rtf,s3m,sam,sb,scm,sdc,sdd,sdw,SEP,SNP,spl,srs,stc,sti,stw,svg,svr,SVW,swa,swf,swv,sxc,SXC,sxd,sxg,sxi,SXM,SXP,sxw,tga,tgz,tif,tiff,tmb,tsp,txt,vac,VCF,VDA,viv,vivo,vob,vox,vsd,VST,wav,wax,wb1,wb2,wb3,wbmp,wdb,wdf,wk,wk1,wk2,wk3,wk4,wk5,wki,wks,wku,wm,wma,wmf,wmv,wmx,wmz,wp5,wp6,wp7,wpd,wpf,wpl,wps,wri,wrk,wrl,ws,wvx,wpp,XBM,xdm,XLC,xls,xlsm,xlsx,XLT,XLW,xml,xpm,xyp,sib,zip,';
                $upload->addValidator('Extension', false, array('extension' => $allowed_extensions, 'messages' => array(Zend_Validate_File_Extension::FALSE_EXTENSION => 'Invalid extension for file %value%')));
                
                $save['form_id'] = $this->dbMapper->lastInsertId();
                
                // upload the file to the server
                $files = $upload->getFileInfo();
                foreach ($files as $file => $info) {
                    if($upload->isUploaded($info['name']) && $upload->isValid($info['name'])) {
                        $upload->receive();
                        $save['file_field'] = $file;
                        $save['filename']   = '/secure/e9b63fb0c2749f5c0fd5c95b9e53ea32/' . $info['name'];
                        
                        $this->dbMapper->saveRecord($save, 'downloads', 'download_id');
                    }
                }
                
                $tr = new Zend_Mail_Transport_Sendmail('-f' . $this->config->settings->email->from);
                Zend_Mail::setDefaultTransport($tr);
                
                $this->view->data    = $data;
                $this->view->formId  = $this->dbMapper->lastInsertId();
                $this->view->siteurl = $this->config->urls->siteurl;
                
                $clientEmailBody = $this->view->render('/email_templates/client_email.phtml');
                $adminEmailBody  = $this->view->render('/email_templates/admin_email.phtml');
    
                $clientMail = new Zend_Mail();
                $clientMail->setSubject('Thank you for your application');
                $clientMail->setBodyHtml($clientEmailBody);
                $clientMail->setFrom($this->config->settings->email->from);
                $clientMail->addTo($data['email']);
                $clientMail->send();
                           
                $adminMail = new Zend_Mail();
                $adminMail->setSubject('A funding application has been made');
                $adminMail->setBodyHtml($adminEmailBody);
                $adminMail->setFrom($this->config->settings->email->from);
                $adminMail->addTo($this->config->settings->email->to);
                $adminMail->send();
                
                $this->_redirect('/view/initial-complete/');
            } else {
                $this->session->formdata['data']   = $this->req->getParams();
                $this->session->formdata['errors'] = $errors;
                
                throw new Exception('Failed to save form');
            }

        } else {
            $this->session->formdata['data']   = $this->req->getParams();
            $this->session->formdata['errors'] = $errors;
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
    }
     
    public function applicationloginAction() {
        $this->view->page_title = 'Application Stage 2 Login';
        
        $this->view->contentView = '/apply/login_part2.phtml';
        $this->view->metaTitle = 'Key Fund - Application Stage 2';
        
        $this->renderView();
    }
    
    public function loginparttwoAction() {
        // 2783cbe7d00cde10
        $email    = $this->req->getParam('email');
        $passcode = $this->req->getParam('passcode');
        
        if(!$email || !$passcode) {
            $this->session->formdata['errors'] = array('Invalid login details');
            $this->_redirect('/apply/applicationlogin/');
        }
        
        $applicationForm = $this->dbMapper->locateApplicationFormByCredentials($email, $passcode);
        if($applicationForm) {
            $this->session->form_id = $applicationForm['form_id'];
            $this->_redirect('/apply/stagetwo/');
        } else {
            $this->session->formdata['errors'] = array('Invalid login details');
            $this->_redirect('/apply/applicationlogin/');
        }
    }
    
    public function stagetwoAction() {
        $form_id = $this->session->form_id;
        
        if(!$form_id) {
            $this->_redirect('/apply/applicationlogin/');
        }
        
        $initialApplicationForm = $this->dbMapper->locateApplicationFormById($form_id);
        if(!$initialApplicationForm) {
            throw new Exception('There seems to have been an error, please contact The Key Fund');
        }
        
        $this->view->initialApplicationForm = $initialApplicationForm;
        
        $this->view->page_title = 'Application Stage 2';
        
        $this->view->contentView = '/apply/form_part2.phtml';
        $this->view->metaTitle = 'Key Fund - Application Stage 2';
        
        $this->renderView();
    }
}