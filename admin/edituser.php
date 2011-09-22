<?php
include('checklogin.php');
require_once('connection/weblynx.php');
require_once('includes/DB.php');

// Setup the database connection
$db = new DB();
$db->connect($database, $hostname, $username, $password);

$userId = (int) $_GET['user'];

if($_GET['action'] == 'delete') {
    $sql = "DELETE FROM cms_users WHERE user_id = " . $userId;
    if($db->query($sql)) {
        header('Location: /admin/users.php');
    } else {
        echo 'Failed to delete user';
    }
}

//$newsItem = $news->getNewsItem($newsItemId);
$user = $db->get_row('SELECT * FROM cms_users WHERE user_id = ' . $userId);

$firstname = !empty($user['firstname']) ? $user['firstname'] : '';
$surname   = !empty($user['lastname']) ? $user['lastname'] : '';
$email   = !empty($user['email']) ? $user['email'] : '';
$username  = !empty($user['username']) ? $user['username'] : '';
$published = ($user['user_active'] == '1') ? 'Yes' : 'No';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Sugarcane Web CMS</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<link rel="shortcut icon" href="SUGARCANE_WEB.ico" />
<script type="text/javascript" src="js/lib/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery.showpassword-1.0.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#password').showPassword('#show_pw');
});
</script>
</head>

<body class="yui-skin-sam">
<div id="page">
    <div id="header">
        <a target="_blank" href="http://www.sugarcaneweb.co.uk"><img src="images/SUGARCANE_WEB_ADMIN_LOGO.png" alt="sugarcane_web" /></a>
        <?php include('includes/nav.php'); ?>
    </div>
    
    <div id="content">
        <h1>Edit User</h1>
        <form method="post" action="processuser.php" autocomplete="off">
            <ul class="form">
                <input type="hidden" name="user_id" value="<?=$userId ? $userId : ''?>" />
                <li>
                    <h3>Person details</h3>
                </li>
                <li>
                    <label class="desc">First Name</label>
                    <input type="text" name="firstname" value="<?=$firstname?>" />
                </li>
                <li>
                    <label class="desc">Surname</label>
                    <input type="text" name="surname" value="<?=$surname?>" />
                </li>
                <li>
                    <label class="desc">Email</label>
                    <input type="text" name="email" value="<?=$email?>" />
                </li>
                <li>
                    <h3>Login details</h3>
                </li>
                <li>
                    <label class="desc">Username</label>
                    <input type="text" name="username" value="<?=$username?>" />
                </li>
                <li>
                    <label class="desc">Password</label>
                    <input type="password" name="password" id="password" /><br />
                    <label><input type="checkbox" value="show_pw" id="show_pw" /> show password</label>
                </li>
                <li>
                    <label class="desc">Account Active</label>
                    <label><input type="radio" name="active" value="1" <?=$published == 'Yes' ? 'checked="checked"' : ''?> /> Yes</label> <label><input type="radio" name="active" value="0" <?=$published == 'No' ? 'checked="checked"' : ''?> /> No</label>
                </li>
                <li id="submitarea">
                    <input type="submit" value="Save" />
                </li>
            </ul>
        </form>
    </div>
</div>
</body>
</html>