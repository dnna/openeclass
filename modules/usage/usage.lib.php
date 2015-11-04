<?php

/* ========================================================================
 * Open eClass 
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
 * ======================================================================== 
 */

require_once 'include/log.php';

/**
 * Get statistics of visits and visit duration to a course. The results are 
 * depicted in te first plot of course statistics 
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param string $interval the time interval to aggregate statistics on, values: 'year'|'month'|'week'|'day'
 * @param int $cid the id of the course
 * @param int $user_id the id of the user to filter out the statistics for
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_course_stats($start = null, $end = null, $interval, $cid, $user_id = null)
{
    global $langHits,$langDuration;
    $formattedr = array('time'=> array(), 'hits'=> array(), 'duration'=> array());
    if(!is_null($start) && !is_null($end && !empty($start) && !empty($end))){
        $g = build_group_selector_cond($interval);
        $groupby = $g['groupby'];
        $date_components = $g['select'];
        if(is_numeric($user_id)){
            $q = "SELECT $date_components, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily WHERE course_id=?d AND day BETWEEN ?t AND ?t AND user_id=?d $groupby";
            $r = Database::get()->queryArray($q, $cid, $start, $end, $user_id);    
        }
        else{
            $q = "SELECT $date_components, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily WHERE course_id=?d AND day BETWEEN ?t AND ?t $groupby";
            $r = Database::get()->queryArray($q, $cid, $start, $end);
        }
        foreach($r as $record){
           $formattedr['time'][] = $record->cat_title;
           $formattedr['hits'][] = $record->hits;
           $formattedr['duration'][] = $record->dur;
        }
    }
    return $formattedr;
}

/**
 * Get preference statistics on the visits in different modules of the course. The 
 * preference distribution is based on visits but can be changed to duration if 
 * preferable. The results are shown in a pie (second plot) of the course stats
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param string $interval the time interval to aggregate statistics on, values: 'year'|'month'|'week'|'day'
 * @param int $cid the id of the course
 * @param int $mid the module id
 * @param int $user_id the id of the user to filter out the statistics for
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_course_module_stats($start = null, $end = null, $interval, $cid, $mid, $user_id = null){
    global $modules;
    $mtitle = (isset($modules[$mid]))? $modules[$mid]['title']:'module '.$mid;
    $g = build_group_selector_cond($interval);
    $groupby = $g['groupby'];
    $date_components = $g['select'];
    if(is_numeric($user_id)){
        $q = "SELECT $date_components, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily WHERE course_id=?d AND module_id=?d AND day BETWEEN ?t AND ?t AND user_id=?d $groupby";
        $r = Database::get()->queryArray($q, $cid, $mid, $start, $end, $user_id);    
    }
    else{
        $q = "SELECT $date_components, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily WHERE course_id=?d AND module_id=?d AND day BETWEEN ?t AND ?t $groupby";
        $r = Database::get()->queryArray($q, $cid, $mid, $start, $end);
    }
    $formattedr = array('time'=>array(),'hits'=>array(),'duration'=>array());
    foreach($r as $record){
       $formattedr['time'][] = $record->cat_title;
       $formattedr['hits'][] = $record->hits;
       $formattedr['duration'][] = $record->dur;
    }
    return array('charttitle'=>$mtitle, 'chartdata'=>$formattedr);
}

/**
 * Get statistics of visits and visit duration to a module of a course. The 
 * results are depicted in the plot of module statistics beside the pie of the 
 * course statistics page (third plot). 
  * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param int $cid the id of the course
 * @param int $user_id the id of the user to filter out the statistics for
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_module_preference_stats($start = null, $end = null, $cid, $user_id = null){
    
    global $modules;
    if(is_numeric($user_id)){
        $q = "SELECT module_id mdl, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily WHERE course_id=?d AND day BETWEEN ?t AND ?t AND user_id=?d GROUP BY module_id ORDER BY hits DESC";
        $r = Database::get()->queryArray($q, $cid, $start, $end, $user_id);    
    }
    else{
        $q = "SELECT module_id mdl, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily WHERE course_id=?d AND day BETWEEN ?t AND ?t GROUP BY module_id ORDER BY hits DESC";
        $r = Database::get()->queryArray($q, $cid, $start, $end);
    }
    $formattedr = array();
    $mdls = array();
    $pmid = null;
    foreach($r as $record){
        $mtitle = (isset($modules[$record->mdl]))? $modules[$record->mdl]['title']:'module '.$record->mdl;
        $formattedr[$record->mdl] = $record->hits;
        $mdls[$record->mdl] = $mtitle;
        if(is_null($pmid)){
            $pmid = $record->mdl;
        }
    }
    return array('modules'=>$mdls,'pmid' => $pmid, 'chartdata' => $formattedr);
}

/**
 * Get statistics of user register and unregister actions of a course. The results are 
 * depicted in the last plot of the course statistics 
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param string $interval the time interval to aggregate statistics on, values: 'year'|'month'|'week'|'day'
 * @param int $cid the id of the course
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_course_registration_stats($start = null, $end = null, $interval, $cid)
{
    $formattedr = array('time'=> array(), 'regs'=> array(), 'unregs'=> array());
    if(!is_null($start) && !is_null($end && !empty($start) && !empty($end))){
        $g_reg = build_group_selector_cond($interval, 'reg_date');
        $groupby_reg = $g_reg['groupby'];
        $date_components_reg = $g_reg['select'];
        $reg_t = "SELECT $date_components_reg, COUNT(user_id) regs FROM course_user WHERE course_id=?d AND reg_date BETWEEN ?t AND ?t $groupby_reg";
        $g_unreg = build_group_selector_cond($interval, 'ts');
        $groupby_unreg = $g_unreg['groupby'];
        $date_components_unreg = $g_unreg['select'];
        $unreg_t = "SELECT $date_components_unreg, COUNT(user_id) unregs FROM log WHERE module_id=".MODULE_ID_USERS." AND action_type=".LOG_DELETE." AND course_id=?d AND ts BETWEEN ?t AND ?t $groupby_unreg";
        $q = "SELECT cat_title, ifnull(regs,0) regs, ifnull(unregs,0) unregs FROM ((SELECT cat_title, regs, unregs FROM ($reg_t) x NATURAL LEFT JOIN ($unreg_t) y) UNION (SELECT cat_title, regs, unregs FROM ($unreg_t) x NATURAL LEFT JOIN ($reg_t) y)) z";
        $r = Database::get()->queryArray($q, $cid, $start, $end, $cid, $start, $end, $cid, $start, $end, $cid, $start, $end);
        foreach($r as $record){
           $formattedr['time'][] = $record->cat_title;
           $formattedr['regs'][] = $record->regs;
           $formattedr['unregs'][] = $record->unregs;
        }
    }
    return array('chartdata'=>$formattedr);
}


/**
 * Detailed list of user activity in course. The results are 
 * listed in the first table of the course detailed statistics 
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param int $user the id of the user to filter out the statistics for
 * @param int $course the id of the course
 * @param int $module the module id
 * @return array an array appropriate for displaying in a datatables table  
*/
function get_course_activity_details($start = null, $end = null, $user, $course, $module = -1){
    
    $course_cond = " WHERE course_id = ?d";
    $pars = array($course);
    
    $user_cond1 = "";
    $user_cond2 = "";
    if($user > 0 ){
        $user_cond1 = " AND u.id";
        $user_cond2 = " AND l.user_id";
        $pars[] = $user;
        $pars[] = $user;
    }
    
    $pars[] = $course;
    
    if(!is_null($module) && $module > 0){
        $module_cond = " AND module_id = ?d ";
    }
    else {
        $module = 0;
        $module_cond = " AND module_id > ?d";
    }
    $pars[] = $module;
    
    $date_cond = "";
    if(!is_null($start) && !empty($start) && !is_null($end) && !empty($end)){
        $date_cond = " AND ts BETWEEN ?t AND ?t ";
    }
    $pars[] = $start;
    $pars[] = $end;
    
    $q = "SELECT l.user_id, course_id, module_id, details, ip, action_type, ts, user_name, username, email FROM log l
                                JOIN 
                                (SELECT user_id, username, email, concat(u.surname, ' ', u.givenname) user_name FROM course_user cui JOIN user u ON cui.user_id=u.id $course_cond $user_cond1) cu ON l.user_id=cu.user_id
                                $user_cond2 $course_cond $module_cond $date_cond 
                                ORDER BY ts DESC";
    $r = Database::get()->queryArray($q, $pars);
    $l = new Log();
    $formattedr = array();
    foreach($r AS $record){
        $formattedr[] = array($record->ts, $record->user_name, which_module($record->module_id), $l->get_action_names($record->action_type), $l->course_action_details($record->module_id, $record->details), $record->ip, $record->username, $record->email);
    }
    return $formattedr;
}

/**
 * Detailed list of user visits and duaration in course. The results are 
 * listed in the second table of the course detailed statistics 
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param int $cid the id of the course
 * @return array an array appropriate for displaying in a datatables table  
*/
function get_course_details($start = null, $end = null, $interval, $cid, $user_id = null)
{
    global $modules;
    if(is_numeric($user_id)){
        $q = "SELECT day, hits, duration, CONCAT(surname, ' ', givenname) uname, username, email, module_id FROM actions_daily a JOIN user u ON a.user_id=u.id WHERE course_id=?d AND day BETWEEN ?t AND ?t AND user_id=?d ORDER BY day, module_id";
        $r = Database::get()->queryArray($q, $cid, $start, $end, $user_id);    
    }
    else{
        $q = "SELECT day, hits, duration, CONCAT(surname, ' ', givenname) uname, username, email, module_id FROM actions_daily a JOIN user u ON a.user_id=u.id WHERE course_id=?d AND day BETWEEN ?t AND ?t ORDER BY day, module_id";
        $r = Database::get()->queryArray($q, $cid, $start, $end);
    }
    $formattedr = array();
    foreach($r as $record){
       $mtitle = (isset($modules[$record->module_id]))? $modules[$record->module_id]['title']:'module '.$record->module_id;
       $formattedr[] = array($record->day, $mtitle, $record->uname, $record->hits, $record->duration, $record->username, $record->email);
    }
    return $formattedr;
}

/**
 * Detailed list of user register and unregister actions of a course. The results are 
 * listed in the second table of course detailed statistics 
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param int $cid the id of the course
 * @return array an array appropriate for displaying in a datatables table  
*/
function get_course_registration_details($start = null, $end = null, $cid)
{
    $formattedr = array();
    $reg_t = "SELECT reg_date day, user_id, CONCAT(surname, ' ', givenname) uname, username, email, 1 action FROM course_user cu JOIN user u ON cu.user_id=u.id WHERE cu.course_id=?d AND cu.reg_date BETWEEN ?t AND ?t ORDER BY cu.reg_date";
    $unreg_t = "SELECT ts day, user_id, CONCAT(surname, ' ', givenname) uname, username, email, 0 action FROM log l JOIN user u ON l.user_id=u.id WHERE l.module_id=".MODULE_ID_USERS." AND l.action_type=".LOG_DELETE." AND l.course_id=?d AND l.ts BETWEEN ?t AND ?t ORDER BY ts";
    $q = "SELECT DATE_FORMAT(x.day,'%d-%m-%Y') day, uname, username, email, action FROM (($reg_t) UNION ($unreg_t)) x  ORDER BY day";
    $r = Database::get()->queryArray($q, $cid, $start, $end, $cid, $start, $end);
    foreach($r as $record){
       $formattedr[] = array($record->day, $record->uname, $record->action, $record->username, $record->email);
    }
    return $formattedr;
}

/************************************** User personal stats *****************************/

/**
 * Get user statistics in terms of her visits and their duration in the platform. 
 * The results are shown in the top bar chart of the user stats (first plot)
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param string $interval the time interval to aggregate statistics on, values: 'year'|'month'|'week'|'day'
 * @param int $user the id of the user to filter out the statistics for
 * @param int $course the id of the course
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_user_stats($start = null, $end = null, $interval, $user, $course = null)
{
    $g = build_group_selector_cond($interval);
    $groupby = $g['groupby'];
    $date_components = $g['select'];
    if(is_numeric($course)){
        $q = "SELECT $date_components, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily where user_id = ?d AND day BETWEEN ?t AND ?t AND course_id = ?d $groupby";
        $r = Database::get()->queryArray($q, $user, $start, $end, $course);    
    }
    else{
        $q = "SELECT $date_components, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily where user_id = ?d AND day BETWEEN ?t AND ?t $groupby";
        $r = Database::get()->queryArray($q, $user, $start, $end);
    }
    $formattedr = array('time'=>array(),'hits'=>array(),'duration'=>array());
    foreach($r as $record){
       $formattedr['time'][] = $record->cat_title;
       $formattedr['hits'][] = $record->hits;
       $formattedr['duration'][] = $record->dur;
    }
    return $formattedr;
}

/**
 * Get preference statistics of the user based on the visits in different courses of the platform. 
 * The results are shown in the pie chart of the user stats (second plot)
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param int $user the id of the user to filter out the statistics for
 * @param int $course the id of the course
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_course_preference_stats($start = null, $end = null, $user, $course = null){
    
    $courses = null;
    $pcid = null;
    if(!is_null($course)){
        $formattedr = get_module_preference_stats($start, $end, $course, $user);
    }
    else{
        $q = "SELECT c.id cid, c.title, s.hits, s.dur FROM (SELECT course_id cid, sum(hits) hits, round(sum(duration)/3600,1) dur FROM actions_daily WHERE user_id=?d AND day BETWEEN ?t AND ?t GROUP BY course_id) s JOIN course c on s.cid=c.id ORDER BY hits DESC LIMIT 10";
        $r = Database::get()->queryArray($q, $user, $start, $end);
        $formattedr = array();
        $courses = array();
        foreach($r as $record){
            $ctitle = $record->title;
            $formattedr[$record->cid] = $record->hits;
            $courses[$record->cid] = $ctitle;
            if(is_null($pcid)){
                $pcid = $record->cid;
            }
        }
    }
    return array('courses'=>$courses,'pcid' => $pcid, 'chartdata' => $formattedr);
}

/**
 * Get user visits and duration in a course. If the course is specified in the 
 * reports' parameters then return the visits in a specified module os a course.
 * The results are shown in a bar chart (third plot) of the user stats
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param string $interval the time interval to aggregate statistics on, values: 'year'|'month'|'week'|'day'
 * @param int $user the id of the user to filter out the statistics for
 * @param int $course the id of the course
 * @param int $module the module id
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_user_course_stats($start = null, $end = null, $interval, $user, $course, $module){
    $ctitle = course_id_to_title($course);
    if(!is_null($module) && !empty($module)){
        $formattedr = get_course_module_stats($start, $end, $interval, $course, $module, $user);
    }
    else{
        $formattedr = get_user_stats($start, $end, $interval, $user, $course);
    }
    return array('charttitle'=>$ctitle, 'chartdata'=>$formattedr);
}

/**
 * Detailed list of user's daily aggregated activity in course. The results are 
 * listed in the first table of the user detailed statistics 
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param string $interval the time interval to aggregate statistics on, values: 'year'|'month'|'week'|'day'
 * @param int $user the id of the user to filter out the statistics for
 * @param int $course the id of the course
 * @return array an array appropriate for displaying in a datatables table  
*/
function get_user_details($start = null, $end = null, $interval, $user, $course = null)
{
    global $modules;
    if(is_numeric($course)){
        $q = "SELECT day, hits hits, duration dur, module_id, c.title FROM actions_daily a JOIN course c ON a.course_id=c.id WHERE user_id = ?d AND day BETWEEN ?t AND ?t AND course_id = ?d ORDER BY day";
        $r = Database::get()->queryArray($q, $user, $start, $end, $course);    
    }
    else{
        $q = "SELECT day, hits hits, duration dur, c.title, module_id FROM actions_daily a JOIN course c ON a.course_id=c.id where user_id = ?d AND day BETWEEN ?t AND ?t ORDER BY day";
        $r = Database::get()->queryArray($q, $user, $start, $end);
    }
    $formattedr = array();
    foreach($r as $record){
        $mtitle = (isset($modules[$record->module_id]))? $modules[$record->module_id]['title']:'module '.$record->module_id;
        $formattedr[] = array($record->day, $record->title, $mtitle, $record->hits, $record->dur);
    }
    return $formattedr;
}

/**************************** Admin stats *************************************/


/**
 * Get user login statistics in the platform. 
 * The results are shown in the top chart of the admin stats (first plot).
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param string $interval the time interval to aggregate statistics on, values: 'year'|'month'|'week'|'day'
 * @param int $user the id of the user to filter out the statistics for
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_user_login_stats($start = null, $end = null, $interval, $user){
    $g = build_group_selector_cond($interval, 'date_time');
    $groupby = $g['groupby'];
    $date_components = $g['select'];
    $q = "SELECT $date_components, COUNT(*) c FROM logins WHERE date_time BETWEEN ?t AND ?t $groupby";
    $r = Database::get()->queryArray($q, $start, $end);
    $formattedr = array('time'=>array(),'logins'=>array());
    foreach($r as $record){
        $formattedr['time'][] = $record->cat_title;
        $formattedr['logins'][] = $record->c;
    }
    return $formattedr;
}

/**
 * Get number of users per type and per department of the platform. 
 * The results are shown in the a bar chart of the admin stats (second plot)
 * @param date $root_department the deprtament for which statistics will be retrieved per subdepartment
 * @param boolean $total if true only the total number of users will be returned 
 * (used by the footer of the corresponding datatable), otherwise per subdepartment
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_department_user_stats($root_department = 1, $total = false){
    global $langStatsUserStatus;
    /*Simple case to get total users of the platform*/
    $q = "SELECT id, lft, rgt INTO @rootid, @rootlft, @rootrgt FROM hierarchy WHERE id=?d;";
    Database::get()->query($q, $root_department);
    if($total && $root_department == 1){
        $q = "SELECT @rootid root, @rootid did, 'platform' dname, 0 leaf, status, count(distinct user_id) users_count FROM course_user GROUP BY status;";
    }
    else{
        $group_field = ($total)? "root":"toph.id";
        $q = "SELECT @rootid root, toph.id did, toph.name dname, IF((toph.rgt-toph.lft)>1, 0, 1) leaf, status, count(distinct user_id) users_count
        FROM (SELECT ch.id did, ch.name dname, ch.lft, ch.rgt, cu.status, cu.user_id
            FROM course_department cd 
            JOIN course_user cu on cd.course=cu.course_id
            JOIN hierarchy ch ON cd.department=ch.id
            JOIN course c ON cd.course=c.id
            WHERE ch.lft BETWEEN @rootlft AND @rootrgt) chh 
            RIGHT JOIN (SELECT descendant.id, descendant.name, descendant.lft, descendant.rgt, count(*) c 
              FROM hierarchy descendant 
              JOIN 
              hierarchy ancestor ON descendant.lft>ancestor.lft AND descendant.lft<=ancestor.rgt 
              WHERE ancestor.lft>=@rootlft AND ancestor.rgt<=@rootrgt AND descendant.lft>=@rootlft AND descendant.rgt<=@rootrgt
              GROUP BY descendant.id having c=1
            ) toph ON chh.lft>=toph.lft AND chh.lft<=toph.rgt 
        GROUP BY ".$group_field.", status ORDER BY did";
    }
    $r = Database::get()->queryArray($q);
    $formattedr = array('department'=>array(),$langStatsUserStatus[USER_TEACHER]=>array(),$langStatsUserStatus[USER_STUDENT]=>array());
    $depids = array();
    $leaves = array();
    $d = '';
    $i = -1;
    foreach($r as $record){
        if($record->dname != $d){
            $i++;
            $depids[] = ($total)? $record->root:$record->did;
            $leaves[] = $record->leaf;
            $formattedr['department'][] = $record->dname;
            $d = $record->dname;
            $formattedr[$langStatsUserStatus[USER_TEACHER]][] = 0;
            $formattedr[$langStatsUserStatus[USER_STUDENT]][] = 0;
       }
       if(!is_null($record->status)){
           $formattedr[$langStatsUserStatus[$record->status]][$i] = $record->users_count;
       }
    }
    return array('deps'=>$depids,'leafdeps'=>$leaves,'chartdata'=>$formattedr);

}

/**
 * Get number of courses per type and per department of the platform. 
 * The results are shown in the a bar chart of the admin stats (third plot)
 * @param date $root_department the deprtament for which statistics will be retrieved per subdepartment
 * @return array an array appropriate for displaying in a c3 plot when json encoded 
*/
function get_department_course_stats($root_department = 1){
    global $langCourseVisibility;
    $q = "SELECT lft, rgt INTO @rootlft, @rootrgt FROM hierarchy WHERE id=?d;";
    Database::get()->query($q, $root_department);
    
    $q = "SELECT toph.id did, toph.name dname, IF((toph.rgt-toph.lft)>1, 0, 1) leaf, visible, SUM(courses) courses_count
      FROM (SELECT ch.lft, ch.rgt, c.visible, count(c.id) courses 
            FROM course_department cd 
            JOIN hierarchy ch ON cd.department=ch.id
            JOIN course c ON cd.course=c.id 
            WHERE ch.lft BETWEEN @rootlft AND @rootrgt
            GROUP BY cd.department, c.visible ) ch
          RIGHT JOIN 
          (SELECT descendant.id, descendant.name, descendant.lft, descendant.rgt, count(*) c 
            FROM hierarchy descendant 
            JOIN 
            hierarchy ancestor ON descendant.lft>ancestor.lft AND descendant.lft<=ancestor.rgt 
            WHERE ancestor.lft>=@rootlft AND ancestor.rgt<=@rootrgt AND descendant.lft>=@rootlft AND descendant.rgt<=@rootrgt
            GROUP BY descendant.id having c=1) toph 
     ON ch.lft>=toph.lft AND ch.lft<=toph.rgt 
    GROUP BY toph.id, ch.visible ORDER BY did";
    $r = Database::get()->queryArray($q);
    $formattedr = array('department'=>array(),$langCourseVisibility[COURSE_CLOSED]=>array(),$langCourseVisibility[COURSE_REGISTRATION]=>array(), $langCourseVisibility[COURSE_OPEN]=>array(), $langCourseVisibility[COURSE_INACTIVE]=>array());
    $depids = array();
    $leaves = array();
    $d = '';
    $i = -1;
    foreach($r as $record){
        if($record->dname != $d){
            $i++;
            $depids[] = $record->did;
            $leaves[] = $record->leaf;
            $formattedr['department'][] = $record->dname;
            $d = $record->dname;
            $formattedr[$langCourseVisibility[COURSE_CLOSED]][] = 0;
            $formattedr[$langCourseVisibility[COURSE_REGISTRATION]][] = 0;
            $formattedr[$langCourseVisibility[COURSE_OPEN]][] = 0;
            $formattedr[$langCourseVisibility[COURSE_INACTIVE]][] = 0;
        }
        if(!is_null($record->visible)){
           $formattedr[$langCourseVisibility[$record->visible]][$i] = $record->courses_count;
        }
    }
    return  array('deps'=>$depids,'leafdeps'=>$leaves,'chartdata'=>$formattedr);

}

/**
 * Detailed list of user logins. The results are 
 * listed in the first table of the admin detailed statistics 
 * @param date $start the start of period to retrieve statistics for
 * @param date $end the end of period to retrieve statistics for
 * @param int $user the id of the user to filter out the statistics for
 * @return array an array appropriate for displaying in a datatables table  
*/
function get_user_login_details($start = null, $end = null, $user){
    $q = "SELECT l.date_time, concat(u.surname, ' ', u.givenname) user_name, username, email, c.title course_title, l.ip 
            FROM logins l 
            JOIN user u ON l.user_id=u.id 
            JOIN course c ON l.course_id=c.id"; 
    
    if(!is_null($start) && !empty($start) && !is_null($end) && !empty($end)){
        $q .= " WHERE l.date_time BETWEEN ?t AND ?t";
        $r = Database::get()->queryArray($q, $start, $end);
    }
    else {
        $r = Database::get()->queryArray($q);
    }
    $formattedr = array();
    foreach($r AS $record){
        $formattedr[] = array($record->date_time, $record->user_name, $record->course_title, $record->ip, $record->username, $record->email);
    }
    return $formattedr;
}

/************************************** Utilities ******************************/

/**
 * Build SELECT and GROUP BY clauses for queries which retrieve stats grouped on a specified time interval
 * @param string $interval a value among 'year', 'month', 'week', 'day'
 * @param string $date_field the name of the date field of the queried table
 * @return array(string, string) an array containing the 'select' and 'groupby' clauses
*/
function build_group_selector_cond($interval = 'month', $date_field = 'day')
{
    $groupby = "";
    $select = "";
    switch($interval){
        case 'year':
            $select = "DATE_FORMAT($date_field, '%Y-01-01') cat_title, DATE_FORMAT($date_field, '%Y') y";
            $groupby = "GROUP BY y";
            break;
        case 'month':
            $select = "DATE_FORMAT($date_field, '%Y-%m-01') cat_title, DATE_FORMAT($date_field, '%m') m, DATE_FORMAT($date_field, '%Y') y";
            $groupby = "GROUP BY y, m";
            break;
        case 'week':
            $select = "STR_TO_DATE(DATE_FORMAT($date_field, '%Y%u Monday'), '%X%V %W') cat_title, DATE_FORMAT($date_field,'%u') w, DATE_FORMAT($date_field, '%m') m, DATE_FORMAT($date_field, '%Y') y";
            $groupby = "GROUP BY y, w";
            break;
        case 'day':
            $select = "DATE_FORMAT($date_field, '%Y-%m-%d') cat_title, DATE_FORMAT($date_field,'%d') d, DATE_FORMAT($date_field,'%u') w, DATE_FORMAT($date_field, '%m') m, DATE_FORMAT($date_field, '%Y') y";
            $groupby = "GROUP BY y, m, d";
            break;
    }
    return array('groupby'=>$groupby,'select'=>$select);
}

/**
 * Count users of the system based on their type
 * @param int $user_type a value among USER_TEACHER, USER_STUDENT, USER_GUEST
 * @return int the number of all the users or of specific type of the system 
*/
function count_users($user_type = null){
    if(is_null($user_type)){
        return Database::get()->querySingle("SELECT COUNT(*) as count FROM user")->count;
    }
    elseif(!in_array($user_type,array(USER_TEACHER, USER_STUDENT, USER_GUEST))){
        return 0;
    }
    else{
        return Database::get()->querySingle("SELECT COUNT(*) as count FROM user WHERE status = ?d", $user_type)->count;
    }
}

/**
 * Count courses of the system based on their type
 * @param int $course_type a value among COURSE_INACTIVE, COURSE_OPEN, COURSE_REGISTRATION, COURSE_CLOSED
 * @return int the number of all the users or of specific type of the system 
*/
function count_courses($course_type = null){
    if(is_null($course_type)){
        return Database::get()->querySingle("SELECT COUNT(*) as count FROM course")->count;
    }
    elseif(!in_array($course_type,array(COURSE_INACTIVE, COURSE_OPEN, COURSE_REGISTRATION, COURSE_CLOSED))){
        return 0;
    }
    else{
        return Database::get()->querySingle("SELECT COUNT(*) as count FROM course WHERE visible != ?d", $course_type)->count;
    }
}

/**
 * Count users of the system based on their type
 * @param int cid a value among USER_TEACHER, USER_STUDENT
 * @param int $user_type a value among USER_TEACHER, USER_STUDENT
 * @return int the number of all the users or of specific type of the system 
*/
function count_course_users($cid, $user_type = null){
    
    if(is_null($user_type)){
        return Database::get()->querySingle("SELECT COUNT(*) as count FROM course_user WHERE course_id = ?d", $cid)->count;
    }
    elseif(!in_array($user_type,array(USER_TEACHER, USER_STUDENT, USER_GUEST))){
        return 0;
    }
    else{
        return Database::get()->querySingle("SELECT COUNT(*) as count FROM course_user WHERE course_id = ?d AND status = ?d", $cid, $user_type)->count;
    }
}

/**
 * Return module title based on the course module id
 * @param int mid the id of the module
 * @return string the title of the course module 
*/
function which_module($mid){
    global $langAdminUsers, $langExternalLinks, $langCourseInfo, $langAbuseReport, $langModifyInfo, $modules; 
    switch($mid) {                  
        case MODULE_ID_USERS:
            return $langAdminUsers;
        case MODULE_ID_TOOLADMIN:
            return $langExternalLinks;
        case MODULE_ID_SETTINGS:
            return $langCourseInfo;
        //case MODULE_ID_ABUSE_REPORT:
          //  return $langAbuseReport;
        case MODULE_ID_COURSEINFO:
            return $langModifyInfo;
        default:
            return $modules[$mid]['title'];
    }
}
  
/**
 * Count user groups of the course
 * @param int cid the course id
 * @return int the number of user groups
*/
function count_course_groups($cid){
    
    return Database::get()->querySingle("SELECT COUNT(*) as count FROM `group` WHERE course_id = ?d", $cid)->count;
}

/**
 * Get the sum of visits with their duration for a course 
 * @param int cid the course id 
 * @return array(int, string) an array with the number of visits and their duration formatted as h:mm:ss
*/
function course_visits($cid){
    $r = Database::get()->querySingle("SELECT sum(hits) hits, sum(duration) dur FROM actions_daily WHERE course_id=?d", $cid);
    return array('hits' => $r->hits, 'duration' => user_friendly_seconds($r->dur));
}

/**
 * Transform seconds to h:mm:ss
 * @param int seconds the nu,ber of seconds to be shown properly 
 * @return string a formated time string
*/
function user_friendly_seconds($seconds){
    $hours = floor($seconds / 3600);
    $mins = floor(($seconds - ($hours*3600)) / 60);
    $secs = floor($seconds % 60);
    $fd = ($hours<10)? '0'.$hours:$hours;
    $fd .= ':';
    $fd .= ($mins<10)? '0'.$mins:$mins;
    $fd .= ':';
    $fd .= ($secs<10)? '0'.$secs:$secs;
    return $fd;
}

/**
 * Create the panel to show a plot
 * @param string plot_id the id of the id of the div where the plot will be drwan 
 * @param string title the caption of the plot
 * @return string a formated element ready to display a plot
*/
function plot_placeholder($plot_id, $title = null){
    $p = "<ul class='list-group'>";
    if(!is_null($title)){
        $p .= "<li class='list-group-item'>"
            . "<label id='".$plot_id."_title'>"
            . $title
            . "</label>"
            . "</li>";
    }
    $p .= "<li class='list-group-item'>"
            . "<div id='$plot_id'></div>"
            . "</li></ul>";
    return $p;
}

/**
 * Create the panel and the table structure of a table to be filled with AJAX with data
 * @param string table_id the id of the table in the DOM
 * @param string table_class tha class of the table element
 * @param string table_schema the header and footer of the table which also specify the column number of the table 
 * @param string titel the caption of the table
 * @return string a formated element containing the specified table
*/
function table_placeholder($table_id, $table_class, $table_schema, $title = null){
    $t = "";
    if(!is_null($title)){
        $t .= "<div class='panel-heading'>"
            . "<label id='".$table_id."_title'>"
            . $title
            . "</label>"
            ."<div class='pull-right' id='{$table_id}_buttons'></div><div style='clear:both;'></div>"
            . "</div>";
    }
    $t .= "<div class='panel-body'>"
       . "<table id='$table_id' class='$table_class'>"
       . "$table_schema"
       . "</table>"
       . "</div>";
    return $t;
}