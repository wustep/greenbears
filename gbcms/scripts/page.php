<?php
	if (!isset($page)) { header("Location: ./"); echo "Something went wrong!"; exit; }
	if (!isset($_SESSION) && !mysqli_connect_errno()) {
		session_start();
	}	
	$edit = 1;
	if (isset($_GET['edit']) && isset($_SESSION['acp-id'])) {
		$title = isset($title) ? $title : "Edit: ".ucfirst($page);
		require_once("header.php");		
	} else {
		$title = isset($title) ? $title : ucfirst($page);
		require_once("header.php");
		if ($stmt = $mysqli->prepare("SELECT editor,edited FROM pages WHERE page=?")) { // some redundancy with this and edit.php loaded in footer, maybe fix
			$stmt->bind_param("s", $page);
			$stmt->execute();
			$stmt->bind_result($editor,$edited);
			$stmt->fetch();
			$stmt->close();
		}
		$edit_info = isset($_SESSION['acp-id']) ? " title='Last edited by ".getName($editor)." on ".date("F j, Y \a\\t g:i A", $edited)."'" : "";
		echo "	<h3$edit_info>$title</h3>
";
	}
	require_once("footer.php"); 
?>