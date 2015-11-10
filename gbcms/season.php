<?php
	$page = "season";
	require_once("scripts/functions.php");	
	if ($stmt = $mysqli->prepare("SELECT seasons.id,date,sport,name,short,text FROM seasons JOIN sports ON seasons.sport=sports.id ORDER BY pos DESC")) {
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			$stmt->bind_result($id,$date,$sport,$name,$short,$text);
			$type = 1;
			$i = 0;
			if (!isset($_SESSION)) {
				 session_start();
			}
			if (isset($_GET['all'])) { // if viewing "All Seasons" page
				$title = "View Seasons";
				$nav = "	<h3>Seasons</h3>
			<p class='sp'>";
				while ($stmt->fetch()) {
					if ($i != 0) {
						$nav .= "<br/>";
					} else {
						$i++;
					}
					$nav .= "
				<a href='season?s=".$short.$date."'>".$date." ".$name."</a>";
				}
				if (isset($_SESSION['acp-id'])) {
					$nav .= "<br/><a href='seasons?manage'>Manage Seasons</a>";
				}
				$nav .= "</p>
		";
			} else if (isset($_SESSION['acp-id']) && isset($_GET['manage'])) { // if viewing "Manage Seasons" page
				$title = "Manage Seasons";
				$nav = "	<h3>Manage Seasons</h3><p>(This is disabled & coming soon!)<br/>Note that creating a new season will hide all old news.<br/>
			<form method='post' id='seasons'><table class='seasons'><thead>
				<tr style='border-bottom:1px solid black;'><th>type</th><th>date</th><th></th></tr>";
				while ($stmt->fetch()) {
					$nav .= "
				<tr><th>".$short."</th><th>".$date."</th><th><a class='del-season' href='seasons?manage&amp;del=".$id."' id='".$short.$date."'><i class='icon-ban-circle season-btn'></i></a></th></tr>";
				}
				$nav .= "
				<tr><th><select class='sinput'><option>TF</option><option>XC</option></select></th><th><input type='number' id='sdate' class='sinput' value='".date('Y')."' min='".(date('Y') - 50)."' max='".(date("Y") + 1)."'/></th><th><a href='#' class='add-season'><i class='icon-plus season-btn'></i></a></th></tr>
			</thead></table></form>
			<script type='text/javascript'>
				$(function() {
					$('#sdate').keyup(function(e){
						if ($(this).val().length >= 5) { 
							$(this).val($(this).val().substr(0, 4));
						}
						$(this).val($(this).val().replace(/\D/g,''));
					});
					$('a.add-season').click(function() {
						$('#seasons').submit();
					});
					$('a.del-season').click(function(e) {
						e.preventDefault()
						var url = $(this).attr('href');
						var confirm_box = confirm('Are you sure you want to hide season '+$(this).attr('id')+'?\');
						if (confirm_box) {
							window.location = url;
						}		
					});
				});
			</script>
		";
			} else { // otherwise display season page - some redundancy	
				$edit = 1;
				$stype = -1; // -1 = invalid/empty URL - go to current season
				// step 1: see if season is set in url, parse
				if (isset($_GET['s']) && strlen($_GET['s']) > 3) { // if season is there, check if season is valid and break it down into variables if so, otherwise get current season
					$get_type = strtoupper(substr($_GET['s'], 0, 2));
					$get_date = substr($_GET['s'], 2);
					if (ctype_alpha($get_type) && preg_match("#^[a-zA-Z0-9'-]+$#",$get_date)) {
						$stype =  1;
						
					}
				}
				// step 2: get id
				$i = 0;
				while ($stmt->fetch()) {
					if ($i == 0) {
						$new_season = $id;
						$season = $type.$date;
						$season_type = $type;
						$season_date = $date;
						if ($stype == -1) {
							break;
						}
						$i++;
					}
					if ($get_type == $short && $get_date == $date) {
						$sid = $id;
						$season = $get_type.$get_date;
						$season_type = $type;
						$season_date = $date;
						break;
					}					
				}
				if (!isset($sid)) { // step 3: if step 1 or 2 failed, get id of newest season
					$sid = $new_season;
				}
				// step 4: display nav and page
				$edit_page = $season;
				if (!isset($_GET['edit']) || (isset($_GET['edit']) && !isset($_SESSION['acp-id']))) {
					$season2 = ($type == 1 ? 'Track and Field ' : 'Cross Country ').$season_date;
					$title = $season2;
					if ($stmt2 = $mysqli->prepare("SELECT editor,edited FROM pages WHERE page=?")) { // some redundancy with this and edit.php loaded in footer, maybe fix
						$stmt2->bind_param("s", $season);
						$stmt2->execute();
						$stmt2->bind_result($editor,$edited);
						$stmt2->fetch();
						$stmt2->close();
					}
					$edit_info = isset($_SESSION['acp-id']) ? " title='Last edited by ".getName($editor)." on ".date("F j, Y \a\\t g:i A", $edited)."'" : "";					
					$nav = "	<h3 class=\"seasons\"$edit_info>$season2</h3>
";
					if ($stmt->num_rows > 1) { 
						$nav .= "	<p class=\"sp seasons-link\"><a href=\"seasons?all\">View All Seasons</a></p>
";
					}
				} else {
					$title = "Edit: ".$season;
				}
				$stmt->close();
			}
		} else {
			$nav = "	<p class='err'>Apologies, no seasons could be found!</p>
";
		}
	} else {
		$nav = "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail]</p>
";
	}		
	require_once("header.php");
	if (!isset($_GET['edit'])) {
		echo $nav;
	}
	require_once("footer.php"); 
?>