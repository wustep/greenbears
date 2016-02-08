<?php // this page is only used if $edit = 1 while footer is loaded; it's not used for any queries that DON'T use the pages table (news)
	require_once("functions.php");
	if (!isset($title) || !isset($page)) { header("Location: ./"); echo "Something went wrong!"; exit; }
	$page2 = isset($edit_page) ? $edit_page : $page; // if page has special $edit_page, then edit that instead, but redirect to the $page
	if ($stmt = $mysqli->prepare("SELECT text FROM pages WHERE page=?")) {
		$stmt->bind_param("s", $page2);
		$stmt->execute();
		$stmt->bind_result($text);
		$stmt->fetch();
		$stmt->close();
	}
	if (!isset($text)) $text = "<p></p>"; // Default text for pages that haven't been edited
	if (isset($_GET['edit']) && isset($_SESSION['acp-id'])) {
		if (isset($_POST['submit'])) {
				if ($astmt = $mysqli->prepare("SELECT editor FROM pages WHERE page=?")) {
					$astmt->bind_param("s", $page2);
					$astmt->execute();
					$astmt->store_result();
					$rows = $astmt->num_rows;
					$astmt->close();
					if ($rows > 0) {
						if ($stmt = $mysqli->prepare("UPDATE pages SET text=?, editor=?, edited=? WHERE page=?")) {
							$stmt->bind_param("siis", stripEditor($_POST['editor']), $_SESSION['acp-id'], time(), $page2);
							$stmt->execute();
							$stmt->close();
						}	
					} else {
						if ($stmt = $mysqli->prepare("INSERT INTO pages (page,text,editor,edited) VALUES (?,?,?,?)")) {
							$stmt->bind_param("ssii", $page2, stripEditor($_POST['editor']), $_SESSION['acp-id'], time());
							$stmt->execute();
							$stmt->close();
						}					
					}
				}			
			header("Location: $page");
			exit;
		}
		require_once("editor.php");
	} else {
		echo $text;
	}
?>