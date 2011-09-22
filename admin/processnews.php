<?php
require_once('modules/news/news.class.php');
require_once('connection/weblynx.php');
require_once('includes/DB.php');
include('checklogin.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

$news = new News($db);

function dateUkToMysql($date) {
	list($d, $m, $y) = explode('/', $date, 3);
	$ts = strtotime("{$y}-{$m}-{$d}");
	$date = date('Y-m-d', $ts);

	if(!$ts || !checkdate($m, $d, $y)){
		return false;
	}else {
		return $date;
	}
}

function uploadimage($image) {
    if(!empty($image['name'])) {
        $filename   = $image['name'];
    	$savePath   = '../images/news_images/main_images';
        $tmpPath    = $image['tmp_name'];

        $extension = strtolower(file_extension($filename));
        $allowed_extensions = array('jpg','jpeg','gif','bmp','png');
        if(in_array($extension, $allowed_extensions)) {
            if(is_uploaded_file($tmpPath)){
                $destination = $savePath . DIRECTORY_SEPARATOR . $filename;

                if(!file_exists($destination)){
                    move_uploaded_file($tmpPath, $destination);
                    return $filename;
                } else {
                	if(unlink($destination)) {
                        move_uploaded_file($tmpPath, $destination);
                        return $filename;
            		} else {
            			error_log("Image already exists, and could not be deleted");
            			return false;
            		}
                }
            } else {
            	error_log("Could not upload file: $tmpPath ". $filename);
            	return false;
            }
        }
    }
}

function file_extension($filename) {
    $path_info = pathinfo($filename);
    return $path_info['extension'];
}

$save['news_id']      = $_POST['news_id'];
$save['news_title']   = $_POST['news_title'];
$save['news_date']    = dateUkToMysql($_POST['news_date']);
$save['preview_text'] = $_POST['preview_text'];
$save['news_text']    = $_POST['news_text'];
$save['published']    = $_POST['published'];

if(!empty($_FILES['image']['name'])) {
    $uploaded = uploadimage($_FILES['image']);
    if($uploaded) {
        $save['news_image'] = $uploaded;
    }
}

$saved = $news->saveNewsItem($save);

if($saved) {
    header('Location: /admin/news.php');
}
?>