<?php
require_once('connection/weblynx.php');
require_once('includes/DB.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

// start a session
session_start();

if(isset($_GET['do'])) {
    if($_GET['do'] == 'login') {
        $user = htmlentities($_POST['wl_username']);
        $pass = md5(htmlentities($_POST['wl_password']));
        
        $sql = sprintf("SELECT * FROM cms_users WHERE username = '%s' AND password = '%s' AND user_active = 1", $user, $pass);
        $userdetails = $db->get_row($sql);
        
        if(count($userdetails)) {
            $_SESSION['user']     = $user;
            $_SESSION['loggedin'] = true;
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['loginfail'] = 'Login failed, username or password incorrect';
        }
    }
    
    if($_GET['do'] == 'logout') {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Sugarcane Web CMS</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<link rel="shortcut icon" href="SUGARCANE_WEB.ico" />
</head>

<body>
<div id="page">
    <div id="header">
        <a target="_blank" href="http://www.sugarcaneweb.co.uk"><img src="images/SUGARCANE_WEB_ADMIN_LOGO.png" alt="sugarcane_web" /></a>
    </div>
    
    <div id="content">
        <h1>Sugarcane Web CMS Login</h1>
        <p>Please login to the Sugarcane Web CMS to continue</p>
        <form method="post" action="login.php?do=login">
            <ul class="form">
                <?php if(!empty($_SESSION['loginfail'])) { ?>
                <li class="error">
                    <?=$_SESSION['loginfail']?>
                    <?php unset($_SESSION['loginfail']); ?>
                </li>
                <?php } ?>
                <li>
                    <label class="desc">Username</label>
                    <input type="text" name="wl_username" />
                </li>
                <li>
                    <label class="desc">Password</label>
                    <input type="password" name="wl_password" />
                </li>
                <li>
                    <input type="submit" value="Login" />
                </li>
            </ul>
        </form>
    </div>
</div>
</body>
</html>