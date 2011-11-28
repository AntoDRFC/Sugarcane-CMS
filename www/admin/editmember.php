<?php
require_once('modules/random_members/random_members.class.php');
require_once('connection/weblynx.php');
require_once('includes/DB.php');
include('checklogin.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

$random_members = new Random_Members($db);

$memberId = (int) $_GET['member'];

if($_GET['action'] == 'delete') {
    $deleted = $random_members->deleteMember($memberId);
    if($deleted) {
        header('Location: /admin/members.php');
    } else {
        echo 'Failed to delete member';
    }
}

$member = $random_members->getMember($memberId);

$client_name = !empty($member['client_name']) ? $member['client_name'] : '';
$summary     = !empty($member['summary']) ? $member['summary'] : '';
$full_text   = !empty($member['full_text']) ? $member['full_text'] : '';
$logo        = !empty($member['logo']) ? $member['logo'] : '';
$website     = !empty($member['website']) ? $member['website'] : '';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Sugarcane Web CMS</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<link rel="shortcut icon" href="SUGARCANE_WEB.ico" />
<script type="text/javascript" src="js/lib/jquery-1.4.2.min.js"></script>
<style type="text/css">
textarea {
    width: 400px;
    height: 60px;
}
.fulltext {
    height: 150px;
}
</style>
</head>

<body>
<div id="page">
    <div id="header">
        <a target="_blank" href="http://www.sugarcaneweb.co.uk"><img src="images/SUGARCANE_WEB_ADMIN_LOGO.png" alt="sugarcane_web" /></a>
        <?php include('includes/nav.php'); ?>
    </div>
    
    <div id="content">
        <h1>Edit Member</h1>
        <form method="post" action="processmember.php" enctype="multipart/form-data">
            <ul class="form">
                <input type="hidden" name="member_id" value="<?=$memberId ? $memberId : ''?>" />
                <li>
                    <label class="desc">Title</label>
                    <input type="text" name="client_name" value="<?=$client_name?>" />
                </li>
                <li>
                    <label class="desc">Member logo</label>
                    <?php if($logo) { ?>
                    <img src="<?='http://'.$_SERVER['HTTP_HOST'].'/images/member_images/'.$logo?>" alt="<?=$client_name?>" class="currentimage" />
                    <?php } ?>
                    <input type="file" name="image" id="image" />
                </li>
                <li>
                    <label class="desc">Summary</label>
                    <textarea name="summary"><?=$summary?></textarea>
                </li>
                <li>
                    <label class="desc">Full text</label>
                    <textarea name="full_text" class="fulltext"><?=$full_text?></textarea>
                </li>
                <li>
                    <label class="desc">Website</label>
                    <input type="text" name="website" value="<?=$website?>" />
                </li>
                <li id="submitarea">
                    <input type="submit" value="Save" />
                </li>
            </ul>
        </form>
    </div>
</div>
<script type="text/javascript" src="js/admin.js"></script>
</body>
</html>