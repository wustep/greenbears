<?php
	$title = "Admins"; $page = "acp";
	require_once("header.php");
	if (isset($_SESSION['acp-id'])) {
?>
	<div id="text">
	<h2>Administrators</h2>
<?php
		if ($stmt = $mysqli->prepare("SELECT id,username,name,email,season FROM users ORDER by season=0 DESC, season DESC, id ASC")) {
			$stmt->execute();
			$stmt->store_result();
			$count = $stmt->num_rows;
			$stmt->bind_result($id, $username, $name, $email, $sid);
			if ($stmt->num_rows > 0) {
				echo "	<p><b>Name (Username) - Email - Season<br/></b>
";
				$season = "";
				while ($stmt->fetch()) {
					if ($sid != 0) {
						if ($stmt2 = $mysqli->prepare("SELECT type,year FROM seasons WHERE id = ?")) {
							$stmt2->bind_param("i", $sid);
							$stmt2->execute();
							$stmt2->bind_result($stype,$syear);
							$stmt2->fetch();
							$stmt2->close();
							$season = ($stype == 1 ? 'Track and Field ' : 'Cross Country ').$syear;
						} else {
							echo "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail1]</p>
";					
						}
					} else {
						$season = "All Seasons";
					}
				echo "	<u>$name</u> (".strtolower($username).") - $email - [$season]<br/>
";
				} // ###TEMP MESSAGE BELOW###
				echo "	<span style='color:brown'>The 'Season' above indicates the administrator's designated season.<br/>Contact Stephen Wu if you need to add, delete, or renew any administrators!</color><br/>
	<a class='sp2' href='acp'>Back to Admin CP</a></p>
";
			} else {
				echo "	<p class='err'>There was a problem with the database! Please contact the webmaster. [result-zero]</p>
";
			}
		} else {
			echo "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail]</p>
";	
		}
	} else {
		header("Location: ./");
	}
	require_once("footer.php"); 
?>