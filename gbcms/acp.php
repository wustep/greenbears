<?php
	$title = "Control Panel"; $page = "acp";
	require_once("header.php");
	if (isset($_POST['login'])) {
		if (!$_POST['username'] || !$_POST['password']) {
			$err = "All fields must be filled in!";
		} else {
			if ($stmt = $mysqli->prepare("SELECT id,username,name,password FROM users WHERE username=?")) {
				$stmt->bind_param("s", $_POST['username']);
				$stmt->execute();
				$stmt->bind_result($id, $username, $name, $password);
				$stmt->fetch();
				$stmt->close();
				if ($username) {
					$salt = substr($password, 0, 64);
					$validHash = substr($password, 64, 64);
					$testHash = hash("sha256", $salt . $_POST['password']);
					if ($testHash === $validHash) {
						$_SESSION['acp-id'] = $id;
						$_SESSION['acp-username'] = $username;
						$_SESSION['acp-name'] = $name;
						if (isset($_POST['rememberMe'])) {
							keepCookie($id, $username, $name, $_SERVER['REMOTE_ADDR']);
						}
						header("Location: ".$_SERVER['REQUEST_URI']);
						exit;
					} else {
						$err = "You have an invalid password!";
					}
				} else {
					$err = "You have an invalid username!";
				}
			} else {
				$err = "fail";
			}
		}
	}
?>
	<div id="text">
<?php
	if (!isset($_SESSION['acp-id'])) {
?>
	<h2>Admin Login</h2>
	<form method="post">
		<i class="icon-user icon-label"></i>
		<input class="field" type="text" id="username" placeholder="Username" name="username" value="<?=isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''?>" size="25"/><br/>
		<i class="icon-key icon-label"></i>
		<input class="field" type="password" id="password" placeholder="Password" name="password" value="" size="25"/><br/>
		<input type="checkbox" id="rememberMe" name="rememberMe" <?=isset($_POST['rememberMe']) ? 'checked' : ''?>/><label for="rememberMe">&nbsp;Remember me</label><br/>
		<input type="submit" name="login" value="Login" class="submit"/><?=isset($err) ? error($err,'form').'
' : ''?>
	</form>
<?php
	} else {
?>
	<h2>Control Panel</h2>
	<p class="sp">Hi, <b><?=$_SESSION['acp-name']?></b>.<br/>
	<i class="icon-home icon-label"></i>
	<a href="./">Home</a><br/>	
	<i class="icon-bullhorn icon-label"></i>
	<a href="news?new">New Announcement</a><br/>
	<i class="icon-folder-open icon-label"></i>
	<a href="afiles" target="_blank">File Manager</a> - <a href="abackups" target="_blank">Backups</a><br/>
	<i class="icon-edit icon-label"></i>
	<a href="schedule?edit">Schedule</a> - <a href="info?edit">Info</a> - <a href="seasons?edit">Seasons</a> - <a href="contact?edit">Contact</a><br/>
	<i class="icon-user icon-label"></i>
	<a href="admins">Admins</a></p>
	<p><b>Bulletin</b> <span class="small sp">- <a href="acp?edit">edit</a></span></p>
<?php
		$edit = 1;
	}
	include_once("footer.php"); 
?>