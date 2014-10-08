<?php

/* ========================================================================
 * Open eClass 3.0
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2014  Greek Universities Network - GUnet
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


/**
 * @brief links module 
 * partially based on code by Patrick Cool
 *
 */

$require_current_course = TRUE;
$require_help = true;
$helpTopic = 'Link';
$guest_allowed = true;

require_once '../../include/baseTheme.php';
require_once 'include/log.php';
require_once 'linkfunctions.php';
require_once 'include/lib/modalboxhelper.class.php';
require_once 'include/lib/multimediahelper.class.php';
ModalBoxHelper::loadModalBox();

require_once 'include/action.php';
$action_stats = new action();
$action_stats->record(MODULE_ID_LINKS);

$nameTools = $langLinks;

$is_in_tinymce = (isset($_REQUEST['embedtype']) && $_REQUEST['embedtype'] == 'tinymce') ? true : false;
$menuTypeID = ($is_in_tinymce) ? 5 : 2;
$tinymce_params = '';

if ($is_in_tinymce) {
    $_SESSION['embedonce'] = true; // necessary for baseTheme
    $docsfilter = (isset($_REQUEST['docsfilter'])) ? '&amp;docsfilter=' . $_REQUEST['docsfilter'] : '';
    $tinymce_params = '&amp;embedtype=tinymce' . $docsfilter;
    load_js('tinymce.popup.urlgrabber.min.js');
}

$head_content .= <<<hContent
<script type="text/javascript">
function checkrequired(which, entry) {
	var pass=true;
	if (document.images) {
		for (i=0;i<which.length;i++) {
			var tempobj=which.elements[i];
			if (tempobj.name == entry) {
				if (tempobj.type=="text"&&tempobj.value=='') {
					pass=false;
					break;
		  		}
	  		}
		}
	}
	if (!pass) {
		alert("$langEmptyLinkURL");
		return false;
	} else {
		return true;
	}
}

</script>
hContent;
$state= 'success';

if (isset($_GET['category'])) {
    $category = intval($_GET['category']);
} else {
    unset($category);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    unset($id);
}

if (isset($_GET['urlview'])) {
    $urlview = urlencode($_GET['urlview']);
} else {
    $urlview = '';
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($is_editor) {
    if (isset($_POST['submitLink'])) {
        submit_link();
    }
    if (isset($_POST['submitCategory'])) {
        submit_category();
    }
    switch ($action) {
        case 'deletelink':
            delete_link($id);
            break;
        case 'deletecategory':
            delete_category($id);
            break;
    }

    if (!empty($catlinkstatus)) {
        $tool_content .= "<p class='$state'>$catlinkstatus</p>";
    }

    if (!$is_in_tinymce) {
        if (isset($_GET['action'])) {
            $tool_content .= action_bar(array(
            array('title' => $langBack,
                  'url' => "$_SERVER[SCRIPT_NAME]?course=$course_code",
                  'icon' => 'fa-reply',
                  'level' => 'primary-label',
                  'show' => $is_editor)));
            
        } else {
            $ext = (isset($category)? "&amp;category=$category": '') .
                   (isset($urlview)? "&amp;urlview=$urlview": '');
            $tool_content .= action_bar(array(
                array('title' => $langLinkAdd,
                      'url' => "$_SERVER[SCRIPT_NAME]?course=$course_code&amp;action=addlink$ext",
                      'icon' => 'fa-plus-circle',
                      'button-class' => 'btn-success',
                      'level' => 'primary-label'),
                array('title' => $langCategoryAdd,
                      'url' => "$_SERVER[SCRIPT_NAME]?course=$course_code&amp;action=addcategory$ext",
                      'icon' => 'fa-plus-circle',
                      'button-class' => 'btn-success',
                      'level' => 'primary-label')));
        }
    }

    // Display the correct title and form for adding or modifying a category or link.
    if (in_array($action, array('addlink', 'editlink'))) {
        $tool_content .= "<form method='post' action='$_SERVER[SCRIPT_NAME]?course=$course_code&amp;urlview=$urlview' onsubmit=\"return checkrequired(this, 'urllink');\">";
        if ($action == 'editlink') {
            $tool_content .= "<input type='hidden' name='id' value='$id' />";
            link_form_defaults($id);
            $form_legend = $langLinkModify;
            $submit_label = $langLinkModify;
        } else {
            $form_url = $form_title = $form_description = '';
            $form_legend = $langLinkAdd;
            $submit_label = $langAdd;
        }
        $tool_content .= "<fieldset>
                        <legend>$form_legend</legend>
                        <table width='100%' class='tbl'>
                        <tr><th>URL:</th>
                            <td><input type='text' name='urllink' size='53'$form_url /></td></tr>
                        <tr><th>$langLinkName:</th>
                            <td><input type='text' name='title' size='53'$form_title /></td></tr>
                        <tr><th>$langDescription:</th>
                            <td>" . rich_text_editor('description', 3, 50, $form_description) . "</td></tr>
                        <tr><th>$langCategory:</th>
                        <td><select name='selectcategory'>
                        <option value='0'>--</option>";
        $resultcategories = Database::get()->queryArray("SELECT * FROM link_category WHERE course_id = ?d ORDER BY `order`", $course_id);
        foreach ($resultcategories as $myrow) {
            $tool_content .= "<option value='$myrow->id'";
            if (isset($category) and $myrow->id == $category) {
                $tool_content .= " selected='selected'";
            }
            $tool_content .= '>' . q($myrow->name) . "</option>";
        }
        $tool_content .= "</select></td></tr>
                        <tr><th>&nbsp;</th>
                        <td class='right'><input type='submit' name='submitLink' value='$submit_label' /></td>
                        </tr>
                        </table>
                      </fieldset>
                      </form>";
    } elseif (in_array($action, array('addcategory', 'editcategory'))) {
        $tool_content .= "<form method='post' action='$_SERVER[SCRIPT_NAME]?course=$course_code&urlview=$urlview'>";
        if ($action == 'editcategory') {
            $tool_content .= "<input type='hidden' name='id' value='$id' />";
            category_form_defaults($id);
            $form_legend = $langCategoryMod;
        } else {
            $form_name = $form_description = '';
            $form_legend = $langCategoryAdd;
        }
        $tool_content .= "<fieldset><legend>$form_legend</legend>
                            <table width='100%' class='tbl'>
                            <tr><th>$langCategoryName:</th>
                                <td><input type='text' name='categoryname' size='53'$form_name /></td></tr>
                            <tr><th>$langDescription:</th>
                                <td><textarea rows='5' cols='50' name='description'>$form_description</textarea></td></tr>
                            <tr><th>&nbsp;</th>
                                <td class='right'><input type='submit' name='submitCategory' value='$form_legend' /></td></tr>
                         </table></fieldset></form>";
    }
}

if (isset($_GET['down'])) {
    move_order('link', 'id', intval($_GET['down']), 'order', 'down', "course_id = $course_id");
} elseif (isset($_GET['up'])) {
    move_order('link', 'id', intval($_GET['up']), 'order', 'up', "course_id = $course_id");
} elseif (isset($_GET['cdown'])) {
    move_order('link_category', 'id', intval($_GET['cdown']), 'order', 'down', "course_id = $course_id");
} elseif (isset($_GET['cup'])) {
    move_order('link_category', 'id', intval($_GET['cup']), 'order', 'up', "course_id = $course_id");
}

$resultcategories = Database::get()->queryArray("SELECT * FROM `link_category` WHERE course_id = ?d ORDER BY `order`", $course_id);

if (count($resultcategories) > 0) {   
    $numberofzerocategory = count(Database::get()->queryArray("SELECT * FROM `link` WHERE course_id = ?d AND (category = 0 OR category IS NULL)", $course_id));
    // making the show none / show all links. Show none means urlview=0000 (number of zeros depending on the
    // number of categories). Show all means urlview=1111 (number of 1 depending on teh number of categories).
    $resultcategories = Database::get()->queryArray("SELECT * FROM `link_category` WHERE course_id = ?d ORDER BY `order`", $course_id);
    $aantalcategories = count($resultcategories);
   
    $tool_content .= "<table class='table table-striped table-bordered table-hover'>";
    // uncategorized links
    if ($numberofzerocategory !== 0) {
        $tool_content .= "<tr><th class='text-left'>$langNoCategory</th><th class='text-center'>" . icon('fa-gears') . "</th></tr>";
        showlinksofcategory(0);        
    }
    if ($aantalcategories > 0) {
       $tool_content .= "<tr><th>$langCategorisedLinks</th><th class='text-center'>";
       if (isset ($_GET['urlview']) and abs($_GET['urlview']) == 0) {
                $tool_content .= "<a href='$_SERVER[SCRIPT_NAME]?course=$course_code&amp;urlview=" . str_repeat('1', $aantalcategories) . $tinymce_params."'>" .icon('fa-folder', $showall)."</a>";
        } else {
            $tool_content .= "<a href='$_SERVER[SCRIPT_NAME]?course=$course_code&amp;urlview=" . str_repeat('0', $aantalcategories) . $tinymce_params."'>" .icon('fa-folder-open', $shownone)."</a>";
        }
        $tool_content .= "</th></tr>";
    }
    $i = 0;
    foreach ($resultcategories as $myrow) {
        if (empty($urlview)) {
            // No $view set in the url, thus for each category link it should be all zeros except it's own
            $view = makedefaultviewcode($i);
        } else {
            $view = $urlview;
            $view[$i] = '1';
        }
        // if the $urlview has a 1 for this categorie, this means it is expanded and should be displayed as a
        // - instead of a +, the category is no longer clickable and all the links of this category are displayed
        $description = standard_text_escape($myrow->description);
        if ((isset($urlview[$i]) and $urlview[$i] == '1')) {
            $newurlview = $urlview;
            $newurlview[$i] = '0';
            $tool_content .= "<tr><th class = 'text-left'>".icon('fa-folder-open-o', $shownone)."
                        <a href='$_SERVER[SCRIPT_NAME]?course=$course_code&amp;urlview=$newurlview$tinymce_params'>" . q($myrow->name) . "</a>";
            if (!empty($description)) {
                $tool_content .= "<br>$description</th>";
            } else {
                $tool_content .= "</th>";
            }
            $tool_content .= "<td class='option-btn-cell'>";
            if ($is_editor && !$is_in_tinymce) {
                showcategoryadmintools($myrow->id);
            } else {
                $tool_content .= "&nbsp;";
            }
            $tool_content .= "</td>";
            $tool_content .= "</tr>";
            showlinksofcategory($myrow->id);
        } else {            
            $tool_content .= "<tr><th class = 'text-left'>".icon('fa-folder-o', $showall)."
                          <a href='$_SERVER[SCRIPT_NAME]?course=$course_code&amp;urlview=";
            $tool_content .= is_array($view) ? implode('', $view) : $view;
            $tool_content .= $tinymce_params . "'>" . q($myrow->name) . "</a>";
            $description = standard_text_escape($myrow->description);
            if (!empty($description)) {
                $tool_content .= "<br>$description</th>";
            } else {
                $tool_content .= "</th>";
            }
            $tool_content .= "<td class='option-btn-cell'>";
            if ($is_editor && !$is_in_tinymce) {                
                showcategoryadmintools($myrow->id);                
            } else {
                $tool_content .= "&nbsp;";
            }
            $tool_content .= "</td>";
            $tool_content .= "</tr>";
        }        
        $i++;
    }
    $tool_content .= "</table>";
} else {   // no category   
    $tool_content .= "<div class='text-center alert-warning'>$langNoLinksExist</div>";
}
add_units_navigation(true);
draw($tool_content, $menuTypeID, null, $head_content);