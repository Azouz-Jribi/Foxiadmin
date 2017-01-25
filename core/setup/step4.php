<?php
	require_once('../../config/config.php');

	if(isset($_POST['email']) && isset($_POST['pass'])){
		$mail = strip_tags($_POST['email']);
		$pass = md5(strip_tags($_POST['pass']));

		$sql = "CREATE TABLE admin_users ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL,password VARCHAR(255) NOT NULL)";
		$result = $db->prepare($sql);
		$result->execute();

		$sql2 = "INSERT INTO admin_users (email, password) VALUES ('".$mail."','".$pass."');";
		$result2 = $db->prepare($sql2);
		$result2->execute();

		$lastID = $db->lastInsertId();
			    
			    if($lastID > 0){
				    header('Location: step5.php');
					exit;
			    }
			    else
			    {
			    	echo "Error";
			    }
	}
	else{
		echo "Error";
	}
?>