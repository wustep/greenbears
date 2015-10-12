<?php
	if (!isset($title) || !isset($text)) { header("Location: ./"); echo "Something went wrong!"; exit; }
	if ((isset($edit_title) && $edit_title != '') || (!isset($edit_title) && $page != "acp")) { // don't show if you're posting something new (edit_title is empty) or you're on the ACP (bulletin)
?>
	<p>You are currently editing <b><?=isset($edit_title) ? $edit_title : (isset($edit_page) ? $edit_page : ucfirst($page))?>.</b></p>
<?php
	}
?>
	<script type="text/javascript" src="scripts/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="scripts/ckfinder/ckfinder.js"></script>
	<form method="post">
<?php
	if (isset($edit_title)) { // edit_title enabled = ''
?>
		<input placeholder="Announcement Title" type="text" name="title" id="title" value="<?=$edit_title?>" size="70"/>
<?php
		if ($stmts = $mysqli->prepare("SELECT id,type,year FROM seasons ORDER BY year DESC, type ASC")) {
			echo "		<select name='season'>
";
			$stmts->execute();
			$stmts->store_result();
			if ($stmts->num_rows > 0) {
				$stmts->bind_result($id,$type,$year);
				$selected1 = (isset($nseason) && $nseason == 0) ? " class='select' selected" : ""; // If the news article is pinned, select pinned
				echo "		<option value='0'$selected1>Pinned</option>
";
				while ($stmts->fetch()) {
					$option_season = ($type == 1 ? 'Track and Field ' : 'Cross Country ').$year;
					$selected2 = ((isset($nseason) && $nseason == $id) || (!isset($nseason) && $id == $sid)) ? " class='select' selected" : ""; // Select news' season or current season if not available
					echo "		<option value='$id'$selected2>$option_season</option>
";
				}
				echo "		</select>
";
			} else {
				echo "<br/>".error("zero", 0);
			}
		} else {
			echo "<br/>".error("fail", 0);
		} 
?></br>
<?php
	}
?>
		<textarea cols="" rows="" style="margin-left:0px" id="editor" name="editor" id="editor">
<?php
	echo ($text != "") ? $text : ""; // Show existing text or empty paragraph tags.
?>
		</textarea>
		<script type="text/javascript">
			var editor = CKEDITOR.replace( 'editor', {contentsCss : ['css/normalize.css','css/main.css','css/xctf.css','css/font-awesome.min.css','css/ckeditor.css']});
			CKFinder.setupCKEditor( editor, 'scripts/ckfinder/' );
		</script><br/>
		<input type="submit" value="submit" name="submit" id="submit"/><input type="button" value="help" onclick="window.open('ahelp')"/><input type="button" value="backups" onclick="window.open('backups/')"/><input type="button" value="cancel" onclick="window.location.href='<?=$page?>'"/>
	</form>
	<script type="text/javascript">
		$(function() {
			var submits = 0;
			$('#submit').on('click', function() {
				submits = 1;
			});
			$(window).bind('beforeunload', function() {
				if (submits == 0) return 'Are you sure you want to navigate away from this page? Your progress may be lost!';
			});
<?php if ($page != "acp") { ?>
			$('#submit').click(function(e) {
				if (editor.getData().replace(/<[^>]*>|\s/g, '').length < 5) {
					alert("Text is too short to submit.");
					e.preventDefault();
				} 			
<?php if (isset($edit_title)) { ?>
				else if ($('#title').val().length < 3) {
					alert("Title is too short to submit.");
					e.preventDefault();
				}
<?php } ?>
			});
<?php } ?>
		});
	</script>
