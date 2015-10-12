<?php
	$title = "Admin Creation"; $page = "acp";
	require_once("header.php");
	$enable = 0; // 4 = works for anyone with link 3 = works at acreate?s=SECRET in header, 2 = works for all hosts as logged-in admin, 1 = only localhost as admin, 0 = nobody
	$secret = "SECRETPASSWORD"; // change secret for $enable = 3
	if (($enable == 1 && $_SERVER['SERVER_ADDR'] == '127.0.0.1') || ($enable == 2 && isset($_SESSION['acp-id'])) || ($enable == 3 && isset($_GET['s']) && $_GET['s'] == $secret) || ($enable == 4)) {
		if (isset($_POST['acreate'])) {
			$err = array();
			if (strlen($_POST['username']) < 3 || strlen($_POST['username']) > 25) {
				$err[] = 'Your username must be between 3 and 25 characters.';
			} else if (!preg_match("/^[a-zA-Z0-9 ._-]+$/", $_POST['username'])) {
				$err[] = 'Your username may only contain alphanumeric characters, dashes, periods, and spaces.';
			} else {
				if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE username=?")) {
					$stmt->bind_param("s", $_POST['username']);
					$stmt->execute();
					$stmt->bind_result($id);
					$stmt->fetch();
					$stmt->close();
					if ($id) {
						$err[] = "Your username is already in use.";
					}
				}
			}
			if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 40) {
				$err[] = 'Your display name must be between 3 and 40 characters.';
			} else if (!preg_match("/^[a-zA-Z0 -]+$/", $_POST['name'])) {
				$err[] = 'Your display name may only contain alphanumeric characters, dashes, and spaces.';
			} else {
				if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE name=?")) {
					$stmt->bind_param("s", $_POST['name']);
					$stmt->execute();
					$stmt->bind_result($id);
					$stmt->fetch();
					$stmt->close();
					if ($id) {
						$err[] = "Your username is already in use.";
					}
				}
			}
			if (strlen($_POST['password']) < 4 || strlen($_POST['password']) > 50) {
				$err[] = 'Your password must be between 4 and 50 characters.';
			} else if ($_POST['password'] != $_POST['vpassword']) {
				$err[] = 'Passwords do not match.';
			}
			if (!preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $_POST['email'])) {
				$err[] = 'Your email is invalid.';
			}
			if (!count($err)) {
				$username = $_POST['username'];
				$name = $_POST['name'];
				$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
				$hash = hash("sha256", $salt . $_POST['password']); 
				$password = $salt.$hash;
				$email = $_POST['email'];
				if ($stmt = $mysqli->prepare("INSERT INTO users(username,name,password,email) VALUES(?,?,?,?)")) {
					$stmt->bind_param("ssss", $username, $name, $password, $email);
					$stmt->execute();
					if ($stmt->affected_rows == 1) {
						$scs = "	<p class='scs'>Account successfully created.</p>
";
					} else {
						$err[] = 'zero';
					}
					$stmt->close();					
				} else {
					$err[] = "fail";
				}
			}
			if (count($err)) {
				$errm = "	<p>";
				$i = 0;
				foreach ($err as $error) {
					if ($i == 0) {
						$i++;
					} else {
						$errm .= "
	";
					}
					$errm .= error($error)."<br/>";
				}
				$errm .= "</p>
";
			}
		}
?>
	<div id="text">
	<h2>Admin Creation</h2>
	<p>Create an administrator account by filling the form below!<br/>
	The Admin Control Panel is available <a class='sp2' href='acp'>here</a>.<br/>
	<form method="post" id="acreate">
		<i class="icon-user icon-label"></i>	
		<input class="field" type="text" name="username" id="username" placeholder="Username" value="<?=isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''?>" size="30"/>
		<i class="icon-question-sign icon-help" title="Username is used solely for logging in and is shown to other administrators."></i><br/>
		<i class="icon-user icon-label"></i>
		<input class="field" type="text" name="name" id="name" placeholder="Display Name" value="<?=isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" size="30"/>
		<i class="icon-question-sign icon-help" title="Display name appears on announcemnets, edits, etc. Use just your first and last name!"></i><br/>
		<i class="icon-lock icon-label"></i>
		<input class="field" type="password" name="password" id="password" placeholder="Password" value="" size="30"/><br/>
		<i class="icon-lock icon-label"></i>
		<input class="field" type="password" name="vpassword" id="vpassword" placeholder="Verify Password" value="" size="30"/><br/>
		<i class="icon-envelope icon-label"></i>
		<input class="field" type="email" name="email" id="email" placeholder="Email Address" value="<?=isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''?>" size="30"/>
		<i class="icon-question-sign icon-help" title="E-mail is for contact purposes and is shown to other administrators."></i><br/>
		<script type="text/javascript">
			var RecaptchaOptions = {
				theme : 'clean'
			};
		</script>
		<input class="submit" type="submit" name="acreate" id="acreate" value="Create"/>
	</form>
<?php
		if (isset($_POST['acreate'])) { echo isset($scs) ? $scs : $errm; }
	} else {
		header("Location: ./");
	}
	require_once("footer.php"); 
?>