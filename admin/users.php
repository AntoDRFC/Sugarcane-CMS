<?php
include('checklogin.php');
require_once('connection/weblynx.php');
require_once('includes/DB.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

$sql = "SELECT * FROM cms_users ORDER BY username ASC";
$users = $db->get_rows($sql);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Sugarcane Web CMS</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<link rel="shortcut icon" href="SUGARCANE_WEB.ico" />
<script type="text/javascript" src="js/lib/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/users.js"></script>
</head>

<body>
<div id="page">
    <div id="header">
        <a target="_blank" href="http://www.sugarcaneweb.co.uk"><img src="images/SUGARCANE_WEB_ADMIN_LOGO.png" alt="sugarcane_web" /></a>
        <?php include('includes/nav.php'); ?>
    </div>
    
    <div id="content">
        <h1>Users</h1>
        <p><a href="/admin/edituser.php">Create New User</a></p>
        <table class="admintable">
            <thead>
                <tr>
                    <th style="text-align: left;">Username</th>
                    <th style="text-align: left;">Email</th>
                    <th>Account Status</th>
                    <th style="display: none;">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(count($users)) {
                $template = '<tr>
                                 <td>%s</td>
                                 <td>%s</td>
                                 <td>%s</td>
                                 <td><a href="/admin/edituser.php?user=%d">Edit User</a> <a href="/admin/edituser.php?user=%4$d&action=delete" class="deleteuser"> Delete User</a></td>
                             </tr>';
                foreach($users as $user) {
                    $active = ($user['user_active'] == 1) ? 'Active' : 'Inactive';
                    echo sprintf($template, $user['username'], $user['email'], $active, $user['user_id']);
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>