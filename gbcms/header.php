<?php
/*
(parameters)
$page = page name, ex. news?title=slug -> news
$title = title of page, appears as browser title and editting title
$edit = permits editting (null = no editting, 1 = editting)
$edit_page = specifies what page you're editting if it differs from redirect
$edit_title = title of page edited/creating
$text = the text you're editting
*/
$warn = 0; // (1/0) : gives warning about maintainence/glitches
require_once("./config.php");
?>
<?php if (!isset($page) || !isset($title)) { header("Location: ./"); echo "Something went wrong!"; exit; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo isset($title) ? $title : ''; // There should always be a $title, I think ?></title> 
	<meta name="description" content="<?php echo isset($desc) ? $desc : ''; ?>">	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
<?php if ($page == "contact") { // here so search engines don't index contact page ?>
	<meta name="robots" content="noindex">
<?php } ?>
	<link type="image/x-icon" href="favicon.ico" rel="shortcut icon"/>
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/main.css">	
	<link rel="stylesheet" href="css/xctf.css?"/>
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!--[if IE 7]>
	<link rel="stylesheet" href="css/font-awesome-ie7.min.css">
	<![endif]-->	
<?php if ($page == "news") { ?>
	<noscript><!-- if no JS support, don't use collapsable news -->
		<style type="text/css">
			.news-container-hidden {
				display: inline !important;
			}
			.arrow-down, .arrow-right {
				display: none;
			}
		</style>
	</noscript>
<?php } ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/jquery-1.9.1.min.js"><\/script>')</script>
	<script src="js/modernizr-2.6.2.min.js"></script>
	<script src="js/main.js"></script>
<?php if (date('n') == 1 || date('n') == 12) { ?>
	<script src="js/snowstorm-min.js"></script>
	<script>
		snowStorm.snowColor = '#EDFFFF';
		snowStorm.flakesMaxActive = 50;
		snowStorm.animationInterval = 50;
		snowStorm.flakeWidth = 32;
		snowStorm.flakeHeight = 32;
	</script>
<?php } ?>
</head>
<?php
	require_once("scripts/functions.php");
?>
<body>
<div id="main">
<?php
	if (!isset($_SESSION) && !mysqli_connect_errno()) {
		session_start();
	}
	if (isset($_COOKIE['acp']) && !isset($_SESSION['acp-id'])) {
		$p = explode(".", $_COOKIE['acp']);
		if ($stmt = $mysqli->prepare("SELECT userid,username,name,cookie,time FROM user_cookies WHERE id=?")) {
			$stmt->bind_param("i", $p[0]);
			$stmt->execute();
			$stmt->store_result();
			$count = $stmt->num_rows;
			$stmt->bind_result($id, $username, $name, $cookie, $time);
			$stmt->fetch();
			if ($count) {
				$salt = substr($cookie, 0, 64);
				$valid = substr($cookie, 64, 64);
				$test = hash("sha256", $salt.$p[1]);
				if ($test === $valid) {
					$dtime = time() - 86400 * 30;
					$mysqli->query("DELETE FROM user_cookies WHERE time < '$dtime'");
					if ($dtime > $time) {
						setcookie("acp",'',time()-3600);
					} else {
						$_SESSION['acp-id'] = $id;
						$_SESSION['acp-username'] = $username;
						$_SESSION['acp-name'] = $name;
						if ($stmt = $mysqli->prepare("DELETE FROM user_cookies WHERE id=?")) {
							$stmt->bind_param("i", $p[0]);
							$stmt->execute();
							$stmt->close();
						} else {
							$h_err = "There was a problem with the database! Please contact the webmaster. [h-stmt2-fail]";
						}
						keepCookie($id, $username, $name, $_SERVER['REMOTE_ADDR'], $p[0]);
					}
				}
			} else {
				setcookie("acp",'',time()-3600); 
			}
		} else {
			$h_err = "There was a problem with the database! Please contact the webmaster. [h-stmt-fail]";
		}
	}
	if (isset($h_err)) {
		echo "	<p class='err'>$h_err</p>
";
	}
	if ($page != "acp") { // don't show header on acp; page serves a few purposes, 1) checking whether to show nav, 2) checking which page to edit, 3) checking whether to show elements of footer
?>
<nav>
	<ul>
		<li<?=$page == 'news' ? ' class="highlight"' : ''?>><a href="./"><i class="icon-bullhorn"></i> News</a></li>
		<li<?=$page == 'schedule' ? ' class="highlight"' : ''?>><a href="schedule"><i class="icon-calendar"></i> Schedule</a></li>
		<li<?=$page == 'season' ? ' class="highlight"' : ''?>><a href="season"><i class="icon-star"></i> Season</a></li>
		<li<?=$page == 'records' ? ' class="highlight"' : ''?>><a href="records"><i class="icon-list-alt"></i> Records</a></li>
		<!--<li<?//=$page == 'season' ? ' class="highlight"' : ''?>><a href="photos"><i class="icon-camera"></i> Photos</a></li>-->
		<li<?=$page == 'info' ? ' class="highlight"' : ''?>><a href="info"><i class="icon-info-sign"></i> Information</a></li>
		<li<?=$page == 'contact' ? ' class="highlight"' : ''?>><a href="contact"><i class="icon-group"></i> Contact</a></li>
	</ul>
<?php if ($warn) { ?>	
	<div style="font-size:12px;padding:4px 0;color:rgb(128, 0, 0);">This site is currently undergoing some maintenance and/or changes. Some pages may not work temporarily.</div>
<?php } ?>
</nav>
<div id="text">
<?php
	}
?>