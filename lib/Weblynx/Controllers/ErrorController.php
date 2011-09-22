<?php
class ErrorController extends Weblynx_Controllers_Base
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $this->getResponse()->clearBody();
        
        $errormsg = $errors->exception->getMessage();
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            case ($errormsg == 404):
                // 404 error -- controller or action not found
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $this->view->titleText   = "Error 404 - Page not found";
                
                $this->view->errorMessage = '<p><strong>This might be because:</strong><br />
                                             You have typed the web address incorrectly, or<br />
                                             the page you were looking for may have been moved, updated or deleted.</p>
                                             <p><a href="/">Return to the Key Fund homepage</a></p>';
                //$this->view->errorMessage = $errors->exception->getMessage();
                
                // Log only broken links, not users who can't type
                if (Globals::ifsetor($_SERVER["HTTP_referer"], false)) {
                    error_log('Page Not Found At URL ' . $_SERVER['REQUEST_URI'] . ' with referer ' . $_SERVER["HTTP_referer"] . ' (missing controller/action?)');
                //} else {
                //    error_log('Page Not Found At URL ' . $_SERVER['REQUEST_URI'] . ' with no referer (missing controller/action?)');
                }

                break;
            default:
                $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Error');
                $this->view->titleText     = "An error occured";
                $this->view->errorMessage  = $errors->exception->getMessage();
                $this->view->advancedError = $errors->exception->getTraceAsString();
                $this->view->params        = $this->req->getParams();
                
                // Log all server-side errors
                error_log($errors->exception->getMessage());
        }

        $this->view->contentView = "errors/default.phtml";
        $this->renderView();
    }
}