<?php
	if (!isset($title)) {
		$title = "News";
	}
	$page = "news";
	require_once("scripts/functions.php");	
	if (!isset($_SESSION)) {
		 session_start();
	}
	if ($sstmt = $mysqli->prepare("SELECT id FROM seasons ORDER BY year DESC, type ASC LIMIT 1")) { // get seasonid, put into $sid
		$sstmt->execute();
		$sstmt->store_result();
		$sstmt->bind_result($sid);
		$sstmt->fetch();
		$sstmt->close();
	}
	if (isset($_GET['id'])) {
		$content = "	<h3><a class='news-href' href='news'>News</a></h3>
";
		$id = $_GET['id'];
		if ($stmt = $mysqli->prepare("SELECT title,text,author,time FROM news WHERE id=?")) {
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($ntitle, $ntext, $author, $time); // this changed the editting title too
			$stmt->fetch();
			if ($stmt->num_rows > 0) {
				$title = $ntitle;
				$admin_options = isset($_SESSION['acp-id']) ? " - <a class='sp2' href='news?edit=$id'>Edit</a>" : "";
				$content = "	<span class='article-title'>$ntitle</span><br/><span class='article-info'> by ".getName($author)." on ".date("F j, Y", $time).".</span>
".$ntext."
	<p><a class='sp2' href='news'>Back to News</a>$admin_options</p>";
			} else {
				$content .= "	<p class='err'>Apologies, no such news entry could be found!</p>
";
			}
			$stmt->close();			
		} else {
			$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail]
";
		}
	} else if (isset($_GET['new']) && isset($_SESSION['acp-id'])) {
		$title = "Post: News";
		$content = "	<h3>Post New Announcement</h3>
";	
		if (isset($_POST['submit'])) {
			$ntitle = trim(htmlentities($_POST['title'],ENT_QUOTES));
			$ntext = stripEditor($_POST['editor']);
			$nslug = trim(preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($ntitle)),"-");
			$ntime = time();
			if ($stmt = $mysqli->prepare("INSERT INTO news(title,text,author,slug,time,season) VALUES(?,?,?,?,?,?)")) {
				$stmt->bind_param("ssisii", $ntitle, $ntext, $_SESSION['acp-id'], $nslug, $ntime, intval($_POST["season"]));
				$stmt->execute();
				if ($stmt->affected_rows == 1) {
					$scs = 1;
					$content .= "	<p class='scs'>Announcement [<b>$ntitle</b>] successfully created.</p>
";
					header("Location: news");
				} else {
					$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [result-zero]</p>
";
				}
				$stmt->close();					
			} else {
				$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail]
";				
			}
		}
		if (!isset($scs)) { // basically if form is not sent or if there's an error, display editor
			$text = '';
			$edit_title = '';
			$req = 1;
		}
	} else if (isset($_GET['edit']) && isset($_SESSION['acp-id'])) {
		$content = "	<h3>News Editor</h3>
";	
		if (isset($_POST['submit'])) {
			if ($stmt = $mysqli->prepare("UPDATE news SET text=?, title=?, slug=?, editor=?, edited=?, season=? WHERE id=?")) {
				$ntitle = trim(htmlEntities($_POST['title'],ENT_QUOTES));
				$ntext = stripEditor($_POST['editor']);
				$nslug = trim(preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($ntitle)),"-");
				$ntime = time();
				$nseason = intval($_POST['season']);
				$stmt->bind_param("ssssiii", $ntext, $ntitle, $nslug, $_SESSION['acp-id'], $ntime, $nseason, $_GET['edit']);
				$stmt->execute();
				if ($stmt->affected_rows == 1) {
					$scs = 1;
					$content .= "	<p class='scs'>Announcement [<b>$ntitle</b>] successfully edited.</p>
";
					
					header("Location: news");
				} else {
					$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [result-zero]</p>
";
				}
				$stmt->close();
			} else {
				$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail]</p>
";				
			}
		}
		$id = $_GET['edit'];
		if ($stmt = $mysqli->prepare("SELECT author,title,text,season FROM news WHERE id = ?")) {
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($author, $ntitle, $ntext, $nseason);
			$stmt->fetch();
			if ($stmt->num_rows > 0) {
				$title = "Edit: ".$ntitle;
				$text = $ntext;
				$edit_title = $ntitle;
				$req = 1;
			} else {
				$content .= "	<p class='err'>Apologies, the news entry you requested could not be found!</p>
";	
			}
			$stmt->close();
		} else {
			$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail]</p>";		
		}
	} else if (isset($_GET['delete']) && isset($_SESSION['acp-id'])) {
		$id = $_GET['delete'];
		$content = "	<h3>News Deletion</h3>
";
		if ($stmt = $mysqli->prepare("SELECT title, season FROM news WHERE id = ?")) {
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($ntitle, $nseason);
			$stmt->fetch();		
			if ($stmt->num_rows > 0) {
				if (isset($_GET['confirm'])) {
					if ($stmt = $mysqli->prepare("DELETE FROM news WHERE id = ?")) {
						$stmt->bind_param("i", $id);
						$stmt->execute();
						if ($stmt->affected_rows == 1) {
							$content .= "	<p class='scs'>Announcement [<b>$ntitle</b>] successfully deleted.</p>
";
							$scs = 1;
							header("Location: news");
						} else {
							$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [result-zero]</p>
";
						}
						$stmt->close();
					} else {
						$content = "	<p class='err'>There was a problem with the database! Please contact the webmaster. [result-zero]</p>
";
					}
				} else if ($nseason != $sid) { // Block deleting old season articles
					$content .= "	<p>Sorry, you cannot delete [<b>$ntitle</b>] because it is from a previous season.</p>
";
				} else {
					$content .= "	<p>If you're absolutely sure you want to delete [<b>$ntitle</b>], click the link below.<br/>
		<b><a href='news?delete=$id&confirm'>Delete [$ntitle]</a></b></p>
";
				}
			} else {
				$content = "	<p class='err'>Apologies, the news entry you requested could not be found!</p>
";			
			}
		} else {
			$content = "	<p class='err'>There was a problem with the database! Please contact the webmaster. [result-zero]</p>
";		
		}
	} else { // season = 0 means pinned
		$content = "	<h3>News</h3>
";
		$news_page = (isset($_GET['p']) && $_GET['p'] > 0) ? intval($_GET['p']) : 0; // current page # (starts at 0)
		$season_check = isset($_SESSION['acp-id']) ? "" : " WHERE season = '0' OR season = '$sid'"; // Show all articles for admins and "permanent articles" & current seasons for users
		if ($stmt0 = $mysqli->prepare("SELECT id FROM news$season_check")) { // TODO: maybe some redundancy here? could do 1 query + for/while loop, but more troublesome
			$stmt0->execute();
			$stmt0->store_result();
			$news_pages = ceil($stmt0->num_rows / 5) - 1; // # of pages (starts at 0)
			if ($news_page > $news_pages) { $news_page = 0; } // if specified page doesnt' exist, show first page.
			$stmt0->close();
			if ($stmt = $mysqli->prepare("SELECT id,title,text,author,time,slug,editor,edited,season FROM news$season_check ORDER BY season=0 DESC, season DESC, time DESC LIMIT ".($news_page*5).",5")) {
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($id, $ntitle, $ntext, $author, $time, $slug, $editor, $edited, $season);
				if ($stmt->num_rows > 0) {
					$i = 1;
					while ($stmt->fetch()) { // TODO: reduce redundancy?
						$pinned = ($season == 0) ? "<i class='icon-bell-alt'></i> " : ""; // Show pinned icon
						$archive = ($season != 0 && $season != $sid) ? "<i class='icon-archive'></i> " : ""; // Show archive icon with description & different color if article is from an older season
						$archive_label = ($season != 0 && $season != $sid) ? " title = 'This article is archived because it is from an older season! Only administrators will see this article listed.'" : "";
						$archived = ($season != 0 && $season != $sid) ? " archived" : "";
						$direction = ($season != 0 && $season != $sid) ? "right" : "down";
						$hidden = ($season != 0 && $season != $sid) ? " style='display: none'" : "";

						$time_info = ($season != 0 || $edited == 0) ? date("F j, Y \a\\t g:i A", $time) : date("F j, Y \a\\t g:i A", $edited); // Show article created time or edit time if pinned and edited. 
						$edit_info = " title='Last edited by ".getName($editor).(($season != 0) ? " on ".date("F j, Y \a\\t g:i A", $edited)."'": "'");
						$author_info = "<span class='news-author'>by ".getName($author)."</span>";						

						$content .= "	<div class='news-entry'>
		<div class='news-head$archived' id='title-$id'$archive_label>
		<i class='icon-chevron-$direction$archived' id='arrow-$id'></i>$pinned$archive
		<b>$ntitle</b>$author_info<span class='small-news-info'> on ".date("m/d/y", ($season == 0 && $edited != 0) ? $edited : $time)."</span>
		<span class='news-info'$edit_info>$time_info</span></div>
		<div class='news-container'$hidden id='text-".$id."'>
".$ntext."
			<div class='news-links sp'>";
						if (isset($_SESSION['acp-id'])) {
							$content .= "
				<a href='news?edit=".$id."'>Edit</a>";		
						}
						if (isset($_SESSION['acp-id']) && ($season == 0 || $season == $sid)) {
							$content .= "
				<a href='news?delete=".$id."'>Delete</a>";
						}
						$content .= "
				<a href='news/$id/$slug'>Permalink</a>
			</div>
		</div>
	</div>
";
						$i++;
					}
					if ($news_pages > 0) {
						$content .= "	<div class='news-nav sp'>
";
						if ($news_page != $news_pages) {
							$content .= "		<a href='news?p=".($news_page+1)."'><span class='news-nav-btn'><i class='icon-level-down icon-flip-horizontal'></i> Older Entries</span></a>
";
						}					
						if ($news_page != 0) {
							$newer = (($news_pages - 1) != 0) ? "?p=".($news_page-1) : "";
							$content .= "		<a href='news$newer'><span class='news-nav-btn'><i class='icon-level-up icon-flip-horizontal'></i> Newer Entries</span></a>
";
						}
						$content .= "	</div>
";
					}
				} else {
					$content .= "	<p class='err'>Apologies, no news entries could be found!</p>
";
				}
				$stmt->close();			
			} else {
				$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail]</p>
";
			}
		$content .= "	<script type=\"text/javascript\">
		$(function() {
			$(\".news-head\").mousedown(function(){ return false; })
			$(\".news-head\").click(function() {
				var parts = $(this).attr(\"id\").split('-');
				$(\"#arrow-\" + parts[1]).toggleClass(\"icon-chevron-right icon-chevron-down\");
				$(\"#text-\" + parts[1]).toggle();
			});
		});
	</script>
";
		} else {
				$content .= "	<p class='err'>There was a problem with the database! Please contact the webmaster. [stmt-fail]</p>
";		
		}
	}
	include_once("header.php");
	echo $content;
	if (isset($req)) { require_once("scripts/editor.php"); }
	include_once("footer.php"); 
?>