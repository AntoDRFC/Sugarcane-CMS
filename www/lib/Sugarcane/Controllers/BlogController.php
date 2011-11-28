<?php

/**
 * Blog Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class BlogController extends Sugarcane_Controllers_Base {
    
    public function indexAction() {
        $blog_id = $this->req->getParam('blog_id', 1);
        
        $blog_info = $this->dbMapper->getBlogInfo($blog_id);
        
        $options['filter']['approved'] = 'Y';
        $options['filter']['blog_id']  = $blog_id;
    
        // get the blog posts
        $posts = $this->dbMapper->getBlogPosts($options);
        
        // send the posts to the view to be rendered
        $this->view->blog_posts = $posts;
        
        $this->view->css[] = '/css/blog.css';
        
        $this->view->contentView = '/blog/' . $blog_info['template'];
        $this->renderView('blog.phtml');
    }
    
    public function archiveAction() {
        $blog_id = $this->req->getParam('blog_id', 1);
        
        $blog_info = $this->dbMapper->getBlogInfo($blog_id);
        
        $options['filter']['approved'] = 'Y';
        $options['filter']['blog_id']  = $blog_id;
    
        // get the blog posts
        $posts = $this->dbMapper->getArchiveBlogPosts($options);
        
        // send the posts to the view to be rendered
        $this->view->blog_posts = $posts;
        $this->view->archive    = true;
        
        $this->view->css[] = '/css/blog.css';
        
        $this->view->contentView = '/blog/' . $blog_info['template'];
        $this->renderView('blog.phtml');
    }
    
    public function viewpostAction() {
        $post_id = $this->req->getParam('postid');
        
        // send the site url to the view so we can share it
        $this->view->siteurl = $this->config->urls->siteurl;
        
        // get the blog post for this id
        $post = $this->dbMapper->getBlogPost($post_id);
        
        $blog_info = $this->dbMapper->getBlogInfo($post['blog_id']);
        $this->view->blog_id    = $blog_info['blog_id'];
        $this->view->post_title = $blog_info['blog_title'];
        $this->view->metaTitle  = $blog_info['blog_title'];
        
        // pass the single post to the view
        $this->view->post = $post;
        
        // lets get any comments this post may have
        $comments = $this->dbMapper->getPostComments($post_id);
        
        // send the comments to the view
        $this->view->comments = $comments;
        
        require_once('recaptchalib.php');
        $publickey = '6LcfnscSAAAAAH52vMlnvlMDmRbZM7J-927DcsI9'; // you got this from the signup page
        $this->view->recaptcha = recaptcha_get_html($publickey);
        
        $this->view->css[] = '/css/blog.css';
        
        $this->view->contentView = '/blog/viewpost.phtml';
        $this->renderView('blog.phtml');        
    }
    
    public function postcommentAction() {
        require_once('recaptchalib.php');
        $privatekey = "6LcfnscSAAAAAA-D41YbssNsTSjp52BrW5D4-YNN";
        
        $challenge_field = $this->req->getParam('recaptcha_challenge_field');
        $response_field  = $this->req->getParam('recaptcha_response_field');
        
        $resp = recaptcha_check_answer ($privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $challenge_field,
                                        $response_field);
        
        if (!$resp->is_valid) {
            // What happens when the CAPTCHA was entered incorrectly
            throw new Exception('Incorrect capthca, please go back and try again');
        } else {
            // Your code here to handle a successful verification
            $comment['post_id']   = (int) $this->req->getParam('post_id');
            $comment['firstname'] = htmlentities($this->req->getParam('firstname'));
            $comment['surname']   = htmlentities($this->req->getParam('lastname'));
            $comment['email']     = htmlentities($this->req->getParam('email'));
            $comment['comment']   = htmlentities($this->req->getParam('comment'));
            $comment['approved']  = 'P';
            
            if($this->dbMapper->saveRecord($comment, 'blogcomments', 'comment_id')) {
                $this->_redirect('/view/commentposted/');
            }
        }
    }
    
}