<?php
require_once('modules/news/news.class.php');
require_once('connection/weblynx.php');
require_once('includes/DB.php');
include('checklogin.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

$news = new News($db);
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
        <h1>News</h1>
        <p><a href="/admin/editnews.php">Add News Item</a></p>
        <table class="admintable">
            <thead>
                <tr>
                    <th class="title">News Title</th>
                    <th>News Date</th>
                    <th>Published</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $newsItems = $news->getAllNews();
            if(count($newsItems)) {
                $template = '<tr>
                                 <td>%s</td>
                                 <td>%s</td>
                                 <td>%s</td>
                                 <td><a href="/admin/editnews.php?newsitem=%d">Edit</a> <a href="/admin/editnews.php?newsitem=%4$d&action=delete" class="deleteitem">Delete</a></td>
                             </tr>';
                foreach($newsItems as $newsItem) {
                    $published = ($newsItem['published'] == 1) ? 'Yes' : 'No';
                    echo sprintf($template, $newsItem['title'], date('d/m/Y', strtotime($newsItem['newsdate'])), $published, $newsItem['news_id']);
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>