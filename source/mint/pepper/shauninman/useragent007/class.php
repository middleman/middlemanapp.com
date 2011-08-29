<?php
/******************************************************************************
 Pepper
 
 Developer		: Shaun Inman
 Plug-in Name	: User Agent 007
 
 http://www.shauninman.com/

 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file
$installPepper = "SI_UserAgent";

class SI_UserAgent extends Pepper
{
	var $version	= 210; 
	var $info		= array
	(
		'pepperName'	=> 'User Agent 007',
		'pepperUrl'		=> 'http://www.haveamint.com/',
		'pepperDesc'	=> 'Mint. Peppermint. User Agent 007 goes undercover to uncover who\'s using which browser on which platform at what resolution with which plug-ins installed.',
		'developerName'	=> 'Shaun Inman',
		'developerUrl'	=> 'http://www.shauninman.com/'
	);
	var $panes		= array
	(
		'User Agents'	=> array
		(
			'Browsers',
			'Platform',
			'Screen',
			'Flash'
		)
	);
	var $altPanes		= array
	(
		'User Agents'	=> array
		(
			'Browsers',
			'Readers',
			'Platform',
			'Screen',
			'Flash'
		)
	);
	var $prefs		= array
	(
		'showReaders' => false
	);
	var $manifest	= array
	(
		'visit'	=> array
		(
			'browser_family' 	=> "VARCHAR(255) NOT NULL",
			'browser_version' 	=> "VARCHAR(15) NOT NULL",
			'platform' 			=> "VARCHAR(255) NOT NULL",
			'platform_version' 	=> "VARCHAR(255) NOT NULL",
			'resolution' 		=> "VARCHAR(13) NOT NULL",
			'flash_version' 	=> "TINYINT(2) NOT NULL"
		)
	);
	
	/**************************************************************************
	 isCompatible()
	 **************************************************************************/
	function isCompatible()
	{
		if ($this->Mint->version < 200)
		{
			$compatible = array
			(
				'isCompatible'	=> false,
				'explanation'	=> '<p>This Pepper requires Mint 2, a paid upgrade, now available at <a href="http://www.haveamint.com/">haveamint.com</a>.</p>'
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
	 update()
	 **************************************************************************/
	function update()
	{
		if (!isset($this->prefs['showReaders']))
		{
			$this->prefs['showReaders'] = false;
		}
		
		// make sure our readers tab persists across an update
		if ($this->prefs['showReaders'])
		{
			$this->Mint->pepper[$this->pepperId]->panes = $this->altPanes;
		}
	}
	
	/**************************************************************************
	 onJavaScript()
	 **************************************************************************/
	function onJavaScript() 
	{
		$js = MINT_ROOT.'pepper/shauninman/useragent007/script.js';
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
 		if (empty($_GET)) { return array(); }
 		
 		$ua 				= $this->parseUserAgent($_SERVER['HTTP_USER_AGENT']);
 		$browser_family 	= $this->Mint->escapeSQL($ua['family']);
 		$browser_version	= $this->Mint->escapeSQL($ua['version']);
 		$platform			= $this->Mint->escapeSQL($ua['platform']);
 		$platform_version	= $this->Mint->escapeSQL($ua['build']);
 		
 		$resolution		= $this->Mint->escapeSQL($_GET['resolution']);
 		$flash_version	= $this->Mint->escapeSQL($_GET['flash_version']);
 		
		return array
 		(
 			'resolution'		=> $resolution,
 			'flash_version'		=> $flash_version,
 			'browser_family'	=> $browser_family,
 			'browser_version'	=> $browser_version,
 			'platform'			=> $platform,
 			'platform_version'	=> $platform_version
 		);
	}
	
	/**************************************************************************
	 onDisplay()
	 **************************************************************************/
	function onDisplay($pane, $tab, $column = '', $sort = '')
	{
		$html = '';
		
		switch($pane) 
		{
			/* User Agents *****************************************************/
			case 'User Agents': 
				switch($tab) 
				{
					/* Browsers ***********************************************/
					case 'Browsers':
						$html .= $this->getHTML_Browsers();
					break;
					/* Readers ************************************************/
					case 'Readers':
						$html .= $this->getHTML_Readers();
					break;
					/* Platform ***********************************************/
					case 'Platform':
						$html .= $this->getHTML_Platform();
					break;
					/* Resolution *********************************************/
					case 'Screen':
						$html .= $this->getHTML_Screen();
					break;
					/* Flash **************************************************/
					case 'Flash':
						$html .= $this->getHTML_FlashVersion();
					break;
				}
			break;
		}
		return $html;
	}
	
	/**************************************************************************
	 onDisplayPreferences()
	 **************************************************************************/
	function onDisplayPreferences() 
	{
		$preferences = array();
		
		if (isset($this->Mint->cfg['pepperLookUp']['SI_BirdFeeder']))
		{
			/* Readers ********************************************************/
			$checked = ($this->prefs['showReaders'])?' checked="checked"':'';
			$preferences['Bird Feeder Integration']	= <<<HERE
			<table>
				<tr>
					<td><label><input type="checkbox" name="showReaders" value="1"$checked /> Show feed reader usage collected by the Bird Feeder Pepper</label></td>
				</tr>
			</table>
HERE;
		}
		
		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{
		$showReaders = (isset($_POST['showReaders']))?$_POST['showReaders']:false;
		
		if ($this->prefs['showReaders'] != $showReaders)
		{
			$this->prefs['showReaders'] = $showReaders;
			$this->toggleReadersTab();
		}
	}
	
	/**************************************************************************
	 onCustom()
	 **************************************************************************/
	function onCustom() 
	{
		if 
		(
			isset($_POST['action']) 				&& 
			isset($_POST['family']) 				&& 
			isset($_POST['total'])
		)
		{
			$family	= $this->escapeSQL($_POST['family']);
			$total	= $this->escapeSQL($_POST['total']);
			
			switch($_POST['action'])
			{
				case 'getBrowserVersion':
					echo $this->getHTML_BrowserVersions($family, $total);
				break;
				
				case 'getPlatformVersion':
					echo $this->getHTML_PlatformVersions($family, $total);
				break;
				
				case 'getReaderVersion':
					echo $this->getHTML_ReaderVersions($family, $total);
				break;
			}
		}
	}
	
	/**************************************************************************
	 getHTML_Browsers()
	 
	 **************************************************************************/
	function getHTML_Browsers()
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
		$html .= $this->generateFilterList('Browsers', $filters, $this->panes['User Agents']);
		$timespan = ($this->filter) ? " AND dt > ".(time() - ($this->filter * 60 * 60)) : '';
		
		$tableData['hasFolders'] = true;
		
		$tableData['table'] = array('id'=>'','class'=>'folder');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'% of Total','class'=>'sort'),
			array('value'=>'Browser Family/Versions','class'=>'focus')
		);
		
		$query = "SELECT `browser_family`, COUNT(`browser_family`) as `total`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE
					`browser_family`!='' {$timespan}
					GROUP BY `browser_family` 
					ORDER BY `total` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$fam	= array();
		$total	= 0;
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$fam[$r['browser_family']] = $r['total'];
				$total += $r['total'];
			}
		}
		foreach ($fam as $family=>$count) {
			
			$percent = $count / $total * 100;
			
			$row = array
			(
				$this->Mint->formatPercents($percent),
				$family,
				
				'folderargs' => array
				(
					'action'	=> 'getBrowserVersion',
					'family'	=> $family,
					'total'		=> $total
				)
			);
			
			if (round($percent) < 5)
			{
				$row['class'] = 'insig';
			}
			
			$tableData['tbody'][] = $row;
		}
		
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_Readers()
	 
	 **************************************************************************/
	function getHTML_Readers() {
		$html = '';
		
		$tableData['hasFolders'] = true;
		$tableData['table'] = array('id'=>'','class'=>'folder');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'% of Total','class'=>'sort'),
			array('value'=>'Reader Family/Versions','class'=>'focus')
		);
		
		$query = "SELECT `reader`, SUM(`reader_count`) as `total`
					FROM `{$this->Mint->db['tblPrefix']}readership` 
					WHERE
					`reader`!='' 
					GROUP BY `reader` 
					ORDER BY `total` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$fam	= array();
		$total	= 0;
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$fam[$r['reader']] = $r['total'];
				$total += $r['total'];
			}
		}
		foreach ($fam as $family=>$count) {
			
			$percent = $count / $total * 100;
			
			$row = array
			(
				$this->Mint->formatPercents($percent),
				$family,
				
				'folderargs' => array
				(
					'action'	=> 'getReaderVersion',
					'family'	=> $family,
					'total'		=> $total
				)
			);
			
			if (round($percent) < 5)
			{
				$row['class'] = 'insig';
			}
			
			$tableData['tbody'][] = $row;
		}
		
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_BrowserVersions()
	 **************************************************************************/
	function getHTML_BrowserVersions($family, $total)
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
		$this->generateFilterList('Browsers', $filters);
		$timespan = ($this->filter) ? " AND dt > ".(time() - ($this->filter * 60 * 60)) : '';
		
		$query = "SELECT `browser_version`, COUNT(`browser_version`) as `total`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE
					`browser_family`='$family' {$timespan}
					GROUP BY `browser_version` 
					ORDER BY `total` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$v = array();
		$tableData['classes'] = array
		(
			'sort',
			'focus'
		);
		
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$percent = $r['total'] / $total * 100;

				$row = array
				(
					$this->Mint->formatPercents($percent),
					$r['browser_version']
				);

				if (round($percent) < 5)
				{
					$row['class'] = 'insig';
				}

				$tableData['tbody'][] = $row;
			}
		}
		
		$html = $this->Mint->generateTableRows($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_PlatformVersions()
	 **************************************************************************/
	function getHTML_PlatformVersions($platform, $total)
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
		$this->generateFilterList('Platform', $filters);
		$timespan = ($this->filter) ? " AND dt > ".(time() - ($this->filter * 60 * 60)) : '';
		
		$query = "SELECT `platform_version`, COUNT(`platform_version`) as `total`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE
					`platform`='$platform' {$timespan}
					GROUP BY `platform_version` 
					ORDER BY `total` DESC 
					LIMIT 0, {$this->Mint->cfg['preferences']['rows']}";
		
		$v = array();
		$tableData['classes'] = array
		(
			'sort',
			'focus'
		);
		
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$percent = $r['total'] / $total * 100;
				
				// handle pre-update rows
				if (empty($r['platform_version']))
				{
					$r['platform_version'] = 'Unrecorded';
				}
				
				$row = array
				(
					$this->Mint->formatPercents($percent),
					$r['platform_version'].' '
				);

				if (round($percent) < 5)
				{
					$row['class'] = 'insig';
				}

				$tableData['tbody'][] = $row;
			}
		}
		
		$html = $this->Mint->generateTableRows($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_ReaderVersions()
	 **************************************************************************/
	function getHTML_ReaderVersions($family, $total)
	{
		$html = '';
		
		$query = "SELECT `reader_version`, SUM(`reader_count`) as `total`
					FROM `{$this->Mint->db['tblPrefix']}readership` 
					WHERE
					`reader`='$family'
					GROUP BY `reader_version` 
					ORDER BY `total` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$v = array();
		$tableData['classes'] = array
		(
			'sort',
			'focus'
		);
		
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$percent = $r['total'] / $total * 100;

				$row = array
				(
					$this->Mint->formatPercents($percent),
					$r['reader_version']
				);

				if (round($percent) < 5)
				{
					$row['class'] = 'insig';
				}

				$tableData['tbody'][] = $row;
			}
		}
		
		$html = $this->Mint->generateTableRows($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_Platform()
	 **************************************************************************/
	function getHTML_Platform()
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
		
		$html .= $this->generateFilterList('Platform', $filters, $this->panes['User Agents']);
		
		$timespan = ($this->filter) ? " AND dt > ".(time() - ($this->filter * 60 * 60)) : '';
		
		$tableData['hasFolders'] = true;
		$tableData['table'] = array('id'=>'','class'=>'folder');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'% of Total','class'=>'sort'),
			array('value'=>'Platform','class'=>'focus')
		);
		
		$query = "SELECT `platform`, COUNT(`platform`) as `total`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE
					`platform`!='' {$timespan}
					GROUP BY `platform` 
					ORDER BY `total` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$platforms	= array();
		$total		= 0;
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$platforms[$r['platform']] = $r['total'];
				$total += $r['total'];
			}
		}
		foreach ($platforms as $platform=>$count) {
			
			$percent = $count / $total * 100;
			
			$row = array
			(
				$this->Mint->formatPercents($percent),
				$platform,

				'folderargs' => array
				(
					'action'	=> 'getPlatformVersion',
					'family'	=> $platform,
					'total'		=> $total
				)
			);
			
			if (round($percent) < 5)
			{
				$row['class'] = 'insig';
			}
			
			$tableData['tbody'][] = $row;
		}
		
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_Screen()
	 **************************************************************************/
	function getHTML_Screen() 
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
		
		$html .= $this->generateFilterList('Screen', $filters, $this->panes['User Agents']);
		
		$timespan = ($this->filter) ? " AND dt > ".(time() - ($this->filter * 60 * 60)) : '';
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'% of Total','class'=>'sort'),
			array('value'=>'Resolution','class'=>'focus')
		);
	
		$query = "SELECT `resolution`, COUNT(`resolution`) as `total`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					WHERE
					`resolution`!='' {$timespan}
					GROUP BY `resolution` 
					ORDER BY `total` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
	
		$res	= array();
		$total	= 0;
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$res[$r['resolution']] = $r['total'];
				$total += $r['total'];
			}
		}
		foreach ($res as $resolution=>$count) 
		{
			$percent = $count / $total * 100;
		
			$row = array
			(
				$this->Mint->formatPercents($percent),
				str_replace("x"," &times; ",$resolution)
			);
		
			if (round($percent) < 5)
			{
				$row['class'] = 'insig';
			}
		
			$tableData['tbody'][] = $row;
		}
		
		$html .= $this->Mint->generateTable($tableData);
		return $html;
	}
	
	/**************************************************************************
	 getHTML_FlashVersion()
	 **************************************************************************/
	function getHTML_FlashVersion()
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
		
		$html .= $this->generateFilterList('Flash', $filters, $this->panes['User Agents']);
		
		$timespan = ($this->filter) ? " WHERE dt > ".(time() - ($this->filter * 60 * 60)) : '';
		
		$tableData['table'] = array('id'=>'','class'=>'');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'% of Total','class'=>'sort'),
			array('value'=>'Version Installed','class'=>'focus')
		);
		
		$query = "SELECT `flash_version`, COUNT(`flash_version`) as `total`
					FROM `{$this->Mint->db['tblPrefix']}visit` 
					{$timespan}
					GROUP BY `flash_version` 
					ORDER BY `total` DESC 
					LIMIT 0,{$this->Mint->cfg['preferences']['rows']}";
		
		$version	= array();
		$total		= 0;
		
		if ($result = $this->query($query))
		{
			while ($r = mysql_fetch_array($result))
			{
				$version[$r['flash_version']] = $r['total'];
				$total += $r['total'];
			}
		}
		foreach ($version as $flash_version => $count)
		{
			if ($flash_version == 0 || $flash_version == 16)
			{
				$flash_version = "None";
			}
			else
			{
				$flash_version = "Flash ".$flash_version;
			}
			
			$percent = $count / $total * 100;
			
			$row = array
			(
				$this->Mint->formatPercents($percent),
				$flash_version
			);
			
			if (round($percent) < 5)
			{
				$row['class'] = 'insig';
			}
			
			$tableData['tbody'][] = $row;
		}
		
		$html .= $this->Mint->generateTable($tableData);
		return $html;
		}
	
	/**************************************************************************
	 toggleReadersTab()
	 **************************************************************************/
	function toggleReadersTab()
	{
		$panesId = $this->Mint->cfg['pepperShaker'][$this->pepperId]['panes'][0];
		$source = (!$this->prefs['showReaders']) ? 'panes' : 'altPanes';
		$this->Mint->cfg['panes'][$panesId]['tabs'] = $this->{$source}['User Agents'];
	}
		
	/**************************************************************************
	 parseUserAgent()
	 **************************************************************************/
	function parseUserAgent($user_agent) 
	{
		$ua['platform']	= "Unknown";
		$ua['build']	= "Unknown";
		$ua['family']	= "Unknown";
		$ua['version']	= "Unknown";

		if (preg_match('/(?<!dar)win/i', $user_agent)) 
		{
			$ua['platform'] = 'Windows';

			if (preg_match_all('#(?<!dar)(win[^;/]+);?#i', $user_agent, $m))
			{
				$builds = array
				(
					'Windows NT 6.1'		 	=> '7',
					'Windows NT 6.0'		 	=> 'Vista',
					'Windows NT 5.2'		 	=> 'XP',
					'Windows NT 5.1'		 	=> 'XP',
					'Windows NT 5.01'		 	=> '2000',
					'Windows NT 5.0'		 	=> '2000',
					'Windows NT 4.0'		 	=> 'NT',
					'WinNT'					 	=> 'NT',
					'Windows 98; Win 9x 4.90'	=> 'ME',
					'Windows 98'			 	=> '98',
					'Win98'					 	=> '98',
					'Windows 95'			 	=> '95',
					'Win95'					 	=> '95',
					'Windows CE'			 	=> 'CE',
					'WinCE'					 	=> 'CE'
				);
				foreach($m[1] as $build)
				{
					if (isset($builds[$build]))
					{
						$ua['build'] = $builds[$build];
						continue;
					}
				}
			}
		}
		else if (preg_match('/mac(?!hine)/i', $user_agent))
		{
			$ua['platform'] = 'Macintosh';

			if (preg_match('#Mac OS X (10[^;]+)#i', $user_agent, $m))
			{
				$build = substr(str_replace('_', '.', $m[1]), 0, 4);
				$builds = array
				(
					'10.0' => 'Cheetah',
					'10.1' => 'Puma',
					'10.2' => 'Jaguar',
					'10.3' => 'Panther',
					'10.4' => 'Tiger',
					'10.5' => 'Leopard',
					'10.6' => 'Snow Leopard',
					'10.7' => 'Lion'
				);

				if (isset($builds[$build]))
				{
					$ua['build'] = $builds[$build];
				}
			}
			else if (strpos($user_agent, 'iPhone') !== false)
			{
				$ua['build'] = 'iPhone';
			}
			else if (strpos($user_agent, 'iPad') !== false)
			{
				$ua['build'] = 'iPad';
			}
		}
		else if (preg_match('/linux/i', $user_agent))
		{
			$ua['platform'] = 'Linux';
		}
		else if (preg_match('/Nintendo Wii/i', $user_agent))
		{
			$ua['platform'] = 'Wii';
		}
		else if (preg_match('/Nitro\) Opera/i', $user_agent))
		{
			$ua['platform'] = 'Nintendo DS';
		}
		else if (preg_match('/PlayStation Portable/i', $user_agent))
		{
			$ua['platform'] = 'PlayStation Portable';
		}
		else
		{
			$ua['platform'] = 'Other';
		}

		// Should never detect robots that are unable to run JavaScript but they are in here anyway
		if (preg_match_all('/(chrome|safari|blazer|shiira|firefox|firebird|feedonfeeds|flock|phoenix|galeon|liferea|netnewswire|pulpfiction|feeddemon|magpierss|shrook|newsfire|bloglines|feedmania|avantgo|newsgator|opera|omniweb|camino|chimera|epiphany|konqueror|icab|lynx|(?<!find)links)(?: \(|\/|[^\/]*\/| )v?([0-9.]*)/i', $user_agent, $m))
		{
			$ua['family']	= $m[1][0];
			$ua['version']	= $m[2][0];

			// iPhone & MobileSafari
			if ($ua['family'] == 'Safari' && strpos($user_agent, 'iPhone') !== false)
			{
				$ua['family'] = 'MobileSafari';
			}

			if (isset($m[1][1], $m[2][1]))
			{
				// catch for OmniWeb which will match first as Safari
				if ($m[1][0] == 'Safari')
				{
					$ua['family']	= $m[1][1];
					$ua['version']	= $m[2][1];
				}
				// catch for Flock which will match first as Firefox
				else if ($m[1][0] == 'Firefox')
				{
					$ua['family']	= $m[1][1];
					$ua['version']	= $m[2][1];
				}
			}
		}
		else if (preg_match_all('/(applewebkit)\/([0-9.]*)/i', $user_agent, $b))
		{
			$ua['family']	= $b[1][0]." (Generic)";
			$ua['version']	= $b[2][0];
		}
		else if (preg_match('/Mozilla\/(4[0-9.]*)/', $user_agent, $v) && !preg_match('/(compatible|MSIE|bot|crawler)/i', $user_agent))
		{
			$ua['family']	= "Netscape";
			$ua['version']	= $v[1];
		}
		else if (strpos($user_agent, 'Mozilla/5.0') !== false && preg_match_all('/(netscape)(?:[0-9]\/)?\/?([0-9.]*)/i', $user_agent, $b))
		{
			$ua['family']	= $b[1][0];
			$ua['version']	= $b[2][0];
		}
		else if (strpos($user_agent, 'Mozilla/5.0') !== false && preg_match('/rv(?: |:)([0-9.]*)/i', $user_agent, $v))
		{
			$ua['family']	= "Mozilla";
			$ua['version']	= $v[1];
		}
		else if (preg_match('/MSIE ?([0-9.]*)/i', $user_agent, $v) && !preg_match('/(bot|(?<!mytotal)search|seeker)/i', $user_agent))
		{
			$ua['family']	= "Internet Explorer";
			$ua['version']	= $v[1];
		}
		else if (preg_match('/(?:bot|obo|spider|crawl|client|feed|slurp|seek|dex|google|track|findlinks|email|search|ask|validator|archive)/i', $user_agent))
		{
			$ua['family'] = "Crawler/Search Engine";
			if (preg_match('/(ask jeeves|google|yahoo|msn|altavista|lycos|css info|feedthing|popdexter|kinja|aol|findlinks|atomz|blogbot|wotbox|feedster|simpy|bobby|blogpulse|technorati|w3search|validator|slurp)/i',$user_agent,$v))
			{
				$ua['version'] = $v[1];
			}
		}
		
		// this seems to be an evolving standard for reporting the actual 
		// application version rather than the rendering engine version
		// current implemented by Safari and Opera
		if (preg_match('/Version\/([.0-9]+)/i', $user_agent, $m))
		{
			$ua['version'] = $m[1];
		}
		return $ua;
	}
}