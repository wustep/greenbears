<?php
	if (isset($_POST['password'])) {
		$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$hash = hash("sha256", $salt . $_POST['password']); 
		$password = $salt.$hash;
		echo $password;			
	}
?>
	<form method="post" id="pw">
		<input class="field" type="password" name="password" id="password" placeholder="Password" value="" size="30"/><br/>
		<i class="icon-lock icon-label"></i>
		<input class="submit" type="submit" name="acreate" id="acreate" value="Go"/>
	</form>