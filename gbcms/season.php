<?php
	$page = "season";
	require_once("scripts/functions.php");	
	//0 = XC, 1 = track
	if ($stmt = $mysqli->prepare("SELECT id,type,year FROM seasons ORDER BY year DESC, type ASC")) {
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			$stmt->bind_result($id,$type,$year);
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
				<a href='season?s=".($type == 1 ? 'TF' : 'XC').$year."'>".$year." ".($type == 1 ? 'Track and Field' : 'Cross Country')."</a>";
				}
				if (isset($_SESSION['acp-id'])) {
					$nav .= "<br/><a href='seasons?manage'>Manage Seasons</a>";
				}
				$nav .= "</p>
		";
			} else if (isset($_SESSION['acp-id']) && isset($_GET['manage'])) { // if viewing "Manage Seasons" page
				$title = "Manage Seasons";
				$nav = "	<h3>Manage Seasons</h3><p>(This is disabled! Please let Stephen know if you have to add seasons.)<br/>Note that creating a new season will hide all old news.<br/>
			<form method='post' id='seasons'><table class='seasons'><thead>
				<tr style='border-bottom:1px solid black;'><th>type</th><th>year</th><th></th></tr>";
				while ($stmt->fetch()) {
					$nav .= "
				<tr><th>".($type == 1 ? 'TF' : 'XC')."</th><th>".$year."</th><th><a class='del-season' href='seasons?manage&amp;del=".$id."' id='".($type == 1 ? 'TF' : 'XC').$year."'><i class='icon-ban-circle season-btn'></i></a></th></tr>";
				}
				$nav .= "
				<tr><th><select class='sinput'><option>TF</option><option>XC</option></select></th><th><input type='number' id='syear' class='sinput' value='".date('Y')."' min='".(date('Y') - 50)."' max='".(date("Y") + 1)."'/></th><th><a href='#' class='add-season'><i class='icon-plus season-btn'></i></a></th></tr>
			</thead></table></form>
			<script type='text/javascript'>
				$(function() {
					$('#syear').keyup(function(e){
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
			} else { // lotsa redundancy, remake this better later
				$edit = 1;
				$stype = -1;
				// step 1: see if season is set in url, parse
				if (isset($_GET['s'])) { // if season is there, check if season is valid and break it down into variables if so, otherwise get current season
					$get_type = strtoupper(substr($_GET['s'], 0, 2));
					$syear = substr($_GET['s'], 2, 4);
					if (($get_type == 'XC' || $get_type == 'TF') && ctype_digit($syear)) {
						$stype = $get_type == 'TF' ? 1 : 0;
					}
				}
				// step 2: get id
				$i = 0;
				while ($stmt->fetch()) {
					if ($i == 0) {
						$new_season = $id;
						$season = ($type == 1 ? 'TF' : 'XC').$year;
						$season_type = $type;
						$season_year = $year;
						if ($stype == -1) {
							break;
						}
						$i++;
					}
					if ($stype == $type && $syear == $year) {
						$sid = $id;
						$season = $get_type.$syear;
						$season_type = $type;
						$season_year = $year;
						//$page .= "?s=$get_type$syear";
						break;
					}					
				}
				if (!isset($sid)) { // step 3: if step 1 or 2 failed, get id of newest season
					$sid = $new_season;
				}
				// step 4: display nav and page
				$edit_page = $season;
				if (!isset($_GET['edit']) || (isset($_GET['edit']) && !isset($_SESSION['acp-id']))) {
					$season2 = ($type == 1 ? 'Track and Field ' : 'Cross Country ').$season_year;
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