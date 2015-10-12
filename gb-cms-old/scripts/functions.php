<?php
	require_once("./config.php");
	
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);

	function getTime($type) {
		$t = time();
		if ($type != 1) {
			return $t;
		} else {
			$offset = 18000;
			if (date("I", $t) == 0) $offset -= 3600;
			return gmdate("g:i A", time()-$offset);
		}
	}
	
	function getName($id) {
		global $mysqli;
		$name = 'Someone';
		if ($stmt = $mysqli->prepare("SELECT name FROM users WHERE id=?")) {
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt->bind_result($name);
			$stmt->fetch();
			$stmt->close();
		}
		return $name;
	}
	
	function keepCookie($userid, $username, $name, $ip, $id=0) {
		global $mysqli;
		$time = time();
		$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$cookie = bin2hex(mcrypt_create_iv(15, MCRYPT_DEV_URANDOM));
		$hash = hash("sha256", $salt . $cookie); 
		$db_cookie = $salt.$hash;
		$query = ($id != 0) ? "INSERT INTO user_cookies(id, userid, username, name, cookie, ip, time) VALUES (?,?,?,?,?,?,?)" : "INSERT INTO user_cookies(userid, username, name, cookie, ip, time) VALUES (?,?,?,?,?,?)";
		if ($stmt = $mysqli->prepare($query)) {
			if ($id != 0) {
				$stmt->bind_param("iissssi", $id, $userid, $username, $name, $db_cookie, $ip, $time);
			} else {
				$stmt->bind_param("issssi", $userid, $username, $name, $db_cookie, $ip, $time);			
			}
			$stmt->execute();
			setcookie("acp", $stmt->insert_id.'.'.$cookie, time() + 86400*30);
			$stmt->close();
		}
	}
	
	function error($error, $extra="", $extra2="") { // $error = error type or error msg
		$form = "";
		if ($extra == "form") {
			$extra = "";
			$form .= " form-err";
		} else if ($extra2 == "form") {
			$extra2 = "";
			$form .= " form-err";
		}
		$err = "<span class=\"err$form\"><i class=\"icon-warning-sign\"></i> ";
		$e = $extra != "" ? "-".$extra : "";
		switch ($error) {
			case "fail":
				$err .= "There was a problem with the database! Please contact the webmaster. [stmt-fail$e]";
				break;
			case "zero":
				$err .= "There was a problem with the database! Please contact the webmaster. [result-zero$e]";
				break;
			default:
				$err .= $error;
				break;
		}
		$err .= "</span>";
		return $err;
	}
	
	function stripEditor($text) {
		$output = preg_replace('/\<p\>\&nbsp\;\<\/p\>$/', '', trim($text));
		while (preg_replace('/\<p\>\&nbsp\;\<\/p\>$/', '', trim($output)) != $output) {
			$output = preg_replace('/\<p\>\&nbsp\;\<\/p\>$/', '', trim($output));
		}
		return $output;
	}
?>