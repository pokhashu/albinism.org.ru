<!-- комментрии подключаются к статьям
если админ, моджно удалять комментарии 
человек который написао комментарий  тоже моеже тего далить
в комментарии указывается время имя пользователя/юзернейм и сам текст -->

<script>
	$(document).ready(function() {
		$("#comment_btn").click(function(){
			$.ajax({
				url: "add_comment.php",
				type: "POST",
				data: ({article_id: $("input[name='article_id']").val(), user_id: $("input[name='user_id']").val(), text: $("textarea[name='text']").val()}),
				dataType: "html",
				success: function(data){
					if(data == "1"){
						$("textarea[name='text']").val("");
						window.location.reload();
						window.location.replace('#comments');
					} else {
						alert("Произошла ошибка");
					}
				}
			});
		});
	});
</script>

<div class="comments" method="post" id="comments">
	<?php
		if(isset($_COOKIE['id'])){
			echo '<form class="add_comment"><input type="hidden" name= "article_id" value="'.$_GET['id'].'"><input type="hidden" name= "user_id" value="'.$_COOKIE['id'].'"><textarea name="text" required></textarea><input type="button" id="comment_btn" name="add_comment-btn" value="Опубликовать"></form>';
		}
	?>

	<br>

<?php

	$comments = mysqli_query($connection, "SELECT * FROM `comments` WHERE `article_id` = " . $_GET['id'] . " ORDER BY `id` DESC");

	while($comment = mysqli_fetch_assoc($comments)){

		$username = mysqli_fetch_assoc(mysqli_query($connection, "SELECT `username` FROM `users` WHERE `id` =".$comment['user_id']))['username'];
		$text = $comment['text'];
		$pubdate = $comment['pubdate'];
		echo '<div class="comment"><span style="color: black">'.$username.'&nbsp;&nbsp;</span>  |  <span>&nbsp;&nbsp;'.$pubdate.'</span><p>&nbsp;&nbsp;&nbsp;'.$text.'</p><br>';
		if(isset($_COOKIE['id'])){
			$is_admin = mysqli_fetch_assoc(mysqli_query($connection, "SELECT `is_admin` FROM `users` WHERE `id` =".$_COOKIE['id']))['is_admin'];
		} else {
			$is_admin = 0;
		}
		
		if($comment['user_id'] == $_COOKIE['id'] || $is_admin){
			echo '<a href="delete.php?comment_id='.$comment['id'].'&article_id='.$_GET['id'].'">Удалить</a></div>';
		}
		
	}

?>
</div>