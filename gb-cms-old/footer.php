<?php 
	if (!isset($page) && !isset($title) && !isset($acp)) { header("Location: ./"); exit; } 
	if (isset($edit)) { require_once("scripts/edit.php"); } 
	if (isset($_SESSION['acp-id'])) {
?>
	<footer>
		<p class="sp"><?php
		if (isset($page) && $page != 'acp') {
			echo "<a href='acp'>Admin CP</a> | ";
			if (isset($edit) && !isset($_GET['edit'])) { // if edit parameter is enabled and edit isn't already in URL
				$which = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ? '&' : '?'; // add & if there are existing parameters, otherwise add ?
				echo "<a href ='".basename($_SERVER['REQUEST_URI']).$which."edit'>Edit this page</a> | ";
			}
			if ($page == 'news' && (!isset($_GET['edit']) && !isset($_GET['new']))) { // if page is news and it's not on new / edit pages
				echo "<a href='news?new'>New Announcement</a> | ";
			}
		}
		echo "<a href='alogout'>Logout</a>";
?></p>
	</footer>
<?php
	}
?>
	<!--[if lt IE 8]>
		<p style="font-size:12px;margin-bottom:0;">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
</div>
</div>
<?php
	if (!isset($_SESSION['acp-id'])) {
	// Put Google Analytics or other tracking here if desired!
	}
?>
</body>
</html>