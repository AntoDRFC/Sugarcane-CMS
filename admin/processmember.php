<?php
require_once('modules/random_members/random_members.class.php');
require_once('connection/weblynx.php');
require_once('includes/DB.php');
include('checklogin.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

$random_members = new Random_Members($db);

function uploadimage($image) {
    if(!empty($image['name'])) {
        $filename   = $image['name'];
    	$savePath   = '../images/member_images';
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

$save['member_id']   = (int) $_POST['member_id'];
$save['client_name'] = htmlentities($_POST['client_name']);
$save['summary']     = htmlentities($_POST['summary']);
$save['full_text']   = htmlentities($_POST['full_text']);
$save['website']     = htmlentities($_POST['website']);

if(!empty($_FILES['image']['name'])) {
    $uploaded = uploadimage($_FILES['image']);
    if($uploaded) {
        $save['logo'] = $uploaded;
    }
}

$saved = $random_members->saveMember($save);

if($saved) {
    header('Location: /admin/members.php');
}
?>