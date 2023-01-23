   <!-- EDITED ON 27.05.2022 -->
<?php
  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL | E_STRICT);
  require "../includes/db.php"; 
  require "../includes/functions.php";
?>

<?php
if(isset($_GET['action'])){
  if($_GET['passw']=="place_for_tmp_password" && isset($_GET['user_id'])){
    if($_GET['action']=="verify"){
      mysqli_query($connection, "UPDATE `users` SET `verified` = '1' WHERE `id` = ". $_GET['user_id']);
      echo "<script>window.location.replace('admin.php')</script>";
    }
    if($_GET['action']=="make_admin"){
      mysqli_query($connection, "UPDATE `users` SET `is_admin` = '1' WHERE `id` = ". $_GET['user_id']);
      mysqli_query($connection, "UPDATE `users` SET `verified` = '1' WHERE `id` = ". $_GET['user_id']);
      echo "<script>window.location.replace('admin.php?p=users')</script>";
    }
    if($_GET['action']=="delete_admin"){
      mysqli_query($connection, "UPDATE `users` SET `is_admin` = '0' WHERE `id` = ". $_GET['user_id']);
      echo "<script>window.location.replace('admin.php?p=users')</script>";
    }
    if($_GET['action']=="delete_user"){
      mysqli_query($connection, "DELETE FROM `users` WHERE `id` = ". $_GET['user_id']);
      echo "<script>window.location.replace('admin.php?p=users')</script>";
    }
  }
  if($_GET['action']=='logout'){
    setcookie("id", "", time() - 3600*24*30*12, "/");
    echo "<script>window.location.replace('../')</script>";
  }
}
if(isset($_GET['delete_doc'])){
  echo unlink(mysqli_fetch_assoc(mysqli_query($connection, "SELECT `src` FROM `docs` WHERE `id` = " . $_GET['delete_doc']))['src']);
  mysqli_query($connection, "DELETE FROM `docs` WHERE `id` = ". $_GET['delete_doc']);
  echo "<script>window.location.replace('admin.php?p=useful')</script>";
}


if(isset($_POST['log_submit'])){
  $users = mysqli_query($connection, "SELECT `username` FROM `users`");
  // print_r($users_usernames);
  while($usernames=mysqli_fetch_assoc($users)){
    if($_POST['username'] == $usernames['username']){
      $username = $_POST['username'];
      $password = mysqli_fetch_assoc(mysqli_query($connection, "SELECT `password` FROM `users` WHERE `username` = '". $username."'"));
      if($password['password'] == $_POST['password']){
        $id = mysqli_fetch_assoc(mysqli_query($connection, "SELECT `id` FROM `users` WHERE `username`= '". $username."'"));
        setcookie("id", $id['id'], time()+60*60*24*365*5, "/");
        echo "<script>window.location.replace('../../index.php')</script>";
      } else {
      echo "<script>window.location.replace('../../index.php?logErr=true')</script>";
      }
    } else {
    echo "<script>window.location.replace('../../index.php?logErr=true')</script>";
    }
  }
}

// SELECT id FROM table ORDER BY id DESC LIMIT 1
if(isset($_POST['submit'])){
  $title = $_POST['title'];
  $text = $_POST['text'];
  $img = true;
  $article_id = $_POST['article_id'];
  print_r($_FILES);
  if($_FILES){
    foreach ($_FILES["article_imgs"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $upload_path = "../imgs/upload/";
            $tmp_name = $_FILES["article_imgs"]["tmp_name"][$key];
            $name = $_FILES["article_imgs"]["name"][$key];
            $sname = md5(random_int(-9999, 9999)) . "--" . md5($tmp_name) . "--" . $name;
            $server_filepath = $upload_path . $sname;
            // move_uploaded_file($tmp_name, "$name");
            if (move_uploaded_file($_FILES['article_imgs']['tmp_name'][$key], $server_filepath)){
                mysqli_query($connection, "INSERT INTO `gallery` (`img`, `article_id`) VALUES ('$server_filepath', '$article_id')");   
            } else {
                echo "fsociety";
            }
            
        }
    }
    
  }
  $connection->query("INSERT INTO `articles` (`title`, `text`, `img`) VALUES ('$title', '$text', '$img')");
  echo "<script>window.location.replace('../index.php')</script>";
}



if(isset($_POST['edit_submit'])){
  $title = $_POST['title'];
  $text = $_POST['text'];
  $edit_id = $_POST['edit_id'];
  $connection->query("UPDATE `articles` SET `title`='$title',`text`='$text' WHERE `id` = '$edit_id'");
  echo "<script>window.location.replace('admin.php?p=articles')</script>";
}





if(isset($_POST['reg_submit'])){
  $parentname = $_POST['parentname'];
  $parentsecname = $_POST['parentsecname'];
  $parentsurname = $_POST['parentsurname'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $city = $_POST['city'];
  $info = $_POST['info'];
  if(isset($_POST['comment'])){
    $comment = $_POST['comment'];
  }


  $q = "INSERT INTO `users` (`name`, `parentsecname`, `surname`, `username`, `password`, `email`, `phone`, `city`, `info`) VALUES ('$parentname', '$parentsecname', '$parentsurname', '$username', '$password', '$email', '$phone', '$city', '$info')";
  $connection->query($q);
  $id = mysqli_fetch_assoc(mysqli_query($connection, "SELECT `id` FROM `users` WHERE `username` = '$username'"))['id'];
  setcookie("id", $id, time()+60*60*24, "/");

  if (isset($_POST['child_name'])) {
    $child_name = $_POST['child_name'];
    $child_sex = $_POST['child_sex'];
    $child_age = strval($_POST['bdate_year'])."-".strval($_POST['bdate_month'])."-".strval($_POST['bdate_day']);
    mysqli_query($connection, "INSERT INTO `children` (`name`, `bdate`, `sex`, `parent_id`) VALUES ('$child_name', '$child_age', '$child_sex', '$id')");
    if (isset($_POST['child_name2']) and $_POST['child_name2'] != '') {
      $child_name2 = $_POST['child_name2'];
      $child_sex2 = $_POST['child_sex2'];
      $child_age2 = strval($_POST['bdate_year2'])."-".strval($_POST['bdate_month2'])."-".strval($_POST['bdate_day2']);
      mysqli_query($connection, "INSERT INTO `children` (`name`, `bdate`, `sex`, `parent_id`) VALUES ('$child_name2', '$child_age2', '$child_sex2', '$id')");

      if (isset($_POST['child_name3']) and $_POST['child_name3'] != '') {
        $child_name3 = $_POST['child_name3'];
        $child_sex3 = $_POST['child_sex3'];
        $child_age3 = strval($_POST['bdate_year3'])."-".strval($_POST['bdate_month3'])."-".strval($_POST['bdate_day3']);
        mysqli_query($connection, "INSERT INTO `children` (`name`, `bdate`, `sex`, `parent_id`) VALUES ('$child_name3', '$child_age3', '$child_sex3', '$id')");
      }
    }
  }

  $msg_body = 'Пользователь <b>' . $parentsurname . ' ' . $parentname . ' ' . $parentsecname . '</b>';
  if (isset($_POST['child_name']) and $_POST['child_name'] != '') {
    $msg_body .= '<br><br><h2>Дети</h2><br>';
    $msg_body .= 'Ребёнок: ' . $child_name . '<br>Дата рождения: ' . $child_age;
    if (isset($_POST['child_name2']) and $_POST['child_name2'] != '') {
        $msg_body .= '<br>Ребёнок: ' . $child_name2 . '<br>Дата рождения: ' . $child_age2;
        if (isset($_POST['child_name3']) and $_POST['child_name3'] != '') {
            $msg_body .= '<br>Ребёнок: ' . $child_name3 . '<br>Дата рождения: ' . $child_age3 . '<br>';
        }
    }
  }
  $msg_body .= '<br><br>Почта ' . $email . '<br>Номер телефона: +' . $phone . '<br>Адрес: ' . $city . '<br>Информация: ' . $info . '<br>Комментарий: ' . $comment;
  sendmail(['konstantinsk@icloud.com', 'albinizm_russia@mail.ru', 'info@aniridia.ru'], 'Новый пользователь', $msg_body);

  echo "<script>window.location.replace('../../index.php')</script>";
}

if(isset($_POST['add_child_btn'])){
  $child_name = $_POST['child_name'];
  $child_age = strval($_POST['bdate_year'])."-".strval($_POST['bdate_month'])."-".strval($_POST['bdate_day']);
  $child_sex = $_POST['child_sex'];
  $parent_id = $_COOKIE['id'];

  mysqli_query($connection, "INSERT INTO `children` (`name`, `bdate`, `sex`, `parent_id`) VALUES ('$child_name', '$child_age', '$child_sex', '$parent_id')");
  if(mysqli_error($connection)){
    echo "<script>window.location.replace('profile.php?error=1')</script>";
  } else {
    echo "<script>window.location.replace('profile.php?error=0')</script>";
  };
  
}

if(isset($_POST['search_btn'])){
    $search_q=$_POST['q'];
    $search_q = trim($search_q);
    $search_q = strip_tags($search_q);
    echo "<script>window.location.replace('search.php?s=".$search_q."')</script>";
}
 
if(isset($_POST['doc-upload_submit'])){
  $file = doc_check_upload();
  $user_id = $_COOKIE['id'];
  $name = basename($file);
  mysqli_query($connection, "INSERT INTO `docs` (`src`, `name`,`user_id`) VALUES ('$file', '$name', '$user_id')");
  echo "<script>window.location.replace('admin.php?p=useful')</script>";
}
?>
