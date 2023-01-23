<?php 
$connection = mysqli_connect('server', 'username', 'password', 'db_name');
if($connection == false){
	echo 'error? <br>';
	echo mysqli_connect_error();
	exit();
}

?>