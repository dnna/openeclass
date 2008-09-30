<?php 

/*========================================================================
*   Open eClass 2.1
*   E-learning and Course Management System
* ========================================================================
*  Copyright(c) 2003-2008  Greek Universities Network - GUnet
*  A full copyright notice can be read in "/info/copyright.txt".
*
*  Developers Group:	Costas Tsibanis <k.tsibanis@noc.uoa.gr>
*			Yannis Exidaridis <jexi@noc.uoa.gr>
*			Alexandros Diamantidis <adia@noc.uoa.gr>
*			Tilemachos Raptis <traptis@noc.uoa.gr>
*
*  For a full list of contributors, see "credits.txt".
*
*  Open eClass is an open platform distributed in the hope that it will
*  be useful (without any warranty), under the terms of the GNU (General
*  Public License) as published by the Free Software Foundation.
*  The full license can be read in "/info/license/license_gpl.txt".
*
*  Contact address: 	GUnet Asynchronous eLearning Group,
*  			Network Operations Center, University of Athens,
*  			Panepistimiopolis Ilissia, 15784, Athens, Greece
*  			eMail: info@openeclass.org
* =========================================================================*/

/*===========================================================================
	work.php
    @version $Id$
	@author : Dionysios G. Synodinos <synodinos@gmail.com>
	@author : Evelthon Prodromou <eprodromou@upnet.gr>
==============================================================================        
        @Description: Main script for the work tool

 	This is a tool plugin that allows course administrators - or others with the
 	same rights

 	The user can : - navigate through files and directories.
                       - upload a file
                       - delete, copy a file or a directory
                       - edit properties & content (name, comments, 
			 html content)

 	@Comments: The script is organised in four sections.

 	1) Execute the command called by the user
           Note (March 2004) some editing functions (renaming, commenting)
           are moved to a separate page, edit_document.php. This is also
           where xml and other stuff should be added.
   	2) Define the directory to display
  	3) Read files and directories from the directory defined in part 2
  	4) Display all of that on an HTML page
 
  	@TODO: eliminate code duplication between document/document.php, scormdocument.php
==============================================================================
*/


$require_current_course = TRUE;
$require_login = true;

include('work_functions.php');
include '../../include/baseTheme.php';

$tool_content = "";

mysql_select_db($currentCourseID);
$gid = user_group($uid);

$coursePath = $webDir."/courses/".$currentCourseID;
if (!file_exists($coursePath)) 
	mkdir("$coursePath",0777);
	
$workPath = $coursePath."/work";
$groupPath = $coursePath."/group/".group_secret($gid);

$nameTools = $langGroupSubmit;

if (isset($_GET['submit'])) {
	$tool_content .= "<p>$langGroupWorkIntro</p>"; 
	show_assignments();	
	draw($tool_content, 2);
} elseif (isset($_POST['assign'])) {
	submit_work($uid, $_POST['assign'], $_POST['file']);
	draw($tool_content, 2);
} else {
	header("Location: work.php");
}


// show non-expired assignments list to allow selection
function show_assignments()
{
	global $m, $uid, $langSubmit, $langDays, $langNoAssign, $tool_content;
		
	$res = db_query("SELECT *, (TO_DAYS(deadline) - TO_DAYS(NOW())) AS days
		FROM assignments");

	if (mysql_num_rows($res) == 0) {
		$tool_content .=  $langNoAssign;
		return;
	}



$tool_content .= <<<cData
	<form action="group_work.php" method="post">
	<input type="hidden" name="file" value="${_GET['submit']}">
	<table width="99%">
	<thead
		<tr bgcolor="#E6E6E6">
		<th align="center">${m['title']}</th>
		<th align="center">${m['deadline']}</th>
		<th align="center" width="10%">${m['submitted']}</th>
		<th align="center" width="10%">${m['select']}</th>
		</tr>
	</thead>
	<tbody>
cData;


	while ($row = mysql_fetch_array($res)) {
		if (!$row['active']) {
			continue;
		}
		
$tool_content .= "<tr><td><a href=\"work.php?id=".$row['id']."\">".htmlspecialchars($row['title']).
	"</a></td><td>".$row['deadline'];

				if ($row['days'] > 1) {
					$tool_content .=  " ($m[in]&nbsp;$row[days]&nbsp;$langDays";
				} elseif ($row['days'] < 0) {
					$tool_content .=  " ($m[expired])";
				} elseif ($row['days'] == 1) {
					$tool_content .=  " ($m[tomorrow])";
				} else {
					$tool_content .=  " ($m[today])";
				}
				
				$tool_content .= "</td><td align=\"center\">";
					
						$subm = was_submitted($uid, user_group($uid), $row['id']);
						if ($subm == 'user') {
							$tool_content .=  $m['yes'];
						} elseif ($subm == 'group') {
							$tool_content .=  $m['by_groupmate'];
						} else {
							$tool_content .=  $m['no'];
						}
					
					
				$tool_content .= "</td><td align=\"center\">";
				
						if ($row['days'] >= 0
							and !was_graded($uid, $row['id'])
							and is_group_assignment($row['id'])) {
							$tool_content .=  "<input type='radio' name='assign' value='$row[id]'>";
						} else {
							$tool_content .=  '-';
						}
						
					$tool_content .= "</td></tr>";

	}
	

		$tool_content .= "</tbody></table><br/>
		<table>
		<thead>
		<tr>
		<th>".$m['comments'].
			":
			</th>
			</tr>
			<tr><td><textarea name=\"comments\" rows=\"4\" cols=\"60\">".
			"</textarea></td></thead></table><br/><input type=\"submit\" name=\"submit\" value=\"".
			$langSubmit."\"></form>";

}


// Insert a group work submitted by user uid to assignment id
function submit_work($uid, $id, $file) {
	global $groupPath, $REMOTE_ADDR, $langUploadError, $langUploadSuccess,
		$langBack, $m, $currentCourseID, $tool_content, $workPath;

	$group = user_group($uid);

	$local_name = greek_to_latin('Group '.$group.'.'.extension($file));

	$source = $groupPath.$file;
	$destination = work_secret($id)."/$local_name";

	if (copy($source, "$workPath/$destination")) {
		delete_submissions_by_uid($uid, $group, $id);
		db_query("INSERT INTO assignment_submit (uid, assignment_id, submission_date,
			submission_ip, file_path, file_name, comments, group_id) 
			VALUES ('$uid','$id', NOW(), '$REMOTE_ADDR', '$destination',
				'".basename($file)."', '$_POST[comments]', '$group')", $currentCourseID);
		
		$tool_content .="<table width=\"99%\"><tbody>
			<tr><td class=\"success\"><p><b>$langUploadSuccess</b></p>
			<p>$m[the_file] \"".basename($file)."\" $m[was_submitted]</p>
			<p><a href='work.php'>$langBack</a></p>
			</td></tr></tbody></table>";
	} else {
		$tool_content .="<table width=\"99%\"><tbody><tr><td class=\"caution\">
		<p><b>$langUploadError</b></p><p><a href='work.php'>$langBack</a></p></td>
		</tr></tbody></table>";
	}
}

?>
