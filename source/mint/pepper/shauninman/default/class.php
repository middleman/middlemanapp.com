<?php
/******************************************************************************
 Pepper
 
 Developer		: Shaun Inman
 Plug-in Name	: Default Pepper
 
 http://www.shauninman.com/

 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file
$installPepper = "SI_Default";

class SI_Default extends Pepper
{
	var $version	= 210;
	var $info		= array
	(
		'pepperName'	=> 'Default',
		'pepperUrl'		=> 'http://www.haveamint.com/',
		'pepperDesc'	=> 'The Default Pepper covers the basics. It is responsible for tracking the number of page views and unique visitors, where they are coming from and what they are looking at, as well as which search terms led them to your site.',
		'developerName'	=> 'Shaun Inman',
		'developerUrl'	=> 'http://www.shauninman.com/'
	);
	var $panes		= array
	(
		'Visits'	=> array
		(
			'Overview',
			'Past Day',
			'Past Week',
			'Past Month',
			'Past Year'
		),
		'Referrers'	=> array
		(
			'Newest Unique',
			'Most Recent',
			'Repeat',
			'Domains'
		),
		'Pages'	=> array
		(
			'Most Popular',
			'Most Recent',
			'Entry',
			'Watched'
		),
		'Searches'	=> array
		(
			'Most Common',
			'Most Recent',
			'Found'
		)
	);
	var $oddPanes	= array
	(
		'Visits'
	);
	var $prefs		= array
	(
		'condensedVisits'			=> 0,
		'trimPrefixIndex'			=> 1,
		'referrerTimespan'			=> 24,
		'ignoreReferringDomains'	=> 'images.google.com bloglines.com',
		'ignoreFeeds'				=> 1
	);
	var $data		= array
	(
		'visits'	=> array
		(
			/****************************************************
			 0	: Every visit ever recorded			(1 index)
			 1	: Every visit recorded by hour		(24 indexes)
			 2	: Every visit recorded by day		(7 indexes)
			 3	: Every visit recorded by week		(8 indexes)
			 4	: Every visit recorded by month		(12 indexes)
			 ****************************************************/
			array // 0
			(
				array // 0
				(
					'total'		=> 0,
					'unique'	=> 0
				)
			),
			array(),
			array(),
			array(),
			array()
		),
		'watched'	=> array()
	);
	var $manifest	= array
	(
		'visit'	=> array
		(
			'referer' 			=> "VARCHAR(255) NOT NULL",
			'referer_checksum' 	=> "INT(10) NOT NULL",
			'domain_checksum' 	=> "INT(10) NOT NULL",
			'referer_is_local' 	=> "TINYINT(1) NOT NULL DEFAULT '-1'",
			'resource' 			=> "VARCHAR(255) NOT NULL",
			'resource_checksum' => "INT(10) NOT NULL",
			'resource_title' 	=> "VARCHAR(255) NOT NULL",
			'search_terms' 		=> "VARCHAR(255) NOT NULL",
			'img_search_found'	=> "TINYINT(1) unsigned NOT NULL default '0'"
		)
	);
	var $hasCrush = false;
	
	/**************************************************************************
	 onUpdate()
	 
	 Called when Mint has been updated. Useful for importing data from versions
	 of Mint prior to 1.2.
	 **************************************************************************/
	function onUpdate()
	{
		if 
		(
			$this->Mint->cfg['version'] < 121				&& 
			!empty($this->data['watched'])						&& 
			gettype($this->data['watched'][0]) == 'string'
		)
		{
			foreach ($this->data['watched'] as $i => $resource)
			{
				$this->data['watched'][$i] = crc32($resource);
			}
		}
		
		return true;
	}
	
	/**************************************************************************
	 update()
	 **************************************************************************/
	function update()
	{
		if (!isset($this->prefs['ignoreFeeds']))
		{	
			$this->prefs['ignoreFeeds'] = 1;
		}
	}
	
	/**************************************************************************
	 isCompatible()
	 **************************************************************************/
	function isCompatible()
	{
		if ($this->Mint->version < 203)
		{
			$compatible = array
			(
				'isCompatible'	=> false,
				'explanation'	=> '<p>This Pepper requires Mint 2.03. Mint 2, a paid upgrade from Mint 1.x, is available at <a href="http://www.haveamint.com/">haveamint.com</a>.</p>'
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
	 onJavaScript()
	 **************************************************************************/
	function onJavaScript() 
	{
		$js = MINT_ROOT.'pepper/shauninman/default/script.js';
		if (file_exists($js))
		{
			include($js);
		}
	}
	
	/**************************************************************************
	 onRecord()
	 **************************************************************************/
	function onRecord() 
	{
 		if (empty($_GET)) 
 		{ 
 			return array(); 
 		}
 		
		$visits = $this->data['visits'];
		// Build an array of the start of the the current hour, day, week, & month
		$timespans = array
		(
			0,
			$this->Mint->getOffsetTime('hour'),
			$this->Mint->getOffsetTime('today'),
			$this->Mint->getOffsetTime('week'),
			$this->Mint->getOffsetTime('month')
		);
		
		// Update totals
		foreach ($timespans as $window => $begins) 
		{
			if (isset($visits[$window][$begins]['total'])) 
			{ 
				$visits[$window][$begins]['total']++;
			}
			else 
			{
				$visits[$window][$begins]['total']=1;
			}
		}
		
		// If the visitor isn't accepting cookies we can't tell if they are unique or not
		if ($this->Mint->acceptsCookies)
		{
			// The virgin visit
			if (!isset($_COOKIE['MintUnique'])) 
			{
				$this->Mint->bakeCookie("MintUnique", 1, (time()+(60 * 60 * 24 * 365 * 10)));

				if (isset($visits[0][0]['unique'])) 
				{ 
					$visits[0][0]['unique']++;
				}
				else 
				{
					$visits[0][0]['unique']=1;
				}
			}
		
			// Unique Hour
			if (!isset($_COOKIE['MintUniqueHour']) || (isset($_COOKIE['MintUniqueHour']) && $_COOKIE['MintUniqueHour'] != $timespans[1]))
			{
				$hour = $timespans[1];
				$this->Mint->bakeCookie("MintUniqueHour", $hour, ($hour + (60 * 60)));

				if (isset($visits[1][$hour]['unique'])) 
				{ 
					$visits[1][$hour]['unique']++;
				}
				else 
				{
					$visits[1][$hour]['unique']=1;
				}
			}
		
			// Unique Day
			if (!isset($_COOKIE['MintUniqueDay']) || (isset($_COOKIE['MintUniqueDay']) && $_COOKIE['MintUniqueDay'] != $timespans[2]))
			{
				$day = $timespans[2];
				$this->Mint->bakeCookie("MintUniqueDay", $day, ($day + (60 * 60 * 24)));

				if (isset($visits[2][$day]['unique'])) 
				{ 
					$visits[2][$day]['unique']++;
				}
				else 
				{
					$visits[2][$day]['unique']=1;
				}
			}
		
			// Unique Week
			if (!isset($_COOKIE['MintUniqueWeek']) || (isset($_COOKIE['MintUniqueWeek']) && $_COOKIE['MintUniqueWeek'] != $timespans[3]))
			{
				$week = $timespans[3];
				$this->Mint->bakeCookie("MintUniqueWeek", $week, ($week + (60 * 60 * 24 * 7)));

				if (isset($visits[3][$week]['unique'])) 
				{ 
					$visits[3][$week]['unique']++;
				}
				else 
				{
					$visits[3][$week]['unique']=1;
				}
			}
		
			// Unique Month
			if (!isset($_COOKIE['MintUniqueMonth']) || (isset($_COOKIE['MintUniqueMonth']) && $_COOKIE['MintUniqueMonth'] != $timespans[4]))
			{
				$month = $timespans[4];
				$this->Mint->bakeCookie("MintUniqueMonth", $month, ($month + (60 * 60 * 24 * 31)));

				if (isset($visits[4][$month]['unique'])) 
				{ 
					$visits[4][$month]['unique']++;
				}
				else 
				{
					$visits[4][$month]['unique']=1;
				}
			}
		}
		
		// Trim older visit data
		$visits[1] = $this->array_prune($visits[1], 48);
		$visits[2] = $this->array_prune($visits[2], 14);
		$visits[3] = $this->array_prune($visits[3], 10);
		$visits[4] = $this->array_prune($visits[4], 24);
		
		// Store the updated visits data
		$this->data['visits'] = $visits;
		
 		/**********************************************************************/
 		
 		$referer 			= $this->escapeSQL($this->sanitizeUrl(preg_replace('/#.*$/', '', htmlentities($_GET['referer']))));
 		$referer_is_local	= -1; // default for no referrer
 		$search				= '';
 		$resource			= $this->escapeSQL($this->sanitizeUrl(preg_replace('/#.*$/', '', htmlentities($_GET['resource']))));
 		$resource_title		= ($_GET['resource_title_encoded']) ? $_GET['resource_title'] : htmlentities($_GET['resource_title']);
 		$res_title			= $this->escapeSQL(trim(str_replace('\n', ' ', preg_replace('/%u([\d\w]{4})/', '&#x$1;', $resource_title))));
 		$image_search		= 0;
		$domain				= preg_replace('/(^([^:]+):\/\/(www\.)?|(:\d+)?\/.*$)/', '', $referer);
 		
 		if (!empty($referer)) 
 		{
 			$referer_is_local	= (preg_match("/^([^:]+):\/\/([a-z0-9]+[\._-])*(".str_replace('.', '\.', implode('|', $this->Mint->domains)).")/i", $referer))?1:0;
			if (!$referer_is_local)
			{
				include(MINT_ROOT.'pepper/shauninman/default/engines.php');
				$search_sites = array();
				$search_query = array();
				$image_sites = array();
				$image_paths = array();
				$image_results = array();

				foreach ($SI_SearchEngines as $engine) 
				{
					$search_sites[] = preg_quote($engine['domain']);
					if (!empty($engine['query']) && !in_array($engine['query'], $search_query))
					{
						$search_query[] = $engine['query'];
					}

					if (isset($engine['images']))
					{
						$image_results[$engine['domain']] = $engine['image_results'];

						// if 'image' starts with a slash then image searches are differentiated by the file or directory
						if ($engine['images']{0} == '/')
						{
							$image_paths[] = preg_quote($engine['images']);
						}
						// otherwise we'll assume the subdomain changes
						else
						{
							$image_sites[] = preg_quote($engine['images']);
						}
					}
				}

				if (preg_match('!('.join('|', $search_sites).')\.[^/]*/(?:(?:[^\?&]*(?:\?|&))+(?:'.join('|', $search_query).')=)([^&]*)!i', html_entity_decode($referer), $q))
				{
					if (!empty($q[2])) // found a search term
					{
						$search = $this->escapeSQL(stripslashes(urldecode(preg_replace('/%u([\d\w]{4})/', '&#x$1;', htmlentities($q[2])))));

						// check to see if this was an image search
						if (preg_match('!^https?://(('.join('|', $image_sites).')|[^/]+('.join('|', $image_paths).'))!i', $referer))
						{
							$image_path = $image_results[$q[1]];
							$image_search = 1;
						}

					}
				}
				// Google Images slips through entirely, because its q var is encoded in the prev var (propbably used in the "Image Results" link)
				else if (strpos($referer, 'google.') !== false && preg_match('!prev=([^&]+)!i', html_entity_decode($referer), $p))
				{
					if (!empty($p[1]) && preg_match('!q=([^&]+)!i', urldecode($p[1]), $q))
					{
						if (!empty($q[1])) // found a search term
						{
							$search = $this->escapeSQL(stripslashes(urldecode(preg_replace('/%u([\d\w]{4})/', '&#x$1;', htmlentities($q[1])))));
							$image_path = $image_results['google'];
							$image_search = 1;
						}
					}
				}
				// The url for image details are usually too long for the referrer column (400+ characters)
				// so generate a link to the results page from the referring search engine
				if ($image_search)
				{
					$referer = 'http://'.$domain.$image_path.urlencode($search);
				}
			}
		}
 		
		// search.com requires that the www. be trimmed after search matching
		if ($this->prefs['trimPrefixIndex']) 
 		{
 			$referer = $this->trimPrefixIndex($referer);
 			$resource = $this->trimPrefixIndex($resource);
 		}

		// generate checksums for speedy indexes and queries
		$referer_checksum	= crc32($referer);
 		$domain_checksum	= crc32($domain);
 		$resource_checksum	= crc32($resource);

 		return array
 		(
 			'referer'			=> $referer,
			'referer_checksum'	=> $referer_checksum,
			'domain_checksum'	=> $domain_checksum,
			'referer_is_local'	=> $referer_is_local,
			'resource'			=> $resource,
			'resource_checksum'	=> $resource_checksum,
			'resource_title'	=> $res_title,
			'search_terms'		=> $search,
			'img_search_found'	=> $image_search
		);
	}
	
	/**************************************************************************
	 onDisplay()
	 **************************************************************************/
	function onDisplay($pane, $tab, $column = '', $sort = '')
	{
		$html = '';
		
		// determine if Secret Crush is installed
		$this->hasCrush	= isset($this->Mint->cfg['pepperLookUp']['SI_SecretCrush']);
		
		switch($pane) 
		{
		/* Referrers **********************************************************/
			case 'Referrers': 
				switch($tab) 
				{
				/* Newest Unique **********************************************/
					case 'Newest Unique':
						$html .= $this->getHTML_ReferrersUnique();
					break;
				/* Most Recent ************************************************/
					case 'Most Recent':
						$html .= $this->getHTML_ReferrersRecent();
					break;
				/* Repeat *****************************************************/
					case 'Repeat':
						$html .= $this->getHTML_ReferrersRepeat();
					break;
				/* Domains **************************************************/
					case 'Domains':
						$html .= $this->getHTML_ReferrersDomains();
					break;
				}
			break;
			
		/* Pages **************************************************************/
			case 'Pages': 
				switch($tab) 
				{
				/* Most Popular ***********************************************/
					case 'Most Popular':
						$html .= $this->getHTML_PagesPopular();
					break;
				/* Most Recent ************************************************/
					case 'Most Recent':
						$html .= $this->getHTML_PagesRecent();
					break;
				/* Entry ******************************************************/
					case 'Entry':
						$html .= $this->getHTML_PagesEntry();
					break;
				/* Watched ****************************************************/
					case 'Watched':
						$html .= $this->getHTML_PagesWatched();
					break;
				}
				break;
		/* Searches ***********************************************************/
			case 'Searches': 
				switch($tab)
				{
				/* Most Common ************************************************/
					case 'Most Common':
						$html .= $this->getHTML_SearchesCommon();
					break;
				/* Most Recent ************************************************/
					case 'Most Recent':
						$html .= $this->getHTML_SearchesRecent();
					break;
				/* Found ******************************************************/
					case 'Found':
						$html .= $this->getHTML_SearchesFound();
					break;
				}
			break;
		/* Visits *************************************************************/
			case 'Visits': 
				switch($tab) 
				{
					case 'Overview':
						$html .= $this->getHTML_Visits();
					break;
					
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
		 onDisplaySupplemental() 
		 **************************************************************************/
		function onDisplaySupplemental($pane) 
		{
			$html = '';
			if ($pane == 'Pages')
			{ 
				$html .= <<<HERE
<style type="text/css" title="text/css" media="screen">
/* <![CDATA[ */
td.watched a.watch { color: #666; font-size: 1.1em; }
td.watched a.unwatch { color: #AB6666; }
/* ]]> */
</style>
<script type="text/javascript" language="javascript">
// <![CDATA[
function SI_manageWatched(e,resource,remove)
{
	if (remove)
	{
		// Remove from display and reorder
		var table	= e.parentNode.parentNode.parentNode.parentNode;
		var tbody	= e.parentNode.parentNode.parentNode;
		var content	= tbody.nextSibling;
		table.removeChild(tbody);
		
		SI.CSS.relate(content);
		
		table.removeChild(content);
		var action = 'unwatch';
		if(window.event && window.event.stopPropagation)
		{
			window.event.stopPropagation();
		}
	}
	else 
	{
		var action = e.href.replace(/^[^#]*#(.*)$/,'$1');
		if (action=='watch') 
		{
			e.href 		= '#unwatch';
			e.innerHTML = '&times;';
			e.title 	= 'Unwatch this page';
			e.className	= 'unwatch';
		}
		else 
		{
			e.href 		= '#watch';
			e.innerHTML	= '+';
			e.title 	= 'Watch this page';
			e.className	= 'watch';
		};
	};

	// Send request
	var url = '{$this->Mint->cfg['installDir']}/?MintPath=Custom&action='+action+'&pane=pages&resource='+escape(resource);
	SI.Request.post(url); //, document.getElementById('donotremove'));
};

// ]]>
</script>
HERE;
			}

			return $html;
		}
	
	/**************************************************************************
	 onDisplayPreferences()
	 **************************************************************************/
	function onDisplayPreferences()
	{
		
		/* Global *************************************************************/
		$checked = ($this->prefs['trimPrefixIndex'])?' checked="checked"':'';
		$preferences['Global']	= <<<HERE
<table>
	<tr>
		<td><label><input type="checkbox" name="trimPrefixIndex" value="1"$checked /> Trim <code>www</code> and <code>index.*</code> from urls (This will logically collapse two different urls that point to the same file. May break some urls.)</label></td>
	</tr>
</table>

HERE;
		
		/* Referrers **********************************************************/
		$ignoredDomains = preg_replace('/[\s,]+/', "\r\n", $this->prefs['ignoreReferringDomains']);
		
		$preferences['Referrers']	= <<<HERE
<table>
	<tr>
		<td><label>Don't show referrals from the following domains in the Newest Unique Referrers and Watched Pages tabs and RSS feed (Search engines recognized by the Searches pane are ignored automatically):</label></td>
	</tr>
	<tr>
		<td><span><textarea id="ignoreReferringDomains" name="ignoreReferringDomains" rows="6" cols="30">{$ignoredDomains}</textarea></span></td>
	</tr>
</table>

HERE;
	
		/* Pages **************************************************************/
		$watch					= $this->getHTML_watchFaveletLink('watch');
		$unwatch				= $this->getHTML_watchFaveletLink('unwatch');
		$ignore_feeds_checked	= ($this->prefs['ignoreFeeds'])?' checked="checked"':'';

		// Requires Bird Feeder Pepper to distinguish
		if (isset($this->Mint->cfg['manifest']['visit']['referred_by_feed']))
		{
			$preferences['Entry Pages'] =  <<<HERE
<table>
	<tr>
		<td><label><input type="checkbox" name="ignoreFeeds" value="1"{$ignore_feeds_checked} /> Ignore referrals from Bird Feeder tracked feeds</label></td>
	</tr>
</table>
HERE;
		}

		$preferences['Watched Pages'] =  <<<HERE
<table>
	<tr>
		<td>
			<p>Drag the favelets below onto your browser's bookmark bar. Next time you view a page on <a href="http://{$this->Mint->cfg['installDomain']}/">{$this->Mint->cfg['siteDisplay']}</a>, use the favelets to add or remove the current page from the Watched tab of the Pages pane.</p>
						
			<p>Watched Favelets: &nbsp; 
			{$watch} &nbsp; 
			{$unwatch}</p>
	
		</td>
	</tr>
</table>

HERE;
		
		return $preferences;
	}

	/**************************************************************************
	 manageWatchedPages()

	 Broken out of the onCustom handler to allow for both POST and GET requests
	 **************************************************************************/
	function manageWatchedPages($action = '', $resource = '')
	{
		// Ignore if the un/watched page isn't local
		$localDomains = str_replace('.', '\.', implode('|', $this->Mint->domains));
		if (!empty($resource) && !empty($action) && preg_match("/^https?:\/\/([a-z0-9]+[\._-])*($localDomains)/i", $resource)) 
		{
			// Get existing Watched Pages
			$watched = $this->data['watched'];

			if ($this->prefs['trimPrefixIndex'])
			{
				$resource = $this->trimPrefixIndex($resource);
			}

			$resource = crc32(preg_replace('/#.*$/', '', htmlentities($resource)));

			if ($action == 'watch')
			{
				if (!in_array($resource,$watched))
				{
					$watched[] = $resource;
				}
			}
			else if ($action == 'unwatch')
			{
				$i = array_search($resource, $watched);
				if ($i!==false)
				{
					unset($watched[$i]);
					$watched = $this->Mint->array_reindex($watched);
				}
			}

			// Save updated Watched Pages
			$this->data['watched'] = $watched;
		}
	}

	/**************************************************************************
	 getHTML_watchFavelet()
	 **************************************************************************/
	function getHTML_watchFaveletLink($action)
	{
		$favelet = <<<JS
var r	= escape(window.location);
var p	= '{$this->Mint->cfg['installFull']}/';
var e	= document.createElement('script');
e.type	= 'text/javascript';
e.src	= p + '?custom&pane=pages&action={$action}&resource=' + r + '&' + (new Date).getTime();
document.getElementsByTagName('head')[0].appendChild(e);
JS;
		
		$favelet = preg_replace('/\s+/', '', $favelet); // strip whitespace
		$favelet = preg_replace('/(var|else|new)/', '\1%20', $favelet); // restore spaces after var, else, new
		$display = ucfirst($action);
			
		return "<a href=\"javascript:(function(){{$favelet}})();\" onclick=\"alert('Drag the Mint {$display} favelet to your browser\'s bookmarks bar.'); return false;\">{$display}</a>";
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{	
		// If the offset is changing then we need to update the visits array
		if (isset($_POST['offset']) && $_POST['offset'] != $this->Mint->cfg['offset'])
		{
			$offset_difference = ($_POST['offset'] - $this->Mint->cfg['offset']) * 60 * 60;
			
			/****************************************************
			 Visits
			 2	: Every visit recorded by day		(7 indexes)
			 3	: Every visit recorded by week		(8 indexes)
			 4	: Every visit recorded by month		(12 indexes)
			 ****************************************************/
			// Get stored visits data
			$visits = $this->data['visits'];
			
			// Update day indexes
			if (isset($visits[2]))
			{
				$days = $visits[2];
				foreach ($days as $date=>$hits)
				{
					$days[($date - $offset_difference)] = $hits;
					unset($days[$date]);
				}
				$visits[2] = $days;
			}
			
			// Update week indexes
			if (isset($visits[3]))
			{
				$weeks = $visits[3];
				foreach ($weeks as $date=>$hits)
				{
					$weeks[($date - $offset_difference)] = $hits;
					unset($weeks[$date]);
				}
				$visits[3] = $weeks;
			}
			
			// Update month indexes
			if (isset($visits[4]))
			{
				$months = $visits[4];
				foreach ($months as $date=>$hits)
				{
					$months[($date - $offset_difference)] = $hits;
					unset($months[$date]);
				}
				$visits[4] = $months;
			}
			
			// Store the updated visits data
			$this->data['visits'] = $visits;
			/***********************************************************************/
		}
		
		$this->prefs['condensedVisits']			= (isset($_POST['condensedVisits']))?$_POST['condensedVisits']:0;
		$this->prefs['trimPrefixIndex']			= (isset($_POST['trimPrefixIndex']))?$_POST['trimPrefixIndex']:0;
		$this->prefs['referrerTimespan']		= (isset($_POST['referrerTimespan']))?$_POST['referrerTimespan']:24;
		$this->prefs['ignoreReferringDomains']	= $this->escapeSQL(preg_replace('/[\s,]+/', ' ', $_POST['ignoreReferringDomains']));
		$this->prefs['ignoreFeeds']				= (isset($_POST['ignoreFeeds']))?1:0;
	}
	
	/**************************************************************************
	 onCustom()
	 **************************************************************************/
	function onCustom() 
	{
		/* Watch/Unwatch Pages (Mint) ----------------------------------------*/
		if
		(
			isset($_POST['action']) && 
			($_POST['action']=='watch' || $_POST['action']=='unwatch') && 
			(isset($_POST['pane']) && $_POST['pane']=='pages') && 
			isset($_POST['resource'])
		) 
		{
			$this->manageWatchedPages($_POST['action'], $_POST['resource']);
		}
		
		/* Watch/Unwatch Pages (Favelet) -------------------------------------*/
		else if
		(
			isset($_GET['action']) && 
			($_GET['action']=='watch' || $_GET['action']=='unwatch') && 
			(isset($_GET['pane']) && $_GET['pane']=='pages') && 
			isset($_GET['resource'])
		) 
		{
			$this->manageWatchedPages($_GET['action'], $_GET['resource']);
			header('Content-type:text/javascript');
			echo "/* {$_GET['action']}ed */";
		}
		
		/* WATCHED PAGE REFERRERS --------------------------------------------*/
		else if 
		(
			isset($_POST['action']) 		&& 
			$_POST['action']=='getreferrers'	&& 
			isset($_POST['checksum'])
		)
		{
			$checksum	= $this->escapeSQL($_POST['checksum']);
			echo $this->getHTML_PagesWatchedReferrers($checksum).' ';
		}
		
		/* REFERRERS BY DOMAIN -----------------------------------------------*/
		else if
		(
			isset($_POST['action']) 		&& 
			$_POST['action']=='getReferrersByDomain'	&& 
			isset($_POST['domain_checksum'])
		)
		{
			$domain_checksum = $this->escapeSQL($_POST['domain_checksum']);
			echo $this->getHTML_ReferrersByDomain($domain_checksum);
		}
		
		/* KEYWORDS BY PAGE -----------------------------------------------*/
		else if
		(
			isset($_POST['action']) 		&& 
			$_POST['action']=='getKeywordsByPage'	&& 
			isset($_POST['resource_checksum'])
		)
		{
			$resource_checksum = $this->escapeSQL($_POST['resource_checksum']);
			echo $this->getHTML_KeywordsByPage($resource_checksum);
		}
	}
	
	/**************************************************************************
	 onRss()
	 **************************************************************************/
	function onRss()
	{
		$rssData = array();
		$rssData['title'] = 'Referrers';
		
		// Ignore certain domains
		$ignoredDomains	= preg_split('/[\s,]+/', $this->prefs['ignoreReferringDomains']);
		$ignoreQuery 	= '';
		if (!empty($ignoredDomains))
		{
			foreach ($ignoredDomains as $domain)
			{
				if (empty($domain))
				{
					continue;
				}
				$ignoreQuery .= ' AND `domain_checksum` != '.crc32($domain);
			}
		}
		
		$query = "SELECT `referer`, `resource`, `resource_title`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `referer_is_local` = 0 AND `search_terms` = '' $ignoreQuery
					GROUP BY `referer_checksum` 
					ORDER BY `dt` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rssRows']}";
		
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$referrer_cleaned	= str_replace('&', '&amp;', $r['referer']);
				$resource_cleaned	= str_replace('&', '&amp;', $r['resource']);
				$res_title			= (!empty($r['resource_title'])) ? stripslashes($r['resource_title']) : $resource_cleaned;
				
				preg_match('/^[^:]+:\/\/(?:www\.)?([^\/]+)/', $r['referer'], $d);
				$domain = $d[1];
				
				$body = <<<HERE

				<table>
					<tr>
						<th scope="row" align="right">From</th>
						<td><a href="{$referrer_cleaned}">{$referrer_cleaned}</a></td>
					</tr>
					<tr>
						<th scope="row" align="right">To</th>
						<td><a href="{$resource_cleaned}">$res_title</a></td>
					</tr>
				</table>

HERE;
				
				$rssData['items'][] = array
				(
					'title' => $domain,
					'body'	=> $body,
					'link'	=> $r['referer'],
					'date'	=> $r['dt']
				);
			}
		}
		
		return $rssData;
	}
	
	/**************************************************************************
	 onWidget()
	 
	 **************************************************************************/
	function onWidget() 
	{
		$visits = $this->data['visits'];
		
		// Ever
		$everArr = array
		(
			@number_format($visits[0][0]['total']),
			@number_format($visits[0][0]['unique']),
			$this->Mint->formatDateRelative($this->Mint->cfg['installDate'], 'month', 1)
		);
		
		// Today
		$day = $this->Mint->getOffsetTime('today');
		if (isset($visits[2][$day]))
		{
			$d = $visits[2][$day];
		}
		else
		{
			$d = array('total'=>'0','unique'=>'0');
		}
		$todayArr = array
		(
			@number_format($d['total']),
			@number_format($d['unique'])
		);
		
		
		// This hour
		$hour = $this->Mint->getOffsetTime('hour');
		if (isset($visits[1][$hour]))
		{
			$h = $visits[1][$hour];
		}
		else
		{
			$h = array('total'=>'0','unique'=>'0');
		}
		$hourArr = array
		(
			@number_format($h['total']),
			@number_format($h['unique']),
			$this->Mint->formatDateRelative(0, 'hour')
		);
		
		$everArr[]	= ($everArr[0] > 1) ? 's' : '';
		$todayArr[]	= ($todayArr[0] > 1) ? 's' : '';
		$hourArr[]	= ($hourArr[0] > 1) ? 's' : '';
		
		$visitsHTML = <<<HERE
			<ul id="visits-list">
				<li id="visits-ever">
					<div class="total">{$everArr[0]}</div>
					<div class="etc">
						<span>Visit{$everArr[3]} since {$everArr[2]}</span><br />
						<div class="unique">{$everArr[1]}</div> <em>Unique</em>
					</div>
				</li>
				<li id="visits-today">
					<div class="total">{$todayArr[0]}</div>
					<div class="etc">
						<span>Visit{$todayArr[2]} today</span><br />
						<div class="unique">{$todayArr[1]}</div> <em>Unique</em>
					</div>
				</li>
				<li id="visits-hour">
					<div class="total">{$hourArr[0]}</div>
					<div class="etc">
						<span>Visit{$hourArr[3]} since {$hourArr[2]}</span><br />
						<div class="unique">{$hourArr[1]}</div> <em>Unique</em>
					</div>
				</li>
			</ul>
HERE;

		return $visitsHTML;
	}
		
	/**************************************************************************
	 getHTML_Visits()
	 **************************************************************************/
	function getHTML_Visits() 
	{
		$visits	= $this->data['visits'];
		
		/* Since **************************************************************/
		$tableData['table'] = array('id'=>'','class'=>'inline-foot striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Since','class'=>'focus'),
			array('value'=>'<abbr title="Total Page Views">Total</abbr>','class'=>''),
			array('value'=>'<abbr title="Total Unique Visitors">Unique</abbr>','class'=>'')
		);
		
		$tableData['tbody'][] = array
		(
			$this->Mint->formatDateRelative($this->Mint->cfg['installDate'], 'month', 1),
			$visits[0][0]['total'],
			$visits[0][0]['unique']
		);
		$sinceHTML = $this->Mint->generateTable($tableData);
		unset($tableData);
		
		
		/* Past Day ***********************************************************/
		$tableData['table'] = array('id'=>'','class'=>'inline day striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Past Day','class'=>'focus'),
			array('value'=>'<abbr title="Total Page Views">Total</abbr>','class'=>''),
			array('value'=>'<abbr title="Total Unique Visitors">Unique</abbr>','class'=>'')
		);
		$hour = $this->Mint->getOffsetTime('hour');
		// Past 24 hours
		for ($i=0; $i<24; $i++) 
		{
			$j = $hour - ($i*60*60);
			if (isset($visits[1][$j]))
			{
				$h = $visits[1][$j];
			}
			else
			{
				$h = array('total'=>'-','unique'=>'-');
			}
			$tableData['tbody'][] = array
			(
				$this->Mint->formatDateRelative($j,"hour"),
				((isset($h['total']))?$h['total']:'-'),
				((isset($h['unique']))?$h['unique']:'-')
			);
		}
		$dayHTML = $this->Mint->generateTable($tableData);
		unset($tableData);
		

		/* Past Week **********************************************************/
		$tableData['table'] = array('id'=>'','class'=>'inline-foot striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Past Week','class'=>'focus'),
			array('value'=>'<abbr title="Total Page Views">Total</abbr>','class'=>''),
			array('value'=>'<abbr title="Total Unique Visitors">Unique</abbr>','class'=>'')
		);
		$day = $this->Mint->getOffsetTime('today');
		// Past 7 days
		for ($i=0; $i<7; $i++) 
		{
			$j = $day - ($i*60*60*24);
			if (isset($visits[2][$j]))
			{
				$d = $visits[2][$j];
			}
			else
			{
				$d = array('total'=>'-','unique'=>'-');
			}
			$tableData['tbody'][] = array
			(
				$this->Mint->formatDateRelative($j,"day"),
				((isset($d['total']))?$d['total']:'-'),
				((isset($d['unique']))?$d['unique']:'-')
			);
		}
		$weekHTML = $this->Mint->generateTable($tableData);
		unset($tableData);
		
		
		/* Past Month *********************************************************/
		$tableData['table'] = array('id'=>'','class'=>'inline inline-foot striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Past Month','class'=>'focus'),
			array('value'=>'<abbr title="Total Page Views">Total</abbr>','class'=>''),
			array('value'=>'<abbr title="Total Unique Visitors">Unique</abbr>','class'=>'')
		);
		$week = $this->Mint->getOffsetTime('week');
		// Past 5 weeks
		for ($i=0; $i<5; $i++)
		{
			$j = $week - ($i*60*60*24*7);
			if (isset($visits[3][$j]))
			{
				$w = $visits[3][$j];
			}
			else
			{
				$w = array('total'=>'-','unique'=>'-');
			}
			$tableData['tbody'][] = array
			(
				$this->Mint->formatDateRelative($j,"week",$i),
				((isset($w['total']))?$w['total']:'-'),
				((isset($w['unique']))?$w['unique']:'-')
			);
		}
		$monthHTML = $this->Mint->generateTable($tableData);
		unset($tableData);
		
		
		/* Past Year **********************************************************/
		$tableData['table'] = array('id'=>'','class'=>'inline year striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Past Year','class'=>'focus'),
			array('value'=>'<abbr title="Total Page Views">Total</abbr>','class'=>''),
			array('value'=>'<abbr title="Total Unique Visitors">Unique</abbr>','class'=>'')
		);
		$month = $this->Mint->getOffsetTime('month');
		// Past 12 months
		for ($i=0; $i<12; $i++)
		{
			if ($i==0)
			{
				$j=$month;
			}
			else
			{
				$days 		= $this->Mint->offsetDate('t', $this->Mint->offsetMakeGMT(0, 0, 0, $this->Mint->offsetDate('n', $month)-1, 1, $this->Mint->offsetDate('Y', $month))); // days in the month
				$j 			= $month - ($days*24*3600);
			}
			
			$month = $j;
			if (isset($visits[4][$j]))
			{
				$m = $visits[4][$j];
			}
			else
			{
				$m = array('total'=>'-','unique'=>'-');
			}
			
			$tableData['tbody'][] = array
			(
				$this->Mint->formatDateRelative($j, 'month', $i),
				((isset($m['total']))?$m['total']:'-'),
				((isset($m['unique']))?$m['unique']:'-')
			);
		}
		$yearHTML = $this->Mint->generateTable($tableData);
		unset($tableData);
		
		/**/
		$html  = '<table cellspacing="0" class="visits">';
		$html .= "\r\t<tr>\r";
		$html .= "\t\t<td class=\"left\">\r";
		$html .= $sinceHTML.$dayHTML;
		$html .= "\t\t</td>";
		$html .= "\t\t<td class=\"right\">\r";
		$html .= $weekHTML.$monthHTML.$yearHTML;
		$html .= "\t\t</td>";
		$html .= "\r\t</tr>\r";
		$html .= "</table>\r";
		return $html;
	}
	
	/**************************************************************************
	 getHTML_VisitsDay()
	 **************************************************************************/
	function getHTML_VisitsDay() 
	{
		$graphData	= array
		(
			'titles' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			),
			'key' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			)
		);
		$high 		= 0;
		$hours		= $this->data['visits'][1];
		$hour 		= $this->Mint->getOffsetTime('hour');

		// Past 24 hours
		for ($i = 0; $i < 24; $i++) 
		{
			$timestamp = $hour - ($i * 60 * 60);
			$counts = array(0, 0);
			if (isset($hours[$timestamp]))
			{
				$counts = array($hours[$timestamp]['total'], $hours[$timestamp]['unique']);
			}
			
			$high = ($counts[0] > $high) ? $counts[0] : $high;
			$twelve = $this->Mint->offsetDate('G', $timestamp) == 12;
			$twentyFour = $this->Mint->offsetDate('G', $timestamp) == 0;
			$hourLabel = $this->Mint->offsetDate('g', $timestamp);
			
			$graphData['bars'][] = array
			(
				$counts[0],
				$counts[1],
				($twelve) ? 'Noon' : (($twentyFour) ? 'Midnight' : (($hourLabel == 3 || $hourLabel == 6 || $hourLabel == 9) ? $hourLabel : '')),
				$this->Mint->formatDateRelative($timestamp, "hour"),
				($twelve || $twentyFour) ? 1 : 0
			);
		}
		
		$graphData['bars'] = array_reverse($graphData['bars']);
		$html = $this->getHTML_Graph($high, $graphData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_VisitsWeek()
	 **************************************************************************/
	function getHTML_VisitsWeek() 
	{
		$graphData	= array
		(
			'titles' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			),
			'key' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			)
		);
		$high 		= 0;
		$days		= $this->data['visits'][2];
		$day		= $this->Mint->getOffsetTime('today');
				
		// Past 7 days
		for ($i = 0; $i < 7; $i++) 
		{
			$timestamp = $day - ($i * 60 * 60 * 24);
			$counts = array(0, 0);
			if (isset($days[$timestamp]))
			{
				$counts = array($days[$timestamp]['total'], $days[$timestamp]['unique']);
			}
			
			$high = ($counts[0] > $high) ? $counts[0] : $high;
			$dayOfWeek = $this->Mint->offsetDate('w', $timestamp);
			$dayLabel = substr($this->Mint->offsetDate('D', $timestamp), 0, 2);
			
			$graphData['bars'][] = array
			(
				$counts[0],
				$counts[1],
				($dayOfWeek == 0) ? '' : (($dayOfWeek == 6) ? 'Weekend' : $dayLabel),
				$this->Mint->formatDateRelative($timestamp, "day"),
				($dayOfWeek == 0 || $dayOfWeek == 6) ? 1 : 0
			);
		}
		
		$graphData['bars'] = array_reverse($graphData['bars']);
		$html = $this->getHTML_Graph($high, $graphData);
		return $html;
	}

	/**************************************************************************
	 getHTML_VisitsMonth()
	 **************************************************************************/
	function getHTML_VisitsMonth() 
	{
		$graphData	= array
		(
			'titles' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			),
			'key' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			)
		);
		$high 		= 0;
		$weeks		= $this->data['visits'][3];
		$week 		= $this->Mint->getOffsetTime('week');
		
		// Past 5 weeks
		for ($i = 0; $i < 5; $i++)
		{
			$timestamp = $week - ($i * 60 * 60 * 24 * 7);
			$counts = array(0, 0);
			if (isset($weeks[$timestamp]))
			{
				$counts = array($weeks[$timestamp]['total'], $weeks[$timestamp]['unique']);
			}
			
			$high = ($counts[0] > $high) ? $counts[0] : $high;
			
			$graphData['bars'][] = array
			(
				$counts[0],
				$counts[1],
				$this->Mint->formatDateRelative($timestamp, "week", $i),
				$this->Mint->offsetDate('D, M j', $timestamp),
				($i == 0) ? 1 : 0
			);
		}
		
		$graphData['bars'] = array_reverse($graphData['bars']);
		$html = $this->getHTML_Graph($high, $graphData);
		return $html;
	}

	/**************************************************************************
	 getHTML_VisitsYear()
	 **************************************************************************/
	function getHTML_VisitsYear() 
	{
		$graphData	= array
		(
			'titles' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			),
			'key' => array
			(
				'background' => 'Total',
				'foreground' => 'Unique'
			)
		);
		$high 		= 0;
		$months		= $this->data['visits'][4];
		$month 		= $this->Mint->getOffsetTime('month');
		
		// Past 12 months
		for ($i = 0; $i < 12; $i++)
		{
			if ($i == 0)
			{
				$timestamp = $month;
			}
			else
			{
				$days 		= $this->Mint->offsetDate('t', $this->Mint->offsetMakeGMT(0, 0, 0, $this->Mint->offsetDate('n', $month)-1, 1, $this->Mint->offsetDate('Y', $month))); // days in the month
				$timestamp 	= $month - ($days * 24 * 3600);
			}
			$month = $timestamp;
			
			$counts = array(0, 0);
			if (isset($months[$timestamp]))
			{
				$counts = array($months[$timestamp]['total'], $months[$timestamp]['unique']);
			}
			
			$high = ($counts[0] > $high) ? $counts[0] : $high;
			
			$graphData['bars'][] = array
			(
				$counts[0],
				$counts[1],
				($i == 0) ? 'This Month' : $this->Mint->offsetDate(' M', $timestamp),
				$this->Mint->offsetDate('F', $timestamp),
				($i == 0) ? 1 : 0
			);
		}
		
		$graphData['bars'] = array_reverse($graphData['bars']);
		$html = $this->getHTML_Graph($high, $graphData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_ReferrersRecent()
	 **************************************************************************/
	function getHTML_ReferrersRecent() 
	{
		$html = '';
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'From','class'=>'focus'),
			array('value'=>'When','class'=>'sort')
		);
		
		$additional_columns = '';
		if ($this->hasCrush)
		{
			// add an empty table head to accomodate the new search column
			array_unshift($tableData['thead'], array('value'=>'&nbsp;','class'=>'search'));
			$additional_columns = ', `ip_long`';
			$SecretCrush =& $this->Mint->getPepperByClassName('SI_SecretCrush');
		}
		
		// Referrers Pane
		$query = "SELECT `referer`, `resource`, `resource_title`, `search_terms`, `img_search_found`, `dt`{$additional_columns}
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `referer_is_local` = 0 
					ORDER BY `dt` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$dt = $this->Mint->formatDateTimeRelative($r['dt']);
				if (!empty($r['search_terms']))
				{
					$class		= ($r['img_search_found']) ? ' class="image-search"' : '';
					$ref_title 	= $this->Mint->abbr(stripslashes($r['search_terms']));
					$ref_html	= "Search for <a href=\"{$r['referer']}\" rel=\"nofollow\" {$class}>$ref_title</a>";
				}
				else
				{	
					$ref_title = $this->Mint->abbr($r['referer']);
					$ref_html = "<a href=\"{$r['referer']}\" rel=\"nofollow\">$ref_title</a>";
				}
				$res_title = $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				$res_html = "To <a href=\"{$r['resource']}\">$res_title</a>";
				if (!empty($ref_title) && $this->Mint->cfg['preferences']['secondary'])
				{
					$ref_html .= "<br /><span>{$res_html}</span>";
				}
				
				$tableRow = array
				(
					$ref_html,
					$dt
				);
				
				if ($this->hasCrush)
				{
					$ip = long2ip($r['ip_long']);
					array_unshift($tableRow, $SecretCrush->generateSearchIcon($ip, true));
				}
				
				$tableData['tbody'][] = $tableRow;
			}
		}
			
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}


	/**************************************************************************
	 getHTML_ReferrersUnique()
	 **************************************************************************/
	function getHTML_ReferrersUnique() 
	{
		$html = '';
		
		$tableData['table'] = array('id'=>'','class'=>'has-feed');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'From','class'=>'focus'),
			array('value'=>'When','class'=>'sort')
		);
		
		// Ignore certain domains
		$ignoredDomains	= preg_split('/[\s,]+/', $this->prefs['ignoreReferringDomains']);
		$ignoreQuery 	= '';
		if (!empty($ignoredDomains))
		{
			foreach ($ignoredDomains as $domain)
			{
				if (empty($domain))
				{
					continue;
				}
				$ignoreQuery .= ' AND `domain_checksum` != '.crc32($domain);
			}
		}
		
		$query = "SELECT `referer`, `resource`, `resource_title`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `referer_is_local` = 0 AND `search_terms` = '' {$ignoreQuery}
					GROUP BY `referer_checksum` 
					ORDER BY `dt` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$dt = $this->Mint->formatDateTimeRelative($r['dt']);
				$ref_title = $this->Mint->abbr($r['referer']);
				$res_title = $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				$tableData['tbody'][] = array
				(
					"<a href=\"{$r['referer']}\" rel=\"nofollow\">$ref_title</a>".(($this->Mint->cfg['preferences']['secondary'])?"<br /><span>To <a href=\"{$r['resource']}\">$res_title</a></span>":''),
					$dt
				);
			}
		}
			
		$html  = $this->Mint->generateTable($tableData);
		$html .= $this->Mint->generateRSSLink($this->pepperId, 'Newest Unique Referrers');
		return $html;
	}

	/**************************************************************************
	 getHTML_ReferrersRepeat()
	 **************************************************************************/
	function getHTML_ReferrersRepeat() 
	{
		$html = '';
		
		$filters = array
		(
			'Show all'	=> 0,
			'Past hour'	=> 1,
			'2h'		=> 2,
			'4h'		=> 4,
			'8h'		=> 8,
			'24h'		=> 24,
			'48h'		=> 48,
			'72h'		=> 72
		);
		
		$html .= $this->generateFilterList('Repeat', $filters);
		
		$timespan = ($this->filter) ? " AND dt > ".(time() - ($this->filter * 60 * 60)) : '';
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Hits','class'=>'sort'),
			array('value'=>'From','class'=>'focus')
		);
		
		$query = "SELECT `referer`, `resource`, `resource_title`, COUNT(`referer`) as `total`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `referer_is_local` = 0 AND `search_terms` = '' $timespan
					GROUP BY `referer_checksum` 
					ORDER BY `total` DESC, `dt` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$ref_title = $this->Mint->abbr($r['referer']);
				$res_title = $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				$tableData['tbody'][] = array
				(
					$r['total'],
					"<a href=\"{$r['referer']}\" rel=\"nofollow\">$ref_title</a>".(($this->Mint->cfg['preferences']['secondary'])?"<br /><span>To <a href=\"{$r['resource']}\">$res_title</a></span>":'')
				);
			}
		}
		
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_ReferrersDomains()
	 **************************************************************************/
	function getHTML_ReferrersDomains()
	{
		$html = '';
		
		$tableData['hasFolders'] = true;
		$tableData['table'] = array('id'=>'','class'=>'folder');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Sources','class'=>'sort'),
			array('value'=>'Domain/Referrers','class'=>'focus'),
			array('value'=>'Hits','class'=>'sort')
		);
		
		// Ignore certain domains
		$ignoredDomains	= preg_split('/[\s,]+/', $this->prefs['ignoreReferringDomains']);
		$ignoreQuery 	= '';
		if (!empty($ignoredDomains))
		{
			foreach ($ignoredDomains as $domain)
			{
				if (empty($domain))
				{
					continue;
				}
				$ignoreQuery .= ' AND `domain_checksum` != '.crc32($domain);
			}
		}
		
		$query = "SELECT `referer`, `domain_checksum`, COUNT(DISTINCT `referer`) as `sources`, COUNT(`referer`) as `hits`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `search_terms` = '' AND `referer_is_local` = 0 {$ignoreQuery}
					GROUP BY `domain_checksum` 
					ORDER BY `sources` DESC, `hits` DESC , `dt` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$domain = preg_replace('!(^https?://[^/]+).*!', '\1', $r['referer']);
				$domain = $this->Mint->abbr($domain);
				$tableData['tbody'][] = array
				(
					$r['sources'],
					$domain,
					$r['hits'],

					'folderargs' => array
					(
						'action'	=>'getReferrersByDomain',
						'domain_checksum'	=>$r['domain_checksum']
					)
				);
			}
		}
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_ReferrersByDomain()
	 **************************************************************************/
	function getHTML_ReferrersByDomain($domain_checksum)
	{
		$html = '';
		
		$query = "SELECT `referer`, `resource`, `resource_title`, COUNT(`referer`) as `total`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `search_terms` = '' AND `domain_checksum` = {$domain_checksum}
					GROUP BY `referer_checksum` 
					ORDER BY `total` DESC, `dt` DESC ";
					//LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$v = array();
		$tableData['classes'] = array
		(
			'sort',
			'focus',
			'sort'
		);
		
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$ref_title = $this->Mint->abbr(preg_replace('!^https?://[^/]+!', '', $r['referer']));
				$res_title = $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				$tableData['tbody'][] = array
				(
					'&nbsp;',
					"<a href=\"{$r['referer']}\" rel=\"nofollow\">$ref_title</a>".(($this->Mint->cfg['preferences']['secondary'])?"<br /><span>To <a href=\"{$r['resource']}\">$res_title</a></span>":''),
					$r['total']
				);
			}
		}
		
		$html = $this->Mint->generateTableRows($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_PagesRecent()
	 **************************************************************************/
	function getHTML_PagesRecent() 
	{
		$html = '';
		
		$watched = $this->data['watched'];
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Page','class'=>'focus'),
			array('value'=>'&nbsp;','class'=>'watched sort'),
			array('value'=>'When','class'=>'sort')
		);
		
		$additional_columns = '';
		if (isset($this->Mint->cfg['manifest']['visit']['referred_by_feed']))
		{
			$additional_columns = ', `referred_by_feed`';
		}
		if ($this->hasCrush)
		{
			// add an empty table head to accomodate the new search column
			array_unshift($tableData['thead'], array('value'=>'&nbsp;','class'=>'search'));
			$additional_columns = ', `ip_long`';
			$SecretCrush =& $this->Mint->getPepperByClassName('SI_SecretCrush');
		}
		
		$query = "SELECT `referer`, `resource`, `resource_checksum`, `resource_title`, `search_terms`, `img_search_found`, `dt`{$additional_columns}
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					ORDER BY `dt` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
					
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$dt = $this->Mint->formatDateTimeRelative($r['dt']);
				if (!empty($r['search_terms']))
				{
					$class		= ($r['img_search_found']) ? ' class="image-search"' : '';
					$ref_title 	= $this->Mint->abbr(stripslashes($r['search_terms']));
					$ref_html	= "From a search for <a href=\"{$r['referer']}\" rel=\"nofollow\" {$class}>$ref_title</a>";
				}
				else
				{	
					$ref_title	= $this->Mint->abbr($r['referer']);
					$ref_html	= "From <a href=\"{$r['referer']}\" rel=\"nofollow\">$ref_title</a>";
				}
				if (isset($this->Mint->cfg['manifest']['visit']['referred_by_feed']) && $r['referred_by_feed'])
				{
					$ref_title	= "From a seed";
					$ref_html	= $ref_title;
				}
				
				$res_title = $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				
				$res_html = "<a href=\"{$r['resource']}\">$res_title</a>";
				if (!empty($ref_title) && $this->Mint->cfg['preferences']['secondary'])
				{
					$res_html .= "<br /><span>{$ref_html}</span>";
				}
				
				if (is_array($watched) && in_array($r['resource_checksum'], $watched)) 
				{
					$action = 'unwatch';
					$title	= 'Unwatch this page';
					$icon	= '&times;';
				}
				else 
				{
					$action = 'watch';
					$title	= 'Watch this page';
					$icon	= '+';
				}
				
				$actionLink = (!$this->Mint->isLoggedIn())?'':"<a href=\"#$action\" class=\"$action\" title=\"$title\" onclick=\"SI_manageWatched(this,'{$r['resource']}',0); return false;\">$icon</a>";
				
				$tableRow = array
				(
					$res_html,
					$actionLink,
					$dt
				);
				
				if ($this->hasCrush)
				{
					$ip = long2ip($r['ip_long']);
					array_unshift($tableRow, $SecretCrush->generateSearchIcon($ip, true));
				}
				
				$tableData['tbody'][] = $tableRow;
			}
		}
		
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}


	/**************************************************************************
	 getHTML_PagesPopular()
	 **************************************************************************/
	function getHTML_PagesPopular() 
	{
		$html = '';
		
		$filters = array
		(
			'Show all'	=> 0,
			'Past hour'	=> 1,
			'2h'		=> 2,
			'4h'		=> 4,
			'8h'		=> 8,
			'24h'		=> 24,
			'48h'		=> 48,
			'72h'		=> 72
		);
		
		$html .= $this->generateFilterList('Most Popular', $filters);
		
		$timespan = ($this->filter) ? " WHERE dt > ".(time() - ($this->filter * 60 * 60)) : '';
		
		$watched = $this->data['watched'];
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Hits','class'=>'sort'),
			array('value'=>'&nbsp;','class'=>'watched sort'),
			array('value'=>'Page','class'=>'focus')
		);
		
		$query = "SELECT `resource`, `resource_checksum`, `resource_title`, COUNT(`resource_checksum`) as `total`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` {$timespan}
					GROUP BY `resource_checksum` 
					ORDER BY `total` DESC, `dt` DESC
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$res_title = $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				$res_html = "<a href=\"{$r['resource']}\">$res_title</a>";
				
				if (is_array($watched) && in_array($r['resource_checksum'],$watched)) 
				{
					$action = 'unwatch';
					$title	= 'Unwatch this page';
					$icon	= '&times;';
				}
				else 
				{
					$action = 'watch';
					$title	= 'Watch this page';
					$icon	= '+';
				}
				
				$actionLink = (!$this->Mint->isLoggedIn())?'':"<a href=\"#$action\" class=\"$action\" title=\"$title\" onclick=\"SI_manageWatched(this,'{$r['resource']}',0); return false;\">$icon</a>";
				
				$tableData['tbody'][] = array
				(
					$r['total'],
					$actionLink,
					$res_html
				);
			}
		}
			
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_PagesEntry()
	 **************************************************************************/
	function getHTML_PagesEntry()
	{
		$html = '';
		
		$filters = array
		(
			'Show all'	=> 0,
			'Past hour'	=> 1,
			'2h'		=> 2,
			'4h'		=> 4,
			'8h'		=> 8,
			'24h'		=> 24,
			'48h'		=> 48,
			'72h'		=> 72
		);
		
		$html .= $this->generateFilterList('Entry', $filters);
		
		$timespan = ($this->filter) ? " AND dt > ".(time() - ($this->filter * 60 * 60)) : '';
		$referredByFeed = (isset($this->Mint->cfg['manifest']['visit']['referred_by_feed']) && $this->prefs['ignoreFeeds']) ? ' AND `referred_by_feed` = 0' : '';
		$watched = $this->data['watched'];
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Hits','class'=>'sort'),
			array('value'=>'&nbsp;','class'=>'watched sort'),
			array('value'=>'Page','class'=>'focus')
		);
		
		$query = "SELECT `resource`, `resource_checksum`, `resource_title`, COUNT(`resource_checksum`) as `total`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit`
					WHERE `referer_is_local` != 1 {$timespan} {$referredByFeed}
					GROUP BY `resource_checksum` 
					ORDER BY `total` DESC, `dt` DESC
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$res_title = $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				
				$res_html = "<a href=\"{$r['resource']}\">$res_title</a>";
				if (is_array($watched) && in_array($r['resource_checksum'],$watched)) 
				{
					$action = 'unwatch';
					$title	= 'Unwatch this page';
					$icon	= '&times;';
				}
				else 
				{
					$action = 'watch';
					$title	= 'Watch this page';
					$icon	= '+';
				}
				
				$actionLink = (!$this->Mint->isLoggedIn())?'':"<a href=\"#$action\" class=\"$action\" title=\"$title\" onclick=\"SI_manageWatched(this,'{$r['resource']}',0); return false;\">$icon</a>";
				
				$tableData['tbody'][] = array
				(
					$r['total'],
					$actionLink,
					$res_html
				);
			}
		}
			
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_PagesWatched()										ACCORDION-STYLE
	 **************************************************************************/
	function getHTML_PagesWatched() 
	{
		$html = '';

		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Hits','class'=>'sort'),
			array('value'=>'&nbsp;','class'=>'watched sort'),
			array('value'=>'Page/Referrers','class'=>'focus'),
			array('value'=>'Referrers','class'=>'sort')
		);
		
		$where = '';
		
		$watched = $this->data['watched'];
		
		if (!empty($watched)) 
		{
			$tableData['hasFolders']		= true;
			$tableData['table']['class']	= 'folder';
			
			$where = "WHERE (`resource_checksum` = '".implode("' OR `resource_checksum` = '", $watched)."')";
		}
		else
		{ 
			$where = "WHERE id = -1";
			$tableData['tbody'][] = array
			(
				'',
				'',
				'You have no Watched Pages',
				''
			);
		}
		
		$referrerCounts = array();
		$pageData = array();
		
		$query = "SELECT `resource_checksum`, COUNT(DISTINCT `referer`) AS `referrers`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					$where AND `referer_is_local` = 0 AND `search_terms` = '' 
					GROUP BY `resource_checksum` ORDER BY `referrers` DESC
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_assoc($result)) 
			{
				$referrerCounts[$r['resource_checksum']] = $r['referrers'];
			}
		}
		
		$query = "SELECT `resource`, `resource_title`, `resource_checksum`, COUNT(`resource_checksum`) AS `total`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					$where 
					GROUP BY `resource_checksum` ORDER BY `total` DESC
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_assoc($result)) 
			{
				$pageData[$r['resource_checksum']] = $r;
			}
		}
		
		foreach ($pageData as $checksum => $page)
		{
			$res_title = $this->Mint->abbr((!empty($page['resource_title']))?stripslashes($page['resource_title']):$page['resource']);
			$tableData['tbody'][] = array
			(
				$page['total'],
				"<a href=\"#unwatch\" class=\"unwatch\" title=\"Unwatch this page\" onclick=\"SI_manageWatched(this,'{$page['resource']}',1); return false;\">&times;</a>",
				$res_title,
				(isset($referrerCounts[$checksum])) ? $referrerCounts[$checksum] : 0,

				'folderargs' => array
				(
					'action'	=> 'getreferrers',
					'checksum'	=> $checksum
				)
			);
		}
		
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_PagesWatchedReferrers()
	 **************************************************************************/
	function getHTML_PagesWatchedReferrers($checksum)
	{
		// Ignore certain domains
		$ignoredDomains	= preg_split('/[\s,]+/', $this->prefs['ignoreReferringDomains']);
		$ignored = '';
		if (!empty($ignoredDomains))
		{
			foreach ($ignoredDomains as $domain)
			{
				if (empty($domain))
				{
					continue;
				}
				$ignored .= ' AND `domain_checksum` != '.crc32($domain);
			}
		}
		
		$html = '';
		$tableData['tbody'] = array();
		$query = "SELECT `referer`, COUNT(`referer`) as `total`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `resource_checksum` = '$checksum' AND `referer_checksum` != 0 AND `referer_is_local` = 0  AND `search_terms` = '' $ignored
					GROUP BY `referer_checksum` 
					ORDER BY `total` DESC, `dt` DESC ";
					//LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$tableData['classes'] = array
		(
			'sort',
			'watched',
			'focus',
			'sort'
		);
		
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$tableData['tbody'][] = array
				(
					'',
					'',
					'<span>From <a href="'.$r['referer'].'" rel="nofollow">'.$this->Mint->abbr($r['referer']).'</a></span>',
					$r['total']
				);
			}
		}
	
		if (empty($tableData['tbody']))
		{
			$tableData['tbody'][] = array
			(
				'',
				'',
				'<span>No external referrers</span>',
				''
			);
		}
		
		$html = $this->Mint->generateTableRows($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_SearchesRecent()
	 **************************************************************************/
	function getHTML_SearchesRecent() 
	{
		$html = '';
		
		$filters = array
		(
			'Show all'	=> 0,
			'Web'		=> 1,
			'Image'		=> 2
		);
		
		$html .= $this->generateFilterList('Most Recent', $filters, array('Most Common'));
		$filter = ($this->filter) ? (($this->filter == 2) ? ' AND `img_search_found` = 1' : ' AND `img_search_found` = 0') : '';
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Keywords','class'=>'focus'),
			array('value'=>'When','class'=>'sort')
		);
		
		$additional_columns = '';
		if ($this->hasCrush)
		{
			// add an empty table head to accomodate the new search column
			array_unshift($tableData['thead'], array('value'=>'&nbsp;','class'=>'search'));
			$additional_columns = ', `ip_long`';
			$SecretCrush =& $this->Mint->getPepperByClassName('SI_SecretCrush');
		}
			
		$query = "SELECT `referer`, `search_terms`, `resource`, `resource_title`, `dt`, `img_search_found`{$additional_columns}
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE
					`search_terms`!='' $filter
					ORDER BY `dt` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$dt = $this->Mint->formatDateTimeRelative($r['dt']);
				$search_terms	= $this->Mint->abbr(stripslashes($r['search_terms']));
				$res_title		= $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				$class			= ($r['img_search_found']) ? ' class="image-search"' : '';
				
				$tableRow = array
				(
					"<a href=\"{$r['referer']}\" rel=\"nofollow\"{$class}>$search_terms</a>".(($this->Mint->cfg['preferences']['secondary'])?"<br /><span>Found <a href=\"{$r['resource']}\">$res_title</a></span>":''),
					$dt
				);
				
				if ($this->hasCrush)
				{
					$ip = long2ip($r['ip_long']);
					array_unshift($tableRow, $SecretCrush->generateSearchIcon($ip, true));
				}
				
				$tableData['tbody'][] = $tableRow;
			}
		}
			
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}


	/**************************************************************************
	 getHTML_SearchesCommon()
	 **************************************************************************/
	function getHTML_SearchesCommon() 
	{
		$html = '';
		
		$filters = array
		(
			'Show all'	=> 0,
			'Web'		=> 1,
			'Image'		=> 2
		);
		
		$html .= $this->generateFilterList('Most Common', $filters, array('Most Recent'));
		$filter = ($this->filter) ? (($this->filter == 2) ? ' AND `img_search_found` = 1' : ' AND `img_search_found` = 0') : '';
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Hits','class'=>'sort'),
			array('value'=>'Keywords','class'=>'focus')
		);
		
		$query = "SELECT `referer`, `search_terms`, `resource`, `resource_title`, COUNT(`referer`) as `total`, `dt`, `img_search_found`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE
					`search_terms`!='' $filter
					GROUP BY `search_terms` 
					ORDER BY `total` DESC, `dt` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$search_terms	= $this->Mint->abbr(stripslashes($r['search_terms']));
				$res_title		= $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				$class			= ($r['img_search_found']) ? ' class="image-search"' : '';
				$tableData['tbody'][] = array
				(
					$r['total'],
					"<a href=\"{$r['referer']}\" rel=\"nofollow\"{$class}>$search_terms</a>".(($this->Mint->cfg['preferences']['secondary'])?"<br /><span>Found <a href=\"{$r['resource']}\">$res_title</a></span>":'')
				);
			}
		}
			
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_SearchesFound()
	 **************************************************************************/
	function getHTML_SearchesFound()
	{
		$html = '';
		
		$tableData['hasFolders'] = true;
		$tableData['table'] = array('id'=>'','class'=>'folder');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'Keywords','class'=>'sort'),
			array('value'=>'Page','class'=>'focus'),
			array('value'=>'Hits','class'=>'sort')
		);
		
		$query = "SELECT `resource`, `resource_title`, `resource_checksum`, COUNT(DISTINCT `search_terms`) as `sources`, COUNT(`resource_checksum`) as `hits`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `referer_checksum` != 0 AND `search_terms` != ''
					GROUP BY `resource_checksum` 
					ORDER BY `hits` DESC, `sources` DESC, `dt` DESC
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		if ($result = $this->query($query)) 
		{
			while ($r = mysql_fetch_array($result)) 
			{
				$res_title = $this->Mint->abbr((!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource']);
				
				$tableData['tbody'][] = array
				(
					$r['sources'],
					$res_title,
					$r['hits'],

					'folderargs' => array
					(
						'action'			=>'getKeywordsByPage',
						'resource_checksum'	=>$r['resource_checksum']
					)
				);
			}
		}
		$html = $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_KeywordsByPage()
	 **************************************************************************/
	function getHTML_KeywordsByPage($resource_checksum)
	{
		$html = '';
		
		$query = "SELECT `referer`, `search_terms`, COUNT(`search_terms`) as `total`, `dt`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE `search_terms` != '' AND `resource_checksum` = {$resource_checksum}
					GROUP BY `search_terms` 
					ORDER BY `total` DESC, `dt` DESC ";
					//LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$v = array();
		$tableData['classes'] = array
		(
			'sort',
			'focus',
			'sort'
		);
		
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$search_title = $this->Mint->abbr($r['search_terms']);
				$tableData['tbody'][] = array
				(
					'&nbsp;',
					"<a href=\"{$r['referer']}\" rel=\"nofollow\">$search_title</a>",
					$r['total']
				);
			}
		}
		
		$html = $this->Mint->generateTableRows($tableData);
		return $html;
	}
	
	/**************************************************************************
	 array_prune()
	 
	 Removes earlier indexes until the array is the specified length. Acts like
	 array_shift() if no length is specified but preserves integer indexes.
	 **************************************************************************/
	function array_prune($array, $length = -1)
	{
		// exit ASAP if pruning is unnecessary
		if ($length != -1 && count($array) <= $length)
		{
			return $array;
		}
		
		// No length specified, default to array_shift behavior
		if ($length==-1) 
		{
			$length = count($array)-1;
		}
		
		// Order ascending
		ksort($array);
		reset($array);
		
		// Go get 'em tiger
		$n = count($array)-$length;
		foreach($array as $key=>$val) 
		{
			if ($n>0) 
			{ 
				unset($array[$key]); 
				$n--;
			}
			else 
			{
				break;
			}
		}
		return $array;
	}
	
	/**************************************************************************
	 trimPrefixIndex()
	 
	 Removes www. and index.* from a url in an attempt to normalize disparate urls
	 **************************************************************************/
	function trimPrefixIndex($url) 
	{
		return preg_replace("/^http(s)?:\/\/www\.([^.]+\.)/i", "http$1://$2", preg_replace("/\/index\.[^?]+/i", "/", $url));
	}
}