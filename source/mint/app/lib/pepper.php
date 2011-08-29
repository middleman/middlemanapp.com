<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Pepper Constructor
 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 

class Pepper
{
	var $Mint;
	var $pepperId;    
	var $filter;
	var $version	= 1; // eg. 1 = 0.01, 200 = 2.0
	var $info		= array
	(
		'pepperName'			=> '',
		'pepperUrl'				=> '',
		'pepperDesc'			=> '',
		'developerName'			=> '',
		'developerUrl'			=> '',
		'additionalDevelopers'	=> array() // an array indexed by devloper name containing their url
	);
	var $panes		= array();
	var $oddPanes	= array(); // an array of panes that will break with fixed pane height styles unless treated differently (eg. the two column Overview tab and graph tabs of the Visits and Feeds panes)
	var $prefs		= array();
	var $data		= array();
	var $manifest	= array(); // an multi-dimensional hash indexed by database table names, nested hashes indexed by database column name with values defining each column
	var $moderate	= array(); // an array of Pepper created tables whose size are not actively moderated by custom code, each table must have an id and dt column
	
	/**************************************************************************
	 Pepper Constructor											DO NOT OVERLOAD
	 
	 Takes care of getting a reference to Mint and loading Pepper data and 
	 preferences. Do NOT overload this constructor in your Pepper. 
	 
	 If you need to perform additional actions onload, overload the 
	 onPepperLoad() method in your Pepper's class.
	 **************************************************************************/
	function Pepper($pepperId)
	{
		global $Mint;
		$this->Mint		=& $Mint;
		$this->db		= $this->Mint->db;
		$this->pepperId	= $pepperId;
		
		if ($this->Mint->version < 200)
		{
			$error = 'Unlicensed use of the Mint 2 Pepper class';
			$this->Mint->logErrorNote('<p>Mint has detected the unlicensed use of the Mint 2 Pepper class on a '.$this->Mint->getFormattedVersion().' installation. Mint 2 is a paid upgrade. Please login into the <a href="http://www.haveamint.com/account/">Mint Account Center</a> and upgrade the license for this domain.</p>');
			$this->Mint->dropError($error);
			$this->Mint->logError($error, 2);
		}
		// $this->onPepperLoad(); called by Mint after prefs and data are retrieved
	}
	
	/**************************************************************************
	 onPepperLoad() 
	 
	 This optional handler is called immediately after Mint loads the Pepper. 
	 To be used in Pepper maintenance routines and as a replacement for 
	 individual Pepper constructors.
	 **************************************************************************/
	function onPepperLoad()
	{
		return true;
	}
	
	/**************************************************************************
	 onUpdate()
	 
	 Called when Mint has been updated. Useful for importing data from versions
	 of Mint prior to 1.2.
	 **************************************************************************/
	function onUpdate()
	{
		return true;
	}
	
	/**************************************************************************
	 update()
	 
	 Mint will compare the version number it has on file for this Pepper against
	 the hardcoded version and call this method if the latter is greater. It is
	 up to the Pepper developer to detect the previous version and update 
	 accordingly.
	
	 Version numbers should be 100 for every full release, eg. 1.0 = v100
	 
	 $Mint->syncPepper() is called after the update and synchronizes Mint's
	 internal version and tab lookup. Adding, removing, or renaming panes of 
	 existing Pepper isn't recommended.
	 **************************************************************************/
	function update()
	{
		return true;
	}
	
	/**************************************************************************
	 isCompatible()
	 
	 It is each Pepper's responsibility to check compatibility with the server 
	 software and the version of Mint installed. Returns a two index array. The 
	 first is a boolean indicating whether this Pepper is compatible or not. The
	 second, optional index provides an upgrade or helpful message specific to 
	 the server software or current version of Mint as a formatted HTML string. 
	 Use HERE doc syntax for complex messages.
	 
	 Please provide a helpful explanation (reasons/upgrade info) if your Pepper 
	 is not compatible.
	 **************************************************************************/
	function isCompatible()
	{
		return array
		(
			'isCompatible'	=> true,
			'explanation'	=> '<p>This Pepper does not check for compatibility with this version of Mint. Please contact the Pepper developer if you have any questions or problems with installation or use of this Pepper.</p>'
		);
	}
	
	/**************************************************************************
	 onInstall() 
	 
	 Called once immediately after the Pepper has been installed. Useful for 
	 notifying existing Pepper of the new Pepper's presence--this method is not
	 intended for adding columns or tables to the Mint database. Please use the
	 $manifest and $moderate properties of the Pepper class to add columns or
	 custom tables to the Mint Database.
	 **************************************************************************/
	function onInstall()
	{
		return;
	}
	
	/**************************************************************************
	 onJavaScript()
	 Outputs JavaScript responsible for extracting the necessary values (if any)
	 tracked by this plug-in. Does not return a string. echo or print() any
	 return output. You may also push an external file out to the browser as in 
	 the Default Pepper.
	 
	 JavaScript should follow format of the new SI object to prevent collisions
	 with local code. Never overwrite a default element event handler like 
	 window.onload, body.onload or a.onclick.
	 **************************************************************************/
	function onJavaScript() 
	{
		return;
	}
	
	/**************************************************************************
	 onRecord()
	 
	 Operates on existing $_GET values, values generated as a result of optional 
	 JavaScript output or existing $_SERVER variables and returns an associative
	 array with a column name as the index and the value to be stored in that 
	 column as the value.
	 **************************************************************************/
	function onRecord() 
	{	
 		return array();
	}
	
	/**************************************************************************
	 onDisplaySupplemental()
	
	 Any additional CSS or JavaScript required by a pane. CSS should be included
	 either via the <link> tag or inline in a <style> tag. JavaScript should be
	 enclosed in a <script> tag.
	 **************************************************************************/
	function onDisplaySupplemental($pane) 
	{
		return '';
	}
	
	/**************************************************************************
	 onDisplay()
	 Produces what the user sees when they are browsing their Mint install.
	 
	 Returns an HTML string for the requested pane and tab. Pane and Tab are 
	 requested by name. The $column and $sort arguments don't do anything 
	 currently.
	 **************************************************************************/
	function onDisplay($pane, $tab, $column = '', $sort = '') 
	{
		return '&nbsp;';
	}
	
	/**************************************************************************
	 onAfterDisplay()
	
	 Gives Pepper the ability to write display-side Javascript to manipulate 
	 the Mint interface in new ways. While onDisplaySupplemental() provides a
	 hook for pane-specific CSS or JavaScript additions, onAfterDisplay()
	 doesn't even require a paned Pepper.JavaScript should be enclosed in its
	 own <script> tag and is output after all panes but before the footer.
	 **************************************************************************/
	function onAfterDisplay() 
	{
		return '';
	}
	
	/**************************************************************************
	 onDisplayPreferences()
	 
	 Should return an assoicative array (indexed by pane name) that contains the
	 HTML contents of that pane's preference. Preferences used by all panes in 
	 this Pepper should be indexed as 'Global' and appear first in the array.
	 **************************************************************************/
	function onDisplayPreferences() 
	{
		return array();
	}
	
	/**************************************************************************
	 onSavePreferences()
	 
	 Should validate and assign user input to the $this->prefs array. This array
	 along with the $this->data array are now automatically saved to the 
	 database by Mint.
	 **************************************************************************/
	function onSavePreferences() 
	{
		return true;
	}
	
	/**************************************************************************
	 onCustom()
	 
	 This optional handler is called when `custom` appears in the query string 
	 of a request or a form is posted with `MintPath` set to 'Custom'. Your 
	 Pepper is responsible for providing additional variables and logic to 
	 handle those variables. The function can return anything or nothing--
	 whatever is appropriate for its use.
	 
	 The $this->prefs array and the $this->data array are automatically saved to
	 the database by Mint after this handler is called.
	 **************************************************************************/
	function onCustom()
	{
		return;
	}
	
	/**************************************************************************
	 onRss()
	 
	 This optional handler is called when `RSS` appears in the query string.
	 Must return a specially formatted rssData array (similar to the tableData
	 array used by Mint->generateTable()).
	 **************************************************************************/
	function onRss()
	{
		return array();
	}
	
	/**************************************************************************
	 onWidget()
	 
	 Returns HTML specially formatted for the Junior Mint Dashboard Widget. 
	 Currently a closed system used only by Default Pepper.
	 **************************************************************************/
	function onWidget() 
	{
		return;
	}
	
	/**************************************************************************
	 onTidy()
	 
	 This function is obsolete. If your Pepper adds a table to the Mint 
	 database and you have not written custom code to manage the growth of that
	 table you must add the table name to your Pepper's moderate property.
	
	 Please see the list of properties at the top of this file for more info.
	 **************************************************************************/
	function onTidy()
	{
		return;
	}
	
	/**************************************************************************
	 onUninstall()
	 
	 This is a Pepper's dying breath. Use it to notify other Pepper that it is
	 being uninstalled (most useful for tandem preferences). Mint still handles
	 uninstallation, use only for notification.
	 **************************************************************************/
	function onUninstall()
	{
		return;
	}
	
	/**************************************************************************
	 getHTML_Graph()											DO NOT OVERLOAD
	 
	 Sample $graphData:
	
	 	$graphData	= array
		(
			'titles' => array
			(
				'background' => 'Total Visits',
				'foreground' => 'Unique Visits'
			),
			'key' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			)
			'rows' => array
			(
				array
				(
					50,			// background value
					25,			// foreground value
					'Weekend',	// bar label
					'Saturday'	// bar title
					true		// whether or not this bar is accented
				)
			)
		);
		
	 **************************************************************************/
	function getHTML_Graph($high = 0, $graphData = array())
	{
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

		$html	= '<div class="graph">';
		$html  .= '<table><tr>';

		$barCount = count($graphData['bars']);
		for ($i = 0; $i < $barCount; $i++)
		{
			$barData = $graphData['bars'][$i];
			$uniqueHeight	= floor(311 * $barData[1] / ($high + $scale));
			$totalMargin	= floor(311 * ($barData[0] - $barData[1]) / ($high + $scale)) - 5;
			$barPadding		= 306 - ($uniqueHeight + $totalMargin);

			$backgroundTitle = (isset($graphData['titles']['background']) && !empty($graphData['titles']['background'])) ? ' title="'.$barData[3].'\'s '.$graphData['titles']['background'].': '.number_format($barData[0]).'"' : '';
			$foregroundTitle = (isset($graphData['titles']['foreground']) && !empty($graphData['titles']['foreground'])) ? ' title="'.$barData[3].'\'s '.$graphData['titles']['foreground'].': '.number_format($barData[1]).'"' : '';

			$html .= (isset($barData[4]) && $barData[4]) ? '<td class="accent">':'<td>';
			$html .= '<div class="bar" style="padding-top: '.$barPadding.'px">';
			$html .= (!empty($barData[2])) ? '<div class="label">'.$barData[2].'</div>' : '';
			$html .= '<div class="total"'.$backgroundTitle.'><span></span>';
			$html .= '<div class="unique"'.$foregroundTitle.' style="margin-top: '.$totalMargin.'px; height: '.$uniqueHeight.'px;">';
			$html .= '<span'.(($barData[0] == $barData[1]) ? ' class="equal"' : '').'></span>';
			$html .= ($i == $barCount - 1) ? '<div class="key"'.$backgroundTitle.'>'.$graphData['key']['background'].' <em'.$foregroundTitle.'>'.$graphData['key']['foreground'].'</em></div>' : '';
			$html .= '</div></div></div></td>';
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
		$html  .= '<ul>';
		$html  .= '<li class="tick-0">'.number_format($scale * 5).$unit.'</li>';
		$html  .= '<li class="tick-1">'.number_format($scale * 4).$unit.'</li>';
		$html  .= '<li class="tick-2">'.number_format($scale * 3).$unit.'</li>';
		$html  .= '<li class="tick-3">'.number_format($scale * 2).$unit.'</li>';
		$html  .= '<li class="tick-4">'.number_format($scale).$unit.'</li>';
		$html  .= '<li class="tick-5">0</li>';
		$html  .= '</ul>';
		$html  .= '</div>';

		return $html;
	}
	
	/**************************************************************************
	 generateFilterList()										DO NOT OVERLOAD
	 
	 Returns an HTML filter list
	 **************************************************************************/
	function generateFilterList($tab = '', $filters = array(), $shareWithOtherTabs = array())
	{
		$html = "\t<ul class=\"filters\">";
		$tabClean = str_replace(' ', '', $tab);
		if (!empty($shareWithOtherTabs))
		{
			foreach($shareWithOtherTabs as $i => $otherTab)
			{
				$shareWithOtherTabs[$i] = str_replace(' ', '', $otherTab);
			}
		}
		
		if (isset($_GET['filter']))
		{
			$active_filter = $filters[$_GET['filter']];
		}
		else if (isset($_COOKIE["MintPepper{$this->pepperId}{$tabClean}Filter"]))
		{
			$active_filter = $_COOKIE["MintPepper{$this->pepperId}{$tabClean}Filter"];
		}
		else
		{
			$active_filter = reset($filters);
		}
		
		$this->filter = $active_filter;
		
		$filter_count = count($filters);
		$j = 0;
		foreach ($filters as $filter => $value) 
		{
			$classes = array();
			
			if ((string) $active_filter == (string) $value)
			{
				$classes[] = 'active';
			}
			if ($filter_count == 1)
			{
				$classes[] = 'only-child';
			}
			else if ($j == 0)
			{
				$classes[] = 'first-child';
			}
			else if ($j == $filter_count - 1)
			{
				$classes[] = 'last-child';
			}
			
			$class 		= (!empty($classes))?' class="'.join(' ', $classes).'"':'';
			$otherTabs	= (!empty($shareWithOtherTabs)) ? " SI.Cookie.set('MintPepper{$this->pepperId}".join("Filter', '{$value}'); SI.Cookie.set('MintPepper{$this->pepperId}", $shareWithOtherTabs)."Filter', '{$value}');" : '';
			$html .= "<li$class><a href=\"#\" onclick=\"SI.Mint.loadFilter('{$tab}',this); SI.Cookie.set('MintPepper{$this->pepperId}{$tabClean}Filter', '{$value}');{$otherTabs} return false;\">{$filter}</a></li>";
			$j++;
		}
		$html .= "</ul>\r";
		
		return $html;
	}
	
	/**************************************************************************
	 getInstalledVersion()										DO NOT OVERLOAD

	 Returns the currently installed version of this Pepper. Useful during the
	 update() event handler.
	 **************************************************************************/
	function getInstalledVersion()
	{
		$version = 0;
		if (isset($this->pepperId))
		{
			$version = $this->Mint->cfg['pepperShaker'][$this->pepperId]['version'];
		}
		return $version;
	}
		
	/**************************************************************************
	 getFormattedVersion()										DO NOT OVERLOAD
	 
	 Returns the version number formatted for display
	 **************************************************************************/
	function getFormattedVersion() 
	{
		$len = (substr($this->version.'',-1) == '0')?1:2;
		return '<abbr title="v'.str_pad($this->version,3,'0',STR_PAD_LEFT).'">'.number_format($this->version/100,$len).'</abbr>';
	}
	
	/**************************************************************************
	 query()													DO NOT OVERLOAD
	 
	 Handler for mysql_query, writes query to $Mint->output
	 **************************************************************************/
	function query($query) 
	{
		$this->Mint->logBenchmark('query("'.substr($query, 0, 24).'...") {');
		
		$this->Mint->queries[] = $query;
		if (!($result = mysql_query($query)))
		{
			$this->Mint->logError('MySQL Error (from '.$this->info['pepperName'].'): '.mysql_error().'. ('.mysql_errno().')<br />Query: '.$query);
			$result = false;
		}
		
		$this->Mint->logBenchmark('}');
		return $result;
	}
	
	/**************************************************************************
	 Shortcuts to Mint methods									DO NOT OVERLOAD
	 **************************************************************************/
	function escapeSQL($str) { return $this->Mint->escapeSQL($str); }
	
	/**************************************************************************
	 sanitizeUrl()												DO NOT OVERLOAD
	
	 Strips malicious code from a url.
	 **************************************************************************/
	function sanitizeUrl($url)
	{
		$javascript = str_replace(' ', '\s*', ' j a v a s c r i p t :');
		return preg_replace("#^{$javascript}.*#i", '', $url);
	}
}