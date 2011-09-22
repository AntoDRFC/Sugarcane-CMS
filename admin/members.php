<?php
require_once('modules/random_members/random_members.class.php');
require_once('connection/weblynx.php');
require_once('includes/DB.php');
include('checklogin.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

$random_members = new Random_Members($db);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Sugarcane Web CMS</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<link rel="shortcut icon" href="SUGARCANE_WEB.ico" />
<script type="text/javascript" src="js/lib/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/admin.js"></script>
</head>

<body>
<div id="page">
    <div id="header">
        <a target="_blank" href="http://www.sugarcaneweb.co.uk"><img src="images/SUGARCANE_WEB_ADMIN_LOGO.png" alt="sugarcane_web" /></a>
        <?php include('includes/nav.php'); ?>
    </div>
    
    <div id="content">
        <h1>Members</h1>
        <p><a href="/admin/editmember.php">Add member</a></p>
        <table class="admintable">
            <thead>
                <tr>
                    <th class="title">Member Name</th>
                    <th class="title2">Summary</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $members = $random_members->getAllMembers();
            if(count($members)) {
                $template = '<tr>
                                 <td>%s</td>
                                 <td>%s</td>
                                 <td><a href="/admin/editmember.php?member=%d">Edit</a> <a href="/admin/editmember.php?member=%3$d&action=delete" class="deletemember">Delete</a></td>
                             </tr>';
                foreach($members as $member) {
                    echo sprintf($template, $member['client_name'], substr($member['summary'], 0 , 20) . '...', $member['member_id']);
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>