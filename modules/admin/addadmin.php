<?php
/* ========================================================================
 * Open eClass 2.4
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2011  Greek Universities Network - GUnet
 * A full copyright notice can be read in "/info/copyright.txt".
 * For a full list of contributors, see "credits.txt".
 *
 * Open eClass is an open platform distributed in the hope that it will
 * be useful (without any warranty), under the terms of the GNU (General
 * Public License) as published by the Free Software Foundation.
 * The full license can be read in "/info/license/license_gpl.txt".
 *
 * Contact address: GUnet Asynchronous eLearning Group,
 *                  Network Operations Center, University of Athens,
 *                  Panepistimiopolis Ilissia, 15784, Athens, Greece
 *                  e-mail: info@openeclass.org
 * ======================================================================== */

$require_admin = TRUE;

require_once '../../include/baseTheme.php';

$nameTools = $langAdmins;
$navigation[] = array('url' => 'index.php', 'name' => $langAdmin);

// Initialize the incoming variables
$username = isset($_POST['username'])?$_POST['username']:'';

if(isset($_POST['submit']) and !empty($username)) {
	$res = db_query("SELECT user_id FROM user 
                        WHERE username=".autoquote($username));		
	if (mysql_num_rows($res) == 1) 	{                        
            list($user_id) = mysql_fetch_array($res);
            switch ($_POST['adminrights']) {
                case 'admin': $privilege = '0'; // platform admin user
                    break;
                case 'poweruser': $privilege = '1'; // power user
                    break;                                
                case 'manageuser': $privilege = '2'; //  manage user accounts
                    break;                                
                case 'managedepartment' : $privilege = '3'; // manage departments
                    break;
            }  
            $sql = db_query("INSERT INTO admin VALUES($user_id, $privilege)");               
            if ($sql) {
                $tool_content .= "<p class='success'>
                        $langTheUser ".htmlspecialchars($username)." $langWith id=$user_id $langDone</p>";                
            }			
        } else {	
		$tool_content .= "<p class='caution'>$langTheUser ".htmlspecialchars($username)." $langNotFound.</p>";	
	}
} else if (isset($_GET['delete'])) { // delete admin users
    $aid = intval($_GET['aid']);
    if ($aid != 1) { // admin user (with id = 1) cannot be deleted
            $sql = db_query("DELETE FROM admin WHERE admin.idUser = $aid");
            if (!$sql) {
                        $tool_content .= "<center><br />$langDeleteAdmin".$aid." $langNotFeasible  <br /></center>";
            } else {
                $tool_content .= "<p class='success'>$langNotAdmin</p>";
            }
            
        } else {
            $tool_content .= "<p class='caution'>$langCannotDeleteAdmin</p>";
        }     
 }

 $tool_content .= printform($langUsername);

// Display the list of admins
$r1 = db_query("SELECT user_id, prenom, nom, username, admin.privilege FROM user, admin 
                    WHERE user.user_id = admin.idUser 
                    ORDER BY user_id");

$tool_content .= "
  <table class='tbl_alt' width='100%'>
  <tr>
    <th class='center'>ID</th>
    <th>$langSurnameName</th>
    <th>$langUsername</th>
    <th class='center'>$langRole</th>
    <th class='center'>$langActions</th>
  </tr>";

while($row = mysql_fetch_array($r1)) {
        $tool_content .= "<tr>";
        $tool_content .= "<td align='right'>".htmlspecialchars($row['user_id']).".</td>".
        "<td>".htmlspecialchars($row['prenom'])." " .htmlspecialchars($row['nom'])."</td>".
        "<td>".htmlspecialchars($row['username'])."</td>";
        switch ($row['privilege']) {
            case '0': $message = $langAdministrator;
                break;
            case '1': $message = $langPowerUser;
                break;
            case '2': $message = $langManageUser;
                break;
            case '3' : $message = $langManageDepartment;
                break;
        }
        $tool_content .= "<td align='center'>$message</td>";
        if($row['user_id'] != 1) {
                $tool_content .= "<td class='center'>
                        <a href='$_SERVER[PHP_SELF]?delete=1&amp;aid=".$row['user_id']."'>
                        <img src='$themeimg/delete.png' title='$langDelete' />
                        </a>
                        </td>";
        } else {
                $tool_content .= "<td class='center'>---</td>";
        }
        $tool_content .= "</tr>";
}
$tool_content .= "</table><br />";

// Display link back to index.php
$tool_content .= "<p class='right'><a href='index.php'>$langBack</a></p>";

draw($tool_content, 3);

/*****************************************************************************
	 			function printform()
******************************************************************************
  This method constructs a simple form where the administrator searches for
  a user by username to give user administrator permissions

  @returns
  $ret: (String) The constructed form
******************************************************************************/
function printform ($message) {
	
    global $langAdd, $themeimg, $langAdministrator, $langPowerUser, $langManageUser, $langAddRole,
            $langHelpAdministrator, $langHelpPowerUser, $langHelpManageUser, $langUserFillData,
            $langManageDepartment, $langHelpManageDepartment;
        
    $ret = "<form method='post' name='makeadmin' action='$_SERVER[PHP_SELF]'>";
    $ret .= "
        <fieldset>
        <legend>$langUserFillData</legend>
        <table class='tbl' width='100%'>      
        <tr>
            <th class='left'>".$message."</th>
            <td><input type='text' name='username' size='30' maxlength='30'></td>
        </tr>
        <tr><th rowspan='4'>$langAddRole</th>            
            <td><input type='radio' name='adminrights' value='admin' checked>&nbsp;$langAdministrator&nbsp;
        <span class='smaller'>($langHelpAdministrator)</span></td></tr>
        <tr>
        <td><input type='radio' name='adminrights' value='poweruser'>&nbsp;$langPowerUser&nbsp;
            <span class='smaller'>($langHelpPowerUser)</span></td></tr>
        <tr><td><input type='radio' name='adminrights' value='manageuser'>&nbsp;$langManageUser&nbsp;
            <span class='smaller'>($langHelpManageUser)</span></td></tr>
        <tr><td><input type='radio' name='adminrights' value='managedepartment'>&nbsp;$langManageDepartment&nbsp;
            <span class='smaller'>($langHelpManageDepartment)</span></td></tr>
        <tr>
            <td colspan='2' class='right'><input type='submit' name='submit' value='$langAdd'></td>
        </tr>
        </table>
        </fieldset>
    </form>";	
    return $ret;
}
