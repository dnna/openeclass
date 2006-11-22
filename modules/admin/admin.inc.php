<?php
/**=============================================================================
       	GUnet e-Class 2.0 
        E-learning and Course Management Program  
================================================================================
       	Copyright(c) 2003-2006  Greek Universities Network - GUnet
        A full copyright notice can be read in "/info/copyright.txt".
        
       	Authors:    Costas Tsibanis <k.tsibanis@noc.uoa.gr>
        	    Yannis Exidaridis <jexi@noc.uoa.gr> 
      		    Alexandros Diamantidis <adia@noc.uoa.gr> 

        For a full list of contributors, see "credits.txt".  
     
        This program is a free software under the terms of the GNU 
        (General Public License) as published by the Free Software 
        Foundation. See the GNU License for more details. 
        The full license can be read in "license.txt".
     
       	Contact address: GUnet Asynchronous Teleteaching Group, 
        Network Operations Center, University of Athens, 
        Panepistimiopolis Ilissia, 15784, Athens, Greece
        eMail: eclassadmin@gunet.gr
==============================================================================*/

/**===========================================================================
	admin.inc.php
	@last update: 31-05-2006 by Stratos Karatzidis
	              11-07-2006 by Vagelis Pitsiougas
	@authors list: Karatzidis Stratos <kstratos@uom.gr>
		       Vagelis Pitsioygas <vagpits@uom.gr>
==============================================================================        
        @Description: Functions Library for admin purposes

 	This library includes all the functions that admin is using 
	and their settings.

==============================================================================
*/

// a class to format date/time for managing the account activity
include 'datetime/datetimeclass.inc';

/*******************************************************
	eclass replacement for php stripslashes() function
	The standard php stripslashes() removes ALL backslashes
	even from strings - so  C:\temp becomes C:temp - this isn't good.
	This function should work as a fairly safe replacement
	to be called on quoted AND unquoted strings (to be sure)

	@param string the string to remove unsafe slashes from
	@return string
*********************************************************/
function stripslashes_safe($string) 
{

    $string = str_replace("\\'", "'", $string);
    $string = str_replace('\\"', '"', $string);
    $string = str_replace('\\\\', '\\', $string);
    return $string;
}

/*************************************************************
Show a selection box. Taken from main.lib.php

Difference: The function returns a value( a formatted select box)
and not just echo the select box (version in main.lib.php)

$entries: an array of (value => label)
$name: the name of the selection element
$default: if it matches one of the values, specifies the default entry
return $select_box : string (a formatted select box)
****************************************************************/
function selection2($entries, $name, $default = '')
{
	$select_box = "<select name='$name'>\n";
	foreach ($entries as $value => $label) 
	{
	    if ($value == $default) 
	    {
		$select_box .= "<option selected value='" . htmlspecialchars($value) . "'>" .
				htmlspecialchars($label) . "</option>\n";
	    } 
	    else 
	    {
		$select_box .= "<option value='" . htmlspecialchars($value) . "'>" .
				htmlspecialchars($label) . "</option>\n";
	    }
	}
	$select_box .= "</select>\n";
	
	return $select_box;
}

/*************************************************************
Show a selection box with departments. 

The function returns a value( a formatted select box with departments)
and their values as keys in the array/select box

$department_value: the predefined/selected department value
return $departments_select : string (a formatted select box)
****************************************************************/
function list_departments($department_value)
{
	$qry = "SELECT faculte.id,faculte.name FROM faculte ORDER BY faculte.name";
  $dep = mysql_query($qry);
  if($dep)
  {
		$departments_select = "";
		$departments = array();
		while($row=mysql_fetch_array($dep))
		{
	    $id = $row['id'];    
	    $name = $row['name'];
	    $departments[$id] = $name;
		}
		$departments_select = selection2($departments,"department",$department_value);
	
		return $departments_select;
	
  }
	else
  {
		return 0;
	}
}

/**************************************************************
Purpose: covert the difference ($seconds) between 2 unix timestamps
and produce a string ($r), explaining the time 
(e.g. 2 years 2 months 1 day)

$seconds : integer
return $r
***************************************************************/
function convert_time($seconds)
{
    $f_minutes = $seconds / 60;
    $i_minutes = floor($f_minutes);
    $r_seconds = intval(($f_minutes - $i_minutes) * 60);
	        
    $f_hours = $i_minutes / 60;
    $i_hours = floor($f_hours);
    $r_minutes = intval(($f_hours  - $i_hours) * 60);
			        
    $f_days = $i_hours / 24;
    $i_days = floor($f_days);
    $r_hours = intval(($f_days - $i_days) * 24);
    $r = "";					        
    if ($i_days > 0) 
    {
        //$r .= $i_days."<br>".$r_minutes."<br>";
	if($i_days >= 365)
	{
	    $i_years = floor($i_days / 365);    
	    $i_days = $i_days % 365;
	    $r = $i_years;
	    if($i_years>1)
	    {
		$r .= " years ";
	    }
	    else
	    {
		$r .= " year ";
	    }
	    if($i_days!=0)
	    {
		$r .= $i_days . " days ";
	    }
	}
	else
	{
	    $r .= "$i_days days ";
	}
    }
    if ($r_hours > 0) $r .= "$r_hours hours ";
    if ($r_minutes > 0) $r .= "$r_minutes min";
    //else $r .= "less than a minute";
        								    
    return $r;
}

/**************************************************************
Purpose: display paging navigation
Parameters: limit - the current limit
            listsize - the size of the list
            fulllistsize - the size of the full list
            page - the page to send links from pages

return String (the constracted table)
***************************************************************/
function show_paging($limit, $listsize, $fulllistsize, $page) {
	$retString = "";
	// Page numbers of navigation
	$pn = 15;
	
	$retString .= "<br><table width=\"99%\"><tbody><tr><td width=\"3%\" nowrap><b>������:</b></td><td align=\"center\">";
	// Deal with previous page
	if ($limit!=0) {
		$newlimit = $limit - $listsize;
		$retString .= "<a href=\"".$page."?limit=".$newlimit."\"><b>�����������</b></a>&nbsp;|&nbsp;";
	} else {
		$retString .= "<b>�����������</b>&nbsp;|&nbsp;";
	}
	// Deal with pages
	if (ceil($fulllistsize / $listsize) <= $pn/3)
	{ 
		// Show all
		$counter = 0;
		while ($counter * $listsize < $fulllistsize) {
			$aa = $counter + 1;
			if ($counter * $listsize == $limit) {
				$retString .= "<b>".$aa."</b>&nbsp;";
			} else {
				$newlimit = $counter * $listsize;
				$retString .= "<b><a href=\"".$page."?limit=".$newlimit."\">".$aa."</a></b>&nbsp;";
			}
			$counter++;
		}
	} elseif ($limit / $listsize < ($pn/3)+3) {
		// Show first 10
		$counter = 0;
		while ($counter * $listsize < $fulllistsize && $counter < $pn/3*2) {
			$aa = $counter + 1;
			if ($counter * $listsize == $limit) {
				$retString .= "<b>".$aa."</b>&nbsp;";
			} else {
				$newlimit = $counter * $listsize;
				$retString .= "<b><a href=\"".$page."?limit=".$newlimit."\">".$aa."</a></b>&nbsp;";
			}
			$counter++;
		}
		$retString .= "<b>...</b>&nbsp;";
		// Show last 5
		$counter = ceil($fulllistsize / $listsize) - ($pn/3);
		while ($counter * $listsize < $fulllistsize) {
			$aa = $counter + 1;
			if ($counter * $listsize == $limit) {
				$retString .= "<b>".$aa."</b>&nbsp;";
			} else {
				$newlimit = $counter * $listsize;
				$retString .= "<b><a href=\"".$page."?limit=".$newlimit."\">".$aa."</a></b>&nbsp;";
			}
			$counter++;
		}
	} elseif ($limit / $listsize >= ceil($fulllistsize / $listsize) - ($pn/3)-3) {
		// Show first 5
		$counter = 0;
		while ($counter * $listsize < $fulllistsize && $counter < ($pn/3)) {
			$aa = $counter + 1;
			if ($counter * $listsize == $limit) {
				$retString .= "<b>".$aa."</b>&nbsp;";
			} else {
				$newlimit = $counter * $listsize;
				$retString .= "<b><a href=\"".$page."?limit=".$newlimit."\">".$aa."</a></b>&nbsp;";
			}
			$counter++;
		}
		$retString .= "<b>...</b>&nbsp;";
		// Show last 10
		$counter = ceil($fulllistsize / $listsize) - ($pn/3*2);
		while ($counter * $listsize < $fulllistsize) {
			$aa = $counter + 1;
			if ($counter * $listsize == $limit) {
				$retString .= "<b>".$aa."</b>&nbsp;";
			} else {
				$newlimit = $counter * $listsize;
				$retString .= "<b><a href=\"".$page."?limit=".$newlimit."\">".$aa."</a></b>&nbsp;";
			}
			$counter++;
		}
	} else {
		// Show first 5
		$counter = 0;
		while ($counter * $listsize < $fulllistsize && $counter < ($pn/3)) {
			$aa = $counter + 1;
			if ($counter * $listsize == $limit) {
				$retString .= "<b>".$aa."</b>&nbsp;";
			} else {
				$newlimit = $counter * $listsize;
				$retString .= "<b><a href=\"".$page."?limit=".$newlimit."\">".$aa."</a></b>&nbsp;";
			}
			$counter++;
		}
		$retString .= "<b>...</b>&nbsp;";
		// Show middle 5
		$counter = ($limit / $listsize) - 2;
		$top = $counter + 5;
		while ($counter * $listsize < $fulllistsize && $counter < $top) {
			$aa = $counter + 1;
			if ($counter * $listsize == $limit) {
				$retString .= "<b>".$aa."</b>&nbsp;";
			} else {
				$newlimit = $counter * $listsize;
				$retString .= "<b><a href=\"".$page."?limit=".$newlimit."\">".$aa."</a></b>&nbsp;";
			}
			$counter++;
		}
		$retString .= "<b>...</b>&nbsp;";
		// Show last 5
		$counter = ceil($fulllistsize / $listsize) - ($pn/3);
		while ($counter * $listsize < $fulllistsize) {
			$aa = $counter + 1;
			if ($counter * $listsize == $limit) {
				$retString .= "<b>".$aa."</b>&nbsp;";
			} else {
				$newlimit = $counter * $listsize;
				$retString .= "<b><a href=\"".$page."?limit=".$newlimit."\">".$aa."</a></b>&nbsp;";
			}
			$counter++;
		}		
	}
	// Deal with next page
	if ($limit + $listsize >= $fulllistsize) {
		$retString .= "|&nbsp;<b>�������</b>";
	} else {
		$newlimit = $limit + $listsize;
		$retString .= "|&nbsp;<a href=\"".$page."?limit=".$newlimit."\"><b>�������</b></a>";
	}
	$retString .= "</td></tr></tbody></table>";
	
	return $retString;
}

?>