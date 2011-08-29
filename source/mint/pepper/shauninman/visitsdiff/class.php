<?php
/******************************************************************************
 Pepper
 
 Developer		: Shaun Inman
 Plug-in Name	: Default Pepper
 
 http://www.shauninman.com/

 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file
$installPepper = "SI_VisitsDiff";

class SI_VisitsDiff extends Pepper
{
	var $version	= 100;
	var $info		= array
	(
		'pepperName'	=> 'Visits Diff',
		'pepperUrl'		=> 'http://www.haveamint.com/',
		'pepperDesc'	=> 'The Visits Diff Pepper compares total page views and unique visitors from the current timeframe to the previous one. The Past Day tab compares hours from today to the same hours yesterday, the Past Week tab compares the days from the past week to the same day from the previous week and so on. Dark green indicates an increase, light green a decrease.',
		'developerName'	=> 'Shaun Inman',
		'developerUrl'	=> 'http://www.shauninman.com/'
	);
	var $panes		= array
	(
		'Visits Diff'	=> array
		(
			'Past Day',
			'Past Week',
			'Past Month',
			'Past Year'
		),
	);
	var $oddPanes	= array
	(
		'Visits Diff'
	);
	
	/**************************************************************************
	 isCompatible()
	 **************************************************************************/
	function isCompatible()
	{
		if ($this->Mint->version < 215)
		{
			$compatible = array
			(
				'isCompatible'	=> false,
				'explanation'	=> '<p>This Pepper requires Mint 2.15. Mint 2, a paid upgrade from Mint 1.x, is available at <a href="http://www.haveamint.com/">haveamint.com</a>.</p>'
			);
		}
		else
		{
			$compatible = array
			(
				'isCompatible'	=> true,
			);
		}
		return $compatible;
	}
	
	/**************************************************************************
	 onDisplay()
	 **************************************************************************/
	function onDisplay($pane, $tab, $column = '', $sort = '')
	{
		$html = '';
		
		switch($pane) 
		{
		/* Visits *************************************************************/
			case 'Visits Diff': 
				switch($tab) 
				{	
					case 'Past Day':
						$html .= $this->getHTML_VisitsDay();
					break;
					
					case 'Past Week':
						$html .= $this->getHTML_VisitsWeek();
					break;
					
					case 'Past Month':
						$html .= $this->getHTML_VisitsMonth();
					break;
					
					case 'Past Year':
						$html .= $this->getHTML_VisitsYear();
					break;
				}
			break;
		}
		return $html;
	}
	
	/**************************************************************************
	 getHTML_Visits()
	
	 Retrieves the visits array from the Default Pepper for the given index.
	 **************************************************************************/
	function getVisits($i = null)
	{
		$visits = $this->Mint->pepper[0]->data['visits'];
		return isset($i) ? $visits[$i] : $visits;
	}
	
	/**************************************************************************
	 getHTML_FiltersList()
	
	 Generates the filter list for the given tab.
	 **************************************************************************/
	function getHTML_FiltersList($tab)
	{
		$filters = array
		(
			'Total Page Views'	=> 0,
			'Unique Visitors'	=> 1
		);
		
		return $this->generateFilterList($tab, $filters, $this->panes['Visits Diff']);
	}
	
	/**************************************************************************
	 tabulateDifference()
	
	 Given a visits array index and two timestamps returns the total and unique
	 differences between the two timestamps.
	 **************************************************************************/
	function tabulateDifference($i, $timestamp, $paststamp, $percentage = 1)
	{
		$visits	= $this->getVisits();
		
		if (isset($visits[$i][$timestamp]))
		{
			$u1 = $visits[$i][$timestamp];
		}
		else
		{
			$u1 = array('total' => 0, 'unique' => 0);
		}
		
		if (isset($visits[$i][$paststamp]))
		{
			$u2 = $visits[$i][$paststamp];
		}
		else
		{
			$u2 = array('total' => 0, 'unique' => 0);
		}
		
		$d = array
		(
			'total'		=> $u1['total']  - floor($u2['total']  * $percentage),
			'unique'	=> $u1['unique'] - floor($u2['unique'] * $percentage)
		);
				
		return $d;
	}
	
	/**************************************************************************
	 getHTML_CompareGraph()
	 **************************************************************************/
	function getHTML_CompareGraph($high, $data)
	{
		$html 		= '';
		$label	 	= $this->filter ? 'Unique' : 'Total'; 
		
		$high *= 2;
		$baseScales = array(1.5, 2, 2.5, 3, 4, 5, 6, 7.5, 8, 10, 15, 20);
		$baseScale 	= ceil($high / 5);
		$scale		= 2;
		$places		= strlen($high) - 1;
		$multiplier	= 1;

		if ($places > 1)
		{
			$multiplier = pow(10, $places - 1);
		}

		foreach($baseScales as $base)
		{
			$scale = floor($base * $multiplier);
			if ($scale * 5 > $high)
			{
				$high = $scale * 5;
				break;
			}
		}
		
		$html  .= '<div class="graph compare-graph">';
		$html  .= '<table><tr>';

		$count = count($data['bars']);
		for ($i = 0; $i < $count; $i++)
		{
			$bar = $data['bars'][$i];
			
			$html .= (isset($bar[3]) && $bar[3]) ? '<td class="accent">':'<td>';
			$html .= '<div class="compare">';
			$html .= (!empty($bar[1])) ? '<div class="label">'.$bar[1].'</div>' : '';
			
			$height = ceil((abs($bar[0]) / ($high + $scale)) * 311);
			
			$title = "{$bar[2]}'s ".$label.': '.($bar[0] < 0 ? 'Down' : 'Up').' '.number_format(abs($bar[0]))." from ".$data['timeframe'];
			
			if ($bar[0] >= 0)
			{
				$html .= '<div title="'.$title.'" class="increase" style="height: '.$height.'px; margin-top: -'.$height.'px;"><span></span></div>';
			}
			else
			{
				$html .= '<div title="'.$title.'" class="decrease" style="height: '.$height.'px;"><span></span></div>';
			}
			$html .= '</div></td>';
		}

		$unit = '';
		if ($scale > 1000000)
		{
			$scale = $scale / 1000000;
			$unit = 'm';
		}

		if ($scale > 1000)
		{
			$scale = $scale / 1000;
			$unit = 'k';
		}

		$html  .= '</tr></table>';
		$html  .= '<ul class="spark-ticks">';
		$html  .= '<li class="tick-0">+'.number_format($scale * 2).$unit.'</li>';
		$html  .= '<li class="tick-1">+'.number_format($scale).$unit.'</li>';
		$html  .= '<li class="tick-2">0</li>';
		$html  .= '<li class="tick-3">-'.number_format($scale).$unit.'</li>';
		$html  .= '<li class="tick-4">-'.number_format($scale * 2).$unit.'</li>';
		$html  .= '</ul>';
		$html  .= '</div>';
		
		return $html;
	}
	
	/**************************************************************************
	 getHTML_VisitsDay()
	 **************************************************************************/
	function getHTML_VisitsDay() 
	{
		$now		= time();
		$high 		= 0;
		$hour 		= $this->Mint->getOffsetTime('hour');
		
		$html		= $this->getHTML_FiltersList('Past Day');
		$filter 	= $this->filter ? 'unique' : 'total';
		
		$data['timeframe'] = '24 hours ago';
		$bars		= array();
		
		// Past 24 hours
		for ($i = 0; $i < 24; $i++) 
		{
			$timestamp = $hour - $i * 60 * 60;
			$paststamp = $timestamp - 24 * 60 * 60;
			
			if ($i == 0)
			{
				$then = $hour + 60 * 60;
				$diff = $then - $now;
				$percentage = 1 - ($diff / (60 * 60));
			}
			else
			{
				$percentage = 1;
			}
			
			$d = $this->tabulateDifference(1, $timestamp, $paststamp, $percentage);
			$c = $d[$filter];
			
			$high	= (abs($c) > $high) ? abs($c) : $high;
			
			$twelve = $this->Mint->offsetDate('G', $timestamp) == 12;
			$twentyFour = $this->Mint->offsetDate('G', $timestamp) == 0;
			$hourLabel = $this->Mint->offsetDate('g', $timestamp);
			
			$bars[] = array
			(
				$c,
				($twelve) ? 'Noon' : (($twentyFour) ? 'Midnight' : (($hourLabel == 3 || $hourLabel == 6 || $hourLabel == 9) ? $hourLabel : '')),
				$this->Mint->formatDateRelative($timestamp, "hour"),
				($twelve || $twentyFour) ? 1 : 0
			);
		}
		$data['bars'] = array_reverse($bars);
		
		$html .= $this->getHTML_CompareGraph($high, $data);
		
		return $html;
	}
	
	/**************************************************************************
	 getHTML_VisitsWeek()
	 **************************************************************************/
	function getHTML_VisitsWeek() 
	{
		$now		= time();
		$high 		= 0;
		$day		= $this->Mint->getOffsetTime('today');
		
		$html		= $this->getHTML_FiltersList('Past Week');
		$filter 	= $this->filter ? 'unique' : 'total';
		
		$data['timeframe'] = '7 days ago';
		$bars		= array();
				
		// Past 7 days
		for ($i = 0; $i < 7; $i++) 
		{
			$timestamp = $day - $i * 24 * 60 * 60;
			$paststamp = $timestamp - 7 * 24 * 60 * 60;
			
			if ($i == 0)
			{
				$then = $day + 24 * 60 * 60;
				$diff = $then - $now;
				$percentage = 1 - ($diff / (24 * 60 * 60));
			}
			else
			{
				$percentage = 1;
			}
			
			$d = $this->tabulateDifference(2, $timestamp, $paststamp, $percentage);
			$c = $d[$filter];
			
			$high	= (abs($c) > $high) ? abs($c) : $high;
						
			$dayOfWeek = $this->Mint->offsetDate('w', $timestamp);
			$dayLabel = substr($this->Mint->offsetDate('D', $timestamp), 0, 2);
			
			$bars[] = array
			(
				$c,
				($dayOfWeek == 0) ? '' : (($dayOfWeek == 6) ? 'Weekend' : $dayLabel),
				$this->Mint->formatDateRelative($timestamp, "day"),
				($dayOfWeek == 0 || $dayOfWeek == 6) ? 1 : 0
			);
		}
		$data['bars'] = array_reverse($bars);
		
		$html .= $this->getHTML_CompareGraph($high, $data);
		
		return $html;
	}

	/**************************************************************************
	 getHTML_VisitsMonth()
	 **************************************************************************/
	function getHTML_VisitsMonth() 
	{
		$now		= time();
		$high 		= 0;
		$week 		= $this->Mint->getOffsetTime('week');
		
		$html		= $this->getHTML_FiltersList('Past Month');
		$filter 	= $this->filter ? 'unique' : 'total';

		$data['timeframe'] = '5 weeks ago';
		$bars		= array();
		
		// Past 5 weeks
		for ($i = 0; $i < 5; $i++)
		{
			$timestamp = $week - $i * 7 * 24 * 60 * 60;
			$paststamp = $timestamp - 5 * 7 * 24 * 60 * 60;
			
			if ($i == 0)
			{
				$then = $week + 7 * 24 * 60 * 60;
				$diff = $then - $now;
				$percentage = 1 - ($diff / (7 * 24 * 60 * 60));
			}
			else
			{
				$percentage = 1;
			}
			
			$d = $this->tabulateDifference(3, $timestamp, $paststamp, $percentage);
			$c = $d[$filter];
			
			$high	= (abs($c) > $high) ? abs($c) : $high;
			
			$bars[] = array
			(
				$c,
				$this->Mint->formatDateRelative($timestamp, "week", $i),
				$this->Mint->offsetDate('D, M j', $timestamp),
				($i == 0) ? 1 : 0
			);
		}
		$data['bars'] = array_reverse($bars);
		
		$html .= $this->getHTML_CompareGraph($high, $data);
		
		return $html;
	}

	/**************************************************************************
	 getHTML_VisitsYear()
	 **************************************************************************/
	function getHTML_VisitsYear() 
	{
		$now		= time();
		$high 		= 0;
		$month 		= $this->Mint->getOffsetTime('month');

		$html		= $this->getHTML_FiltersList('Past Year');
		$filter 	= $this->filter ? 'unique' : 'total';

		$data['timeframe'] = '12 months ago';
		$bars		= array();		

		// Past 12 months
		for ($i = 0; $i < 12; $i++)
		{	
			if ($i == 0)
			{
				$timestamp = $month;
				
				$days = $this->Mint->offsetDate('t', $this->Mint->offsetMakeGMT(0, 0, 0, $this->Mint->offsetDate('n', $month), 1, $this->Mint->offsetDate('Y', $month)));
				$then = $month + ($days * 24 * 60 * 60);
				$diff = $then - $now;
				$percentage = 1 - ($diff / ($days * 24 * 60 * 60));
			}
			else
			{
				$days 		= $this->Mint->offsetDate('t', $this->Mint->offsetMakeGMT(0, 0, 0, $this->Mint->offsetDate('n', $month)-1, 1, $this->Mint->offsetDate('Y', $month))); // days in the previous month
				$timestamp 	= $month - ($days * 24 * 60 * 60);
				
				$percentage = 1;
			}
			$month = $timestamp;
			$paststamp = $this->Mint->offsetMakeGMT(0, 0, 0, $this->Mint->offsetDate('n', $month), 1, $this->Mint->offsetDate('Y', $month) - 1);
			
			$d = $this->tabulateDifference(4, $timestamp, $paststamp, $percentage);
			
			$c = $d[$filter];
			
			$high	= (abs($c) > $high) ? abs($c) : $high;
			
			$bars[] = array
			(
				$c,
				($i == 0) ? 'This Month' : $this->Mint->offsetDate('M', $timestamp),
				$this->Mint->offsetDate('F', $timestamp),
				($i == 0) ? 1 : 0
			);
		}
		
		$data['bars'] = array_reverse($bars);
		
		$html .= $this->getHTML_CompareGraph($high, $data);

		return $html;
	}
}