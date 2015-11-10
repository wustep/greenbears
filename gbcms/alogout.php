<?php
	$page = "alogout";
	$title = "Logout";
	require_once("header.php");
	$_SESSION = array();
	session_destroy();
	if (isset($_COOKIE['acp'])) {
		$cookie = explode('.', $_COOKIE['acp']);
		if ($stmt = $mysqli->prepare("DELETE FROM user_cookies WHERE id=?")) {
			$stmt->bind_param("i", $cookie[0]);
			$stmt->execute();
			$stmt->close();					
		}	
		setcookie("acp",'',time()-3600);		
	}
	header("Location: ./");
	echo "<p class='scs'>You are now logged out!</p>";
	require_once("footer.php");
?>