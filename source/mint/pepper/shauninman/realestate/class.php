<?php
/******************************************************************************
 Pepper
 
 Developer		: Shaun Inman
 Plug-in Name	: Real Estate
 
 http://www.shauninman.com/

 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file
$installPepper = "SI_RealEstate";
	
class SI_RealEstate extends Pepper
{
	var $version	= 201;
	var $info		= array
	(
		'pepperName'	=> 'Real Estate',
		'pepperUrl'		=> 'http://www.haveamint.com/',
		'pepperDesc'	=> 'The Real Estate Pepper picks up where User Agent 007 leaves off by tracking the width and height of the browser window on each page load helping you to make more informed design decisions than with just screen dimensions alone.',
		'developerName'	=> 'Shaun Inman',
		'developerUrl'	=> 'http://www.shauninman.com/'
	);
	var $panes = array
	(
		'Real Estate' => array
		(
			'Refresh'
		)
	);
	var $oddPanes	= array
	(
		'Real Estate'
	);
	var $prefs = array
	(
		// Common resolutions widths minus worst-case browser chrome (56)
		'widthGroups' => '584, 744, 968, 1096, 1344, 1384, 1544, 1624, 1824',
		// Common resolutions heights minus realistic worst-case browser chrome (70)
		'heightGroups' => '410, 530, 598, 630, 684, 700, 730, 800, 830, 890, 938, 954, 1130, 1530'
	);
	var $manifest = array
	(
		'visit'	=> array
		(
			'window_width'	=> "SMALLINT(5) NOT NULL DEFAULT '-1'",
			'window_height'	=> "SMALLINT(5) NOT NULL DEFAULT '-1'"
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
	 onJavaScript()
	 **************************************************************************/
	function onJavaScript() 
	{
		$js = MINT_ROOT.'pepper/shauninman/realestate/script.js';
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
 		$windowWidth =  $this->Mint->escapeSQL($_GET['window_width']);
	 	$windowHeight =  $this->Mint->escapeSQL($_GET['window_height']);
		return array
		(
			'window_width' => (int) $windowWidth,
			'window_height' => (int) $windowHeight
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
			/* Window Widths ***************************************************/
			case 'Real Estate': 
				switch($tab)
				{
					/* Refresh ************************************************/
					case 'Refresh':
						$html .= $this->getHTML_RealEstate();
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
		$defaultGroups = get_class_vars('SI_RealEstate');
		
		/* Widths *************************************************************/
		$preferences['Widths']	= <<<HERE
<table>
	<tr>
		<th>Group widths greater than </th>
	</tr>
	<tr>
		<td><span><textarea id="widthGroups" name="widthGroups" rows="6" cols="30">{$this->prefs['widthGroups']}</textarea></span></td>
	</tr>
	<tr>
		<td><a href="#default" onclick="document.getElementById('widthGroups').value = '{$defaultGroups['prefs']['widthGroups']}'; return false;" style="float: left; margin: 0 11px 11px 0;"><img src="pepper/shauninman/realestate/images/btn-default-mini-single.png" width="51" height="17" alt="Default" /></a> Common resolution widths minus the realistic worst-case scenario browser chrome width of 56px.</td>
	</tr>
</table>

HERE;

		/* Heights ************************************************************/
		$preferences['Heights']	= <<<HERE
<table>
	<tr>
		<th>Group heights greater than </th>
	</tr>
	<tr>
		<td><span><textarea id="heightGroups" name="heightGroups" rows="6" cols="30">{$this->prefs['heightGroups']}</textarea></span></td>
	</tr>
	<tr>
		<td><a href="#default" onclick="document.getElementById('heightGroups').value = '{$defaultGroups['prefs']['heightGroups']}'; return false;" style="float: left; margin: 0 11px 11px 0;"><img src="pepper/shauninman/realestate/images/btn-default-mini-single.png" width="51" height="17" alt="Default" /></a> Common resolution heights minus the realistic worst-case scenario browser chrome height of 70px.</td>
	</tr>
</table>

HERE;
		
		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{
		$this->prefs['widthGroups']	= $this->escapeSQL($_POST['widthGroups']);
		$this->prefs['heightGroups']	= $this->escapeSQL($_POST['heightGroups']);
	}
	
	/**************************************************************************
	 getHTML_WindowWidth()
	 **************************************************************************/
	function getHTML_RealEstate()
	{
		$html = '';
		
		$widths	= preg_split('/[\s,]+/', $this->prefs['widthGroups']);
		$widths[] = 0;
		sort($widths);
		
		$tableData['table'] = array('id'=>'','class'=>'inline striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'% of Total','class'=>'sort'),
			array('value'=>'Window Width','class'=>'focus')
		);
		
		foreach ($widths as $width)
		{
			$query = "SELECT COUNT(`window_width`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit` 
						WHERE `window_width` >= $width";
			
			if ($result = $this->query($query)) 
			{
				if ($r = mysql_fetch_array($result)) 
				{
					if (!$width)
					{
						$total = ($r['total'])?$r['total']:1;
						continue;
					}
					if ($r['total'])
					{
						$percent = $r['total'] / $total * 100;
						
						$row = array
						(
							$this->Mint->formatPercents($percent),
							'<span>&gt;</span> '.$width
						);
						
						if (round($percent) < 5)
						{
							$row['class'] = 'insig';
						}
						
						$tableData['tbody'][] = $row;
					}
				}
			}
		}
			
		$widthHTML = $this->Mint->generateTable($tableData);
		unset($tableData);
		
		$heights	= preg_split('/[\s,]+/', $this->prefs['heightGroups']);
		$heights[] = 0;
		sort($heights);
		
		$tableData['table'] = array('id'=>'','class'=>'inline striped');
		$tableData['thead'] = array
		(
			// display name, CSS class(es) for each column
			array('value'=>'% of Total','class'=>'sort'),
			array('value'=>'Window Height','class'=>'focus')
		);
		
		foreach ($heights as $height)
		{
			$query = "SELECT COUNT(`window_height`) AS `total` FROM `{$this->Mint->db['tblPrefix']}visit` 
						WHERE `window_height` >= $height";
			
			if ($result = $this->query($query)) 
			{
				if ($r = mysql_fetch_array($result)) 
				{
					if (!$height)
					{
						$total = ($r['total'])?$r['total']:1;
						continue;
					}
					
					if ($r['total'])
					{
						$percent = $r['total'] / $total * 100;
						
						$row = array
						(
							$this->Mint->formatPercents($percent),
							'<span>&gt;</span> '.$height
						);
						
						if (round($percent) < 5)
						{
							$row['class'] = 'insig';
						}
						
						$tableData['tbody'][] = $row;
					}
				}
			}
		}
		
		$heightHTML = $this->Mint->generateTable($tableData);
		unset($tableData);
		
		$html  = '<table cellspacing="0" class="two-columns">';
		$html .= "\r\t<tr>\r";
		$html .= "\t\t<td class=\"left\">\r";
		$html .= $widthHTML;
		$html .= "\t\t</td>";
		$html .= "\t\t<td class=\"right\">\r";
		$html .= $heightHTML;
		$html .= "\t\t</td>";
		$html .= "\r\t</tr>\r";
		$html .= "</table>\r";
		return $html;
	}
}