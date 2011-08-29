<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Utility Path
 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Mint: Visits Editor</title>
<style type="text/css" title="text/css" media="screen">
/* <![CDATA[ */
body
{
	position: relative;
	background-color: #FFF;
	margin: 0;
	padding: 48px 0;
	font: 76%/1.6em "Lucida Grande", Verdana, Arial, sans-serif;
	color: #333;
	text-align: center;
}

div
{
	width: 400px;
	margin: 0 auto;
	text-align: left;
}

h1
{
	font-weight: normal;
	line-height: 1.2em;
}

table
{
	border-collapse: collapse;
	border-bottom: 1px solid #DDD;
	margin: 0 0 1.0em;
}

th,td
{
	text-align: left;
	vertical-align: top;
	white-space: nowrap;
}

th.sub
{
	font-weight: normal;
	font-size: 0.9em;
	border-top: 2px solid #999;
	background-color: #DDD;
	padding: 2px 8px;
}

td
{
	border-top: 1px solid #DDD;
	padding: 2px 4px;
}

tr.alt td
{
	background-color: #EEE;
}

tr.alert td
{
	color: #FFF;
	background-color: #F11 !important;
	border-top: 1px solid #C00 !important;
}

input[type=text]
{
	width: 64px;
}

td span 
{
	font-size: 0.82em;
}

th#notice
{
	border: 1px solid #F2F2C2;
	background-color: #FFC;
	padding: 0.6em 1.0em;
}

select
{
	font-size: 0.82em;
}

/* ]]> */
</style>
</head>
<body>
<div>
<h1>Mint says, &#8220;<em>Gulp</em>, I sure hope you know what you are doing!&#8221;</h1>

<p>This form is <strong>supposed to be</strong> intimidating. If you are not intimidated, continue reading until you at least understand why you <em>should be</em>.</p>

<p>This form provides direct access to the visits property of the Default Pepper's data array. It is <em>highly</em> recommended that you make a full backup of your Mint database using either the Backup/Restore Pepper or third-party tools like PhpMyAdmin before playing with any of these numbers.</p>

<p>Potential issues with the data are highlighted with a red background. An issue is usually an invalid timestamp used to index the collection of hits.</p>

<ul>
	<li>Valid timestamps for Past Week data should be consecutive days starting at 12:00am or 0:00</li>
	<li>Valid timestamps for Past Month data should be consecutive Sundays starting at 12:00am or 0:00</li>
	<li>Valid timestamps for Past Year should be the first day of consecutive months starting at 12:00am or 0:00</li>
</ul>

<p>If a corrected timestamp conflicts with an existing timestamp, saving this data may cause data loss. To avoid this potential problem, do not correct the invalid timestamp. Instead, add its Total and Unique columns to the values in the valid row. Then choose to "Delete" the invalid row and save.</p>

<form method="post" action="">
<?php

$months = array
(
	1 => 'January',
	2 => 'February',
	3 => 'March',
	4 => 'April',
	5 => 'May',
	6 => 'June',
	7 => 'July',
	8 => 'August',
	9 => 'September',
	10 => 'October',
	11 => 'November',
	12 => 'December'
);

function option($display, $value, $default)
{
	return '<option value="'.$value.'"'.(($default == $value)?' selected="selected"':'').'>'.$display.'</option>';
}

function optionsForYearSelect($year)
{
	$html = '';
	
	for ($i = 2005; $i <= 2012; $i++)
	{
		$html .= option($i, $i, $year);
	}
	return $html;
}

function optionsForMonthSelect($month)
{
	global $months;

	$html = '';
	
	foreach ($months as $value => $display)
	{
		$html .= option(substr($display, 0, 3), $value, $month);
	}
	return $html;
}

function optionsForDaySelect($day)
{
	$html = '';
	
	for ($i = 1; $i <= 31; $i++)
	{
		$html .= option($i, $i, $day);
	}
	return $html;
}

function optionsForHourSelect($hour)
{
	$html = '';
	
	for ($i = 0; $i <= 23; $i++)
	{
		$html .= option($i.':00', $i, $hour);
	}
	return $html;
}

function array_rename_key($array, $originalIndex, $replacementIndex)
{
	$keys	= array_keys($array);
	$values	= array_values($array);
	
	$key = array_search($originalIndex, $keys);
	if ($key !== false)
	{
		$keys[$key] = $replacementIndex;
	}
	
	$array = array();
	foreach($keys as $i => $key)
	{
		$array[$key] = $values[$i];
	}
	
	return $array;
}

// Get incorrect month visits
$pepper = $Mint->getPepperByClassName('SI_Default');
$visits	= $pepper->data['visits'];
$html = '';

if (isset($_POST['action']) && $_POST['action'] == 'edit-visits')
{	
	$control = unserialize(stripslashes($_POST['control']));
	$editedVisits = $_POST['visits'];
	
	foreach ($_POST['visits'] as $grouping => $existing)
	{
		foreach ($existing as $timestamp => $hits)
		{
			// Delete
			if (isset($_POST['delete'][$grouping]) && in_array($timestamp, $_POST['delete'][$grouping]))
			{
				# Delete this timestamp!
				unset($visits[$grouping][$timestamp]);
				continue;
			}
			
			// Update counts
			$diffTotal = $hits['total'] - $control[$grouping][$timestamp]['total'];
			$visits[$grouping][$timestamp]['total'] += $diffTotal;
			
			$diffUnique = $hits['unique'] - $control[$grouping][$timestamp]['unique'];
			$visits[$grouping][$timestamp]['unique'] += $diffUnique;
			
			// Update timestamps
			if (isset($_POST['timestamps'][$grouping][$timestamp]))
			{
				$ts = $_POST['timestamps'][$grouping][$timestamp];
				$newTimestamp = $Mint->offsetMakeGMT($ts['hour'], 0, 0, $ts['month'], $ts['day'], $ts['year']);
				
				if ($timestamp != $newTimestamp)
				{
					$visits[$grouping] = array_rename_key($visits[$grouping], $timestamp, $newTimestamp);
				}
			}
		}
	}
	
	$pepper->data['visits'] = $visits;
	$Mint->_save();
	
	$html .= '<tr><th colspan="4" id="notice">Saved.</th></tr>';
}

// Comes after editing
echo '<input type="hidden" name="control" value=\''.serialize($visits).'\' />';

$groupings = array
(
	'Since Install',
	'Past Day (by hour)',
	'Past Week (by day)',
	'Past Month (by week)',
	'Past Year (by month)'
);

foreach ($visits as $grouping => $existing)
{
	$html .= '<tr>
	<th colspan="4" class="sub">'.$groupings[$grouping].'</th>
</tr>';
	$alt = false;
	foreach ($existing as $timestamp => $hits)
	{
		$classes = array();
		$displayTimestamp = (!$grouping)?$Mint->cfg['installDate']:$timestamp;
		$disabled = (!$grouping)?' disabled="disabled" ':'';
		if ($alt) { $classes[] = 'alt'; }
		$alt = !$alt;
		
		$year	= $Mint->offsetDate('Y', $displayTimestamp);
		$month	= $Mint->offsetDate('n', $displayTimestamp);
		$day	= $Mint->offsetDate('j', $displayTimestamp);
		$hour	= $Mint->offsetDate('G', $displayTimestamp);
		
		if 
		(
			($grouping > 1 && $hour > 0) ||
			($grouping == 4 && $day != 1)
		)
		{
			$classes[] = 'alert';
		}
		
		$class = (!empty($classes))?' class="'.join(' ', $classes).'"':'';
		
		$html .= '<tr'.$class.'>
	<td>
		<select id="timestamps-'.$grouping.'-'.$timestamp.'-year" name="timestamps['.$grouping.']['.$timestamp.'][year]" onchange="validateDateGroup(\'timestamps-'.$grouping.'-'.$timestamp.'\');"'.$disabled.'>'.optionsForYearSelect($year).'</select>
		<select id="timestamps-'.$grouping.'-'.$timestamp.'-month" name="timestamps['.$grouping.']['.$timestamp.'][month]" onchange="validateDateGroup(\'timestamps-'.$grouping.'-'.$timestamp.'\');"'.$disabled.'>'.optionsForMonthSelect($month).'</select>
		<select id="timestamps-'.$grouping.'-'.$timestamp.'-day" name="timestamps['.$grouping.']['.$timestamp.'][day]" onchange="validateDateGroup(\'timestamps-'.$grouping.'-'.$timestamp.'\');"'.$disabled.'>'.optionsForDaySelect($day).'</select>
		at <select name="timestamps['.$grouping.']['.$timestamp.'][hour]"'.$disabled.'>'.optionsForHourSelect($hour).'</select>
	</td>';
	
		$html .= '
	<td><input type="text" value="'.$hits['total'].'" name="visits['.$grouping.']['.$timestamp.'][total]" /></td>
	<td><input type="text" value="'.$hits['unique'].'" name="visits['.$grouping.']['.$timestamp.'][unique]" /></td>
	<td><input type="checkbox" value="'.$timestamp.'" name="delete['.$grouping.'][]" '.$disabled.'/> <span>Delete</span></td>
</tr>';
	}
}

?>
<script type="text/javascript">
// <![CDATA[
var months		= ['','<?php echo join("','", $months)?>'];
var daysInMonth = [0,31,28,31,30,31,30,31,31,30,31,30,31];

function validateDateGroup(groupId)
{
	var daySelect	= document.getElementById(groupId + '-day');
	var monthSelect = document.getElementById(groupId + '-month');
	var yearSelect	= document.getElementById(groupId + '-year');
	
	var day		= daySelect.options[daySelect.selectedIndex].value;
	var month	= monthSelect.options[monthSelect.selectedIndex].value;
	var year	= yearSelect.options[yearSelect.selectedIndex].value;
	
	daysInMonth[2] = (year%4 == 0)?29:28;
	totalDays = daysInMonth[month];
	
	if (day > totalDays)
	{
		var diff = day - totalDays;
		var warning = 'Invalid date. There ' + pluralize(totalDays, 'is', 'are') + ' only ' + totalDays + ' ' + pluralize(totalDays, 'day', 'days') + ' in ' + months[month]
		warning += (month == 2 && day == 29)?' in ' + year:'';
		warning +=	'. Month and day values have been adjusted accordingly to: ' + months[parseInt(month) + 1] + ' ' + diff + '.';
		
		alert(warning);
		
		daySelect.selectedIndex = diff - 1;
		monthSelect.selectedIndex = month;
	}
}

function pluralize(num, single, plural)
{
	return (num == 1)?single:plural;
}

function clearNotice()
{
	var notice	= document.getElementById('notice');
	if (notice)
	{
		notice.style.display = 'none';
	}
}

window.onload = function()
{
	window.setTimeout('clearNotice()', 15 * 1000);
};

// ]]>
</script>
<table>
	<tr>
		<th>Date range begins</th>
		<th>Totals</th>
		<th colspan="2">Uniques</th>
	</tr>
<?php echo $html; ?>
</table>
<input type="hidden" name="action" value="edit-visits" />
<input type="submit" value="Save Changes" />
</form>
</div>
</body>
</html>