<?php
require_once('modules/news/news.class.php');
require_once('connection/weblynx.php');
require_once('includes/DB.php');
include('checklogin.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

$news = new News($db);

$newsItemId = (int) $_GET['newsitem'];

if($_GET['action'] == 'delete') {
    $deleted = $news->deleteNewsItem($newsItemId);
    if($deleted) {
        header('Location: /admin/news.php');
    } else {
        echo 'Failed to delete news item';
    }
}

$newsItem = $news->getNewsItem($newsItemId);

$title      = !empty($newsItem['title']) ? $newsItem['title'] : '';
$newsdate   = !empty($newsItem['newsdate']) ? date('d/m/Y', strtotime($newsItem['newsdate'])) : '';
$image      = !empty($newsItem['news_image']) ? $newsItem['news_image'] : '';
$preview    = !empty($newsItem['preview']) ? $newsItem['preview'] : '';
$content    = !empty($newsItem['content']) ? $newsItem['content'] : '';
$published  = ($newsItem['published'] == '1') ? 'Yes' : 'No';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Sugarcane Web CMS</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<link rel="shortcut icon" href="SUGARCANE_WEB.ico" />
<script type="text/javascript" src="js/lib/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/lib/jquery-ui-1.8.1.custom.min.js"></script>
<script src="js/ckeditor/ckeditor.js"></script>
<script src="js/AjexFileManager/ajex.js"></script>

<!-- Skin CSS file -->
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.1/build/assets/skins/sam/skin.css">
<!-- Utility Dependencies -->
<script src="http://yui.yahooapis.com/2.8.1/build/yahoo-dom-event/yahoo-dom-event.js"></script> 
<script src="http://yui.yahooapis.com/2.8.1/build/element/element-min.js"></script> 
<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
<script src="http://yui.yahooapis.com/2.8.1/build/container/container_core-min.js"></script>
<script src="http://yui.yahooapis.com/2.8.1/build/menu/menu-min.js"></script>
<script src="http://yui.yahooapis.com/2.8.1/build/button/button-min.js"></script>
<!-- Source file for Rich Text Editor-->
<script src="http://yui.yahooapis.com/2.8.1/build/editor/editor-min.js"></script>
<script src="http://yui.yahooapis.com/2.8.1/build/connection/connection-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.8.1/build/logger/logger-min.js"></script>
<script src="js/yui-image-uploader26.js"></script>

<link rel="stylesheet" type="text/css" href="css/black-tie/jquery-ui-1.8.1.custom.css">
</head>

<body class="yui-skin-sam">
<div id="page">
    <div id="header">
        <a target="_blank" href="http://www.sugarcaneweb.co.uk"><img src="images/SUGARCANE_WEB_ADMIN_LOGO.png" alt="sugarcane_web" /></a>
        <?php include('includes/nav.php'); ?>
    </div>
    
    <div id="content">
        <h1 style="font-size: 18px;">Edit News Item</h1>
        <form method="post" action="processnews.php" enctype="multipart/form-data">
            <ul class="form">
                <input type="hidden" name="news_id" value="<?=$newsItemId ? $newsItemId : ''?>" />
                <li>
                    <label class="desc">Title</label>
                    <input type="text" name="news_title" value="<?=$title?>" />
                </li>
                <li>
                    <label class="desc">News date</label>
                    <input type="text" name="news_date" id="news_date" value="<?=$newsdate?>" />
                </li>
                <li>
                    <label class="desc">News image</label>
                    <?php if($image) { ?>
                    <img src="<?='http://'.$_SERVER['HTTP_HOST'].'/images/news_images/main_images/'.$image?>" alt="<?=$title?>" class="currentimage" />
                    <?php } ?>
                    <input type="file" name="image" id="image" />
                </li>
                <li>
                    <label class="desc">Preview text</label>
                    <textarea name="preview_text" id="preview_text"><?=$preview?></textarea>
                </li>
                <li>
                    <label class="desc">News text</label>
                    <textarea name="news_text" id="news_text"><?=$content?></textarea>
                </li>
                <li>
                    <label class="desc">Published</label>
                    <label><input type="radio" name="published" value="1" <?=$published == 'Yes' ? 'checked="checked"' : ''?> /> Yes</label> <label><input type="radio" name="published" value="0" <?=$published == 'No' ? 'checked="checked"' : ''?> /> No</label>
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