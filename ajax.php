<?php 
ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL | E_STRICT);
require "../includes/db.php"; 
if ((!empty($_POST))&&(isset($_SERVER['HTTP_X_REQUESTED_WITH']))&&($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
header('Content-Type: application/json; charset=utf-8');
$response = array();
$response['status'] = 'bad';
print_r($_POST);
if (!empty($_FILES['file']['tmp_name']) && !$_POST['article_id']){
	for($key=0;$key<count($_FILES['file']['tmp_name']);$key++){
		$upload_path = "../imgs/upload/";
		$user_filename = $_FILES['file']['name'][$key];
		$userfile_basename = pathinfo($user_filename,PATHINFO_FILENAME );
		$userfile_extension = pathinfo($user_filename, PATHINFO_EXTENSION);
		$server_filename = $userfile_basename . md5(random_int(-9999, 9999)) . "." . $userfile_extension;
		$server_filepath = $upload_path . $server_filename;
		$i = 0;
		while(file_exists($server_filepath)){
			$i++;
			$server_filepath = $upload_path .  $userfile_basename . "($i)." . $userfile_extension;
		}
		if (copy($_FILES['file']['tmp_name'][$key], $server_filepath)){
			$response['files'][] =  $server_filepath;
			mysqli_query($connection, "INSERT INTO `gallery` (`img`) VALUES ('". $server_filepath ."')");
			$response['status'] = 'ok';
		}
	}
	
} else {
    for($key=0;$key<count($_FILES['file']['tmp_name']);$key++){
		$upload_path = "../imgs/upload/";
		$user_filename = $_FILES['file']['name'][$key];
		$userfile_basename = pathinfo($user_filename,PATHINFO_FILENAME );
		$userfile_extension = pathinfo($user_filename, PATHINFO_EXTENSION);
		$server_filename = $userfile_basename . md5(random_int(-9999, 9999)) . "." . $userfile_extension;
		$server_filepath = $upload_path . $server_filename;
		$i = 0;
		while(file_exists($server_filepath)){
			$i++;
			$server_filepath = $upload_path .  $userfile_basename . "($i)." . $userfile_extension;
		}
		if (copy($_FILES['file']['tmp_name'][$key], $server_filepath)){
			$response['files'][] =  $server_filepath;
			mysqli_query($connection, "INSERT INTO `gallery` (`img`) VALUES ('$server_filepath', '" . $_POST['article_id']; . "')";
			mysqli_query($connection, "INSERT INTO `articles` (`title`, `text`, `img`) VALUES ('".$_POST['title']."', '".$_POST['text']."', '1')");
			$response['status'] = 'ok';
		}
	}
}

echo json_encode($response);
?> 