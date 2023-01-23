<?php 
 
	function getRandomFileName($path){
		$path = $path ? $path . '/' : '';
		do {
			$name = md5(microtime() . rand(0, 9999));
			$file = $path . $name;
		} while (file_exists($file));
		return $name;
	}

	function reArrayFiles(&$file_post) {

	$file_ary = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);

	for ($i=0; $i<$file_count; $i++) {
		foreach ($file_keys as $key) {
			$file_ary[$i][$key] = $file_post[$key][$i];
		}
	}

    return $file_ary;
}

	function image_check_upload(){
		$imgs_srcs = array();

		foreach(reArrayFiles($_FILES['image']) as $files){
			if(!$files['error']){
				if (isset($files)){
					$image = $files;
					// Получаем нужные элементы массива "image"
					$fileTmpName = $files['tmp_name'];
					$errorCode = $files['error'];
					// Проверим на ошибки
					if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($fileTmpName)) {
						// Массив с названиями ошибок
						$errorMessages = [
							UPLOAD_ERR_INI_SIZE   => 'Размер файла превысил значение upload_max_filesize в конфигурации PHP.',
							UPLOAD_ERR_FORM_SIZE  => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE в HTML-форме.',
							UPLOAD_ERR_PARTIAL    => 'Загружаемый файл был получен только частично.',
							UPLOAD_ERR_NO_FILE    => 'Файл не был загружен.',
							UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка.',
							UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
							UPLOAD_ERR_EXTENSION  => 'PHP-расширение остановило загрузку файла.',
						];
						// Зададим неизвестную ошибку
						$unknownMessage = 'При загрузке файла произошла неизвестная ошибка.';
						// Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
						$outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
						// Выведем название ошибки
						die($outputMessage);
					} else {
						// echo 'Ошибок нет.';
					}
				};
				// Создадим ресурс FileInfo
				$fi = finfo_open(FILEINFO_MIME_TYPE);
				// Получим MIME-тип
				$mime = (string) finfo_file($fi, $fileTmpName);
				// Проверим ключевое слово image (image/jpeg, image/png и т. д.)
				if (strpos($mime, 'image') === false) die('Можно загружать только изображения.');
				$image = getimagesize($fileTmpName);
				var_dump($image);
				// Результат функции запишем в переменную
				$image = getimagesize($fileTmpName);
				 
				// Зададим ограничения для картинок
				$limitBytes  = 1024 * 1024 * 20;
				$limitWidth  = 7680;
				$limitHeight = 4320;
				 
				// Проверим нужные параметры
				if (filesize($fileTmpName) > $limitBytes) die('Размер изображения не должен превышать 5 Мбайт.');
				if ($image[1] > $limitHeight)             die('Высота изображения не должна превышать 768 точек.');
				if ($image[0] > $limitWidth)              die('Ширина изображения не должна превышать 1280 точек.');
				// Сгенерируем новое имя файла на основе MD5-хеша
				$name = getRandomFileName($fileTmpName);
				// Сгенерируем расширение файла на основе типа картинки
				$extension = image_type_to_extension($image[2]);
				 
				// Сократим .jpeg до .jpg
				$format = str_replace('jpeg', 'jpg', $extension);
				 
				// Переместим картинку с новым именем и расширением в папку /upload
				if (!move_uploaded_file($fileTmpName, '../imgs//upload/' . $name . $format)) {
				  die('При записи изображения на диск произошла ошибка.');
				}
				 
				array_push($imgs_srcs, '../imgs//upload/' . $name . $format);
			} else {
				continue;
			}
		}

		return $imgs_srcs;

	}

	function doc_check_upload(){
		if (isset($_FILES['doc'])){
			$file = $_FILES['doc'];
			// Получаем нужные элементы массива "image"
			$fileTmpName = $_FILES['doc']['tmp_name'];
			$errorCode = $_FILES['doc']['error'];
			// Проверим на ошибки
			if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($fileTmpName)) {
				// Массив с названиями ошибок
				$errorMessages = [
					UPLOAD_ERR_INI_SIZE   => 'Размер файла превысил значение upload_max_filesize в конфигурации PHP.',
					UPLOAD_ERR_FORM_SIZE  => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE в HTML-форме.',
					UPLOAD_ERR_PARTIAL    => 'Загружаемый файл был получен только частично.',
					UPLOAD_ERR_NO_FILE    => 'Файл не был загружен.',
					UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка.',
					UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
					UPLOAD_ERR_EXTENSION  => 'PHP-расширение остановило загрузку файла.',
				];
				// Зададим неизвестную ошибку
				$unknownMessage = 'При загрузке файла произошла неизвестная ошибка.';
				// Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
				$outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
				// Выведем название ошибки
				die($outputMessage);
			} else {
				// echo 'Ошибок нет.';
			}
		};
		// // Проверим ключевое слово image (image/jpeg, image/png и т. д.)
		if (strpos($_FILES['doc']['type'], 'text') === false AND strpos($_FILES['doc']['type'], 'application') === false) die('Можно загружать только некоторые файлы.');
		// Сгенерируем новое имя файла на основе MD5-хеша
		$name = getRandomFileName($fileTmpName);
		 
		// Переместим картинку с новым именем и расширением в папку /upload
		if (!move_uploaded_file($fileTmpName, '../upload-docs/' . basename($_FILES['doc']['name']))) {
		  die('При записи документа на диск произошла ошибка.');
		}
		 
		return '../upload-docs/' . basename($_FILES['doc']['name']);

	}


function sendmail($to = array(), $subject, $body){
	require_once('../phpmailer/PHPMailerAutoload.php');

	$mail = new PHPMailer;
	$mail->CharSet = 'utf-8';

	//$mail->SMTPDebug = 3;                               

	$mail->isSMTP();                                      
	$mail->Host = 'smtp.mail.ru';                         
	$mail->SMTPAuth = true;                               
	$mail->Username = 'info.albinism.org.ru@mail.ru';     
	$mail->Password = 'GpQdE3tHNRF3Rh3wB3S6';             
	$mail->SMTPSecure = 'ssl';                            
	$mail->Port = 465;                                    
	
	$mail->setFrom('info.albinism.org.ru@mail.ru');       
	$email_i = 0;
	while($email_i < count($to)){
	    echo $to[$email_i];
	    $mail->addBCC($to[$email_i]);
	    $email_i ++;
	}
	$mail->isHTML(true);
	$mail->Subject = $subject;
	$mail->Body    = $body;
	$mail->AltBody = '';
	$mail->send();
}

?>