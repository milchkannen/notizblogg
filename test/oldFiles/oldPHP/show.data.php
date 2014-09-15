<?php
/**
 * Created by IntelliJ IDEA.
 * User: ak
 * Date: 18/06/14
 * Time: 15:43
 */


// wird durch einzelne phps ersetzt, die json liefern, das mit jquery.show verarbeitet wird


function show($type, $query, $access, $viewer)
{
	switch($viewer) {
		case 'desk';
			$openViewer = '<div class=\'desk\'>';
			$openPartLeft = '<div class=\'left_side\'>';
			$openPartRight = '<div class=\'right_side\'>';
			break;

		default;		// wall
			$openViewer = '<div class=\'wall\'>';
			$openPartLeft = '<div>';
			$openPartRight = '<div>';

	}
	$close = '</div>';

	switch ($type) {
		case 'source';
			echo $openViewer;
			echo $openPartLeft;
				$source = NEW show();
				$source->id = $query;
				$source->access = $access;
				$source->type = $type;
				$source->showData();
			echo $close;
			echo $openPartRight;
			echo '<div class=\'timeline_container\'><div class=\'timeline\'><div class=\'plus\'></div></div></div>';
				$source = NEW get();
				$source->id = $query;
				$source->access = $access;
				$source->data = json_decode($source->getData(), true);
			// get the notes
			if(!empty($source->data['notes'])) {
				$i = 0;
				while ($i < count($source->data['notes'])) {
					$note = NEW show();
					$note->id = $source->data['notes'][$i];
					$note->access = $access;
					$note->type = 'note';
					$note->showData();
					$i++;
				}

			}
			echo $close; 		// part right
			echo $close; 		// viewer type
			break;

		case 'note';
			echo $openViewer;
				$note = NEW show();
				$note->id = $query;
				$note->access = $access;
				$note->type = $type;
				$note->showData();
			echo $close;
			break;

		case 'label';
			// 1. get the label name
			$label = '';
			condb('open');
			$sql = mysql_query("SELECT labelName FROM label WHERE labelID=" . $query);
			condb('close');
			$num_results = mysql_num_rows($sql);
			if ($num_results > 0) {
				while ($row = mysql_fetch_object($sql)) {
					$label = $row->labelName;
				}
				echo $openViewer;
				// 2. get the sources
				condb('open');
				$sql = mysql_query("SELECT sourceID FROM rel_source_label WHERE labelID=" . $query);
				condb('close');
				$num_results = mysql_num_rows($sql);
				if($num_results > 0) {
					if($num_results > 1) {
						echo '<div class=\'note\'><h3 id="source">'. $num_results . ' Sources with the label \'' . $label . '\'</h3></div>';
					} else {
						echo '<div class=\'note\'><h3 id="source">'. $num_results . ' Source with the label\'' . $label . '\'</h3></div>';
					}
					while ($row = mysql_fetch_object($sql)) {
						$source = NEW show();
						$source->id = $row->sourceID;
						$source->access = $access;
						$source->type = 'source';
						$source->showData();
					}
				} else {
					echo '<div class=\'note\'><h3 id="source">No Sources with the label \'' . $label . '\'</h3></div>';
				}
				echo $close;

				echo $openViewer;
				// 2. get the note to this label
				condb('open');
				$sql = mysql_query("SELECT noteID FROM rel_note_label WHERE labelID=" . $query);
				condb('close');
				$num_results = mysql_num_rows($sql);
				if($num_results > 0) {
					if($num_results > 1) {
						echo '<div class=\'note\'><h3 id="note">'. $num_results . ' Notes with the label \'' . $label . '\'</h3></div>';
					} else {
						echo '<div class=\'note\'><h3 id="note">'. $num_results . ' Note with the label\'' . $label . '\'</h3></div>';
					}
					while ($row = mysql_fetch_object($sql)) {
						$note = NEW show();
						$note->id = $row->noteID;
						$note->access = $access;
						$note->type = 'note';
						$note->showData();
					}
				} else {
					echo '<div class=\'note\'><h3 id="note">No Notes with the label \'' . $label . '\'</h3></div>';
				}
				echo $close;
			}
			break;

		case 'author';
			// 1. get the author name
			$author = '';
			condb('open');
			$sql = mysql_query("SELECT authorName FROM author WHERE authorID=" . $query . ";");
			condb('close');
			$num_results = mysql_num_rows($sql);
			if ($num_results > 0) {
				while ($row = mysql_fetch_object($sql)) {
					$author = $row->authorName;
				}

				//2. get the sources
				condb('open');
				$sql = mysql_query("SELECT sourceID FROM rel_source_author WHERE authorID=" . $query . ";");
				condb('close');
				$num_results = mysql_num_rows($sql);
				echo '<div class=\'note\'><h3 id="source">Sources with the author \'' . $author . '\': # ' . $num_results . '</h3></div>';
				if ($num_results > 0) {
					while ($row = mysql_fetch_object($sql)) {
						show('source', $row->sourceID, $access, $viewer);
					}
				}

			}
			break;

		case 'collection';
			show('source', $query, $access, 'desk');

			// get the other sources
			$source = NEW get();
			$source->id = $query;
			$source->access = $access;
			$source->data = json_decode($source->getSource(), true);
			// get the sources
			if(!empty($source->data['sources'])) {
				$i = 0;
				while ($i < count($source->data['sources'])) {
					show('source', $source->data['sources'][$i], $access, 'desk');
					$i++;
				}
			}
			break;

		case 'search';
			// show sources
			condb('open');
			$sql = mysql_query("SELECT sourceID FROM source WHERE sourceTitle LIKE '%".$query."%' OR sourceSubtitle LIKE '%".$query."%' OR sourceNote LIKE '%".$query."%' OR sourceYear = '".$query."';");
			condb('close');
			$num_results = mysql_num_rows($sql);
			echo $openViewer;
			if($num_results > 0) {
				if($num_results > 1) {
					echo '<div class=\'note\'><h3 id="source">'. $num_results . ' Sources with \'' . $query . '\'</h3></div>';
				} else {
					echo '<div class=\'note\'><h3 id="source">'. $num_results . ' Source with \'' . $query . '\'</h3></div>';
				}
				while ($row = mysql_fetch_object($sql)) {
					$source = NEW show();
					$source->id = $row->sourceID;
					$source->access = $access;
					$source->type = 'source';
					$source->showData();
					//$source = NEW source();
					//$source->showSource($row->sourceID, $access);
				}
			} else {
				echo '<div class=\'note\'><h3 id="source">No sources with \'' . $query . '\'</h3></div>';
			}
			echo $close;

			// show notes
			condb('open');
			$sql = mysql_query("SELECT noteID FROM note WHERE noteTitle LIKE '%".$query."%' OR noteContent LIKE '%".$query."%'");
			condb('close');
			$num_results = mysql_num_rows($sql);
			echo $openViewer;
			if($num_results > 0) {
				if($num_results > 1) {
					echo '<div class=\'note\'><h3 id="note">'. $num_results . ' Notes with \'' . $query . '\'</h3></div>';
				} else {
					echo '<div class=\'note\'><h3 id="note">'. $num_results . ' Note with \'' . $query . '\'</h3></div>';
				}
				while ($row = mysql_fetch_object($sql)) {
					$note = NEW show();
					$note->id = $row->noteID;
					$note->access = $access;
					$note->type = 'note';
					$note->showData();
				}
			} else {
				echo '<div class=\'note\'><h3 id="note">No notes with \'' . $query . '\'</h3></div>';
			}
			echo $close;

			// show authors
			condb('open');
			$sql = mysql_query("SELECT * FROM author WHERE authorName LIKE '%".$query."%'");
			condb('close');
			$num_results = mysql_num_rows($sql);
			echo $openViewer;
			if($num_results > 0) {
				if($num_results > 1) {
					echo '<div class=\'note\'><h3 id="author">'. $num_results . ' Authors with \'' . $query . '\'</h3></div>';
				} else {
					echo '<div class=\'note\'><h3 id="author">'. $num_results . ' Author with \'' . $query . '\'</h3></div>';
				}
				while ($row = mysql_fetch_object($sql)) {
					echo '<div class=\'note\'><div class=\'text\'><a href=\'?author=' . $row->authorID . '\'>' . $row->authorName . '</a></div></div>';
				}
			} else {
				echo '<div class=\'note\'><h3 id="author">No authors with \'' . $query . '\'</h3></div>';
			}
			echo $close;

			// show labels
			condb('open');
			$sql = mysql_query("SELECT * FROM label WHERE labelName LIKE '%".$query."%'");
			condb('close');
			$num_results = mysql_num_rows($sql);
			echo $openViewer;
			if($num_results > 0) {
				if($num_results > 1) {
					echo '<div class=\'note\'><h3 id="label">'. $num_results . ' Labels with \'' . $query . '\'</h3></div>';
				} else {
					echo '<div class=\'note\'><h3 id="label">'. $num_results . ' Label with \'' . $query . '\'</h3></div>';
				}
				while ($row = mysql_fetch_object($sql)) {
					echo '<div class=\'note\'><div class=\'text\'><a href=\'?label=' . $row->labelID . '\'>' . $row->labelName . '</a></div></div>';
				}
			} else {
				echo '<div class=\'note\'><h3 id="label">No Labels with \'' . $query . '\'</h3></div>';
			}
			echo $close;
			break;

		default:

			if($access == 'public') {
				$query = 'AND note.notePublic = 1';
			} else { // ($access !== 'public' && $id === 'all')
				$query = '';
			}
			condb('open');
			$sql = mysql_query("SELECT bib.bibID, note.noteID FROM bib, note WHERE bib.noteID = note.noteID " . $query . ";");

			//echo "SELECT bib.bibID, note.noteID FROM bib, note WHERE bib.noteID = note.noteID " . $query . ";";
			condb('close');
			$num_results = mysql_num_rows($sql);
			if($num_results > 0) {
				echo $openViewer;
				while ($row = mysql_fetch_object($sql)) {


					$source = NEW show();
					$source->id = $row->noteID;
					$source->access = $access;
					$source->type = 'source';
					$source->showData();

					//$source = NEW source();
					//$source->showSource($row->sourceID, $access);
				}
				echo $close;
			}
	}

}







function show_oldversion($type, $part, $partID, $access){

	if($access == 'public'){
		$gpa = "AND notePublic = 1";
	} else {
		$gpa = "";
	}

	$count = 200;
	$sql = '';
	if(!isset($_GET['page'])){
		$_GET['page'] = 1;
	}
	$offset = ($_GET['page'] - 1) * $count;
	if($type == 'note'){
		$orderBy = "pageStart, ".$type."Title, date DESC LIMIT ".$offset;
	} else {
		$orderBy = $type."Typ, " . $type."Title, date DESC LIMIT ".$offset;
	}
	if($access == 'public' && $type == 'note'){
		$gpa = "AND notePublic = 1";
	} else {
		$gpa = "";
	}

	switch($part){
		// case 1:n
		case "category";
		case "project";
		case "source";
			$sql = mysql_query("SELECT ".$type."ID FROM ".$type." WHERE ".$type.$part." = ".$partID." ".$gpa." ORDER BY ".$orderBy.", ".$count."");
			$partIndex = $part;
			$titleIndexLeft = linkIndex($type, $part, $partID);
			break;

		// case m:n
		case "tag";
		case "author";
			$relTable = "rel_".$type."_".$part;
			$sql = mysql_query("SELECT ".$part."Name, ".$type."ID FROM ".$part.", ".$relTable." WHERE ".$part.".".$part."ID = ".$relTable.".".$part."ID AND ".$relTable.".".$part."ID = '".$partID."' ORDER BY ".$part."Name");
			$partIndex = $part;
			$titleIndexLeft = linkIndex($type, $part, $partID);
			break;

		case "excerpt";
		case "collection";
			$sql = mysql_query("SELECT ".$type."ID FROM ".$type." WHERE ".$type."ID = ".$partID." ".$gpa.";");
			$partIndex = $part;
			$titleIndexLeft = linkIndex('note', 'source', $partID);
			break;

		case "search";
			$partID = htmlentities($partID,ENT_QUOTES,'UTF-8');
			if($type == 'note'){
				$sql = mysql_query("SELECT noteID FROM note WHERE (`noteTitle` LIKE '%".$partID."%' OR `noteContent` LIKE '%".$partID."%' OR noteSourceExtern LIKE '%".$partID."%') ".$gpa." ORDER BY date DESC");

			} else {
				$sql = mysql_query("SELECT sourceID FROM source WHERE (`sourceTitle` LIKE '%".$partID."%' OR `sourceSubtitle` LIKE '%".$partID."%' OR sourceNote LIKE '%".$partID."%' OR sourceName LIKE '%".$partID."%') ".$gpa." ORDER BY date DESC");
			}
			$partIndex = $part."ed";
			$titleIndexLeft = "'".$partID."'";
			break;

		case "export";
			$sql = mysql_query("SELECT ".$type."ID FROM ".$type." WHERE ".$type."Typ != 0 ORDER BY sourceTyp DESC");
			$partIndex = $part;
			$partName = getIndex($type, $part, $partID);
			$year = date("Y");
			$date = date("Ymd");
			$filename = $partName . "_" . $date . ".bib";
			//$tmpPath = split('/notizblogg', SITE_URL);
			$backuppath = "export/bibtex/" . $filename;
			$downloadurl = DOWNLOAD_URL . "/bibtex/" . $filename;
			$titleIndexLeft = "<a href='".$downloadurl."'>Download bibTex file</a>";
			if(!file_exists($backuppath)){
				$copyRight = html_entity_decode("%% %% %% %% %% %% %% %% %% %% %% %% %% %% %%\n%% This bibFile was created with\n%% Notizblogg &copy; by\n%% Andr&eacute; Kilchenmann | 2006-". $year ." \n%%\n%% -&gt; ak@notizblogg.ch\n%% -&gt; http://notizblogg.ch\n%% %% %% %% %% %% %% %% %% %% %% %% %% %% %%\n\n",ENT_NOQUOTES,'ISO-8859-15');
				fopen($backuppath, 'w+');
				if (!$handle = fopen($backuppath, 'w')) {
					echo "Cannot open file (".$backuppath.")";
					exit;
				}
				if (fwrite($handle, $copyRight) === FALSE) {
					echo "Cannot write to file (".$backuppath.")";
					exit;
				}
			}
			break;
		//default:

	}

	// hier noch if $sql existiert
	$countResult = mysql_num_rows($sql);
	if($countResult != 0){
		?>
		<script type="text/javascript">
			$('.partIndex h2').html("<?php echo $partIndex; ?>");
			$('.titleIndex .left').html("<?php echo $titleIndexLeft; ?>");
			$('.titleIndex .right').html("<?php echo "#".$type."s: ".$countResult; ?>");
		</script>
		<?php

		$tableID = $type."ID";
		for($i=1; $i<=$countResult; $i++){
			$row = mysql_fetch_object($sql);
			$typeID = $row->$tableID;
			if($type=="note"){
				showNote($typeID, $access);
			} else {
				showSource($typeID, $access);
				if($_GET['part']=='export'){
					//$file = escapeshellarg($backuppath); // for the security concious (should be everyone!)
					//$line = `tail -n 1 $backuppath;

					$file_arr = file($backuppath);
					$last_row = $file_arr[count($file_arr) - 1];
					$last_data = explode("%", $last_row);

					if($last_data[1] != ($countResult)){
						//$fp = fopen($backuppath, "r");
						//$data = fgets($fp, 12);
						//echo ftell($fp);
						/*
						$cursor = -1;
						$tmp = " ";
						while ($tmp != "%".$i) {
							fseek($read, $cursor, SEEK_END);
							$tmp = fgetc($read);
							$pos = $pos - 1;
						}
						$tmp = fgets($read);
						fclose($read);
						echo $tmp;
						*/

						$handle = fopen($backuppath, 'a');
						exportSource($typeID, $handle);
						if($i == ($countResult)){
							fwrite($handle, "%".$i);
							fclose($backuppath);
						}
					}
				}
			}

		}


		/*
		while($row = mysql_fetch_object($sql)){
			$typeID = $row->$tableID;
			if($type=="note"){
				showNote($typeID, $access);
			} else {
				showSource($typeID, $access);
				if($_GET['part']=='all'){
					$handle = fopen($backuppath, 'a');
						exportSource($typeID, $handle);
					fclose($backuppath);
				}
			}
		}
		* */
	} else {
		$partIndex = $part;
		if($part == 'source'){
			$titleIndexLeft = linkIndex($type, $part, $partID);
		} elseif($part == 'search') {
			$partIndex = $part."ed";
			$titleIndexLeft = "'".$partID."'";
		} else {
			$titleIndexLeft = "found nothing";
		}
		?>
		<script type="text/javascript">
			$('.partIndex h2').html("<?php echo $partIndex; ?>");
			$('.titleIndex .left').html("<?php echo $titleIndexLeft; ?>");
			$('.titleIndex .right').html("<?php echo "#".$type."s: ".$countResult; ?>");
		</script>
		<?php
		$zeroResults = "Either you are not allowed to see the note or there are no notes with this item.";
		echo "<div class='note'><p class='advice'>".$zeroResults."</p></div>";
	}
}