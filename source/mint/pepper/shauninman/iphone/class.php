<?php
/******************************************************************************
 Pepper
 
 Developer		: Shaun Inman
 Plug-in Name	: iPhone
 
 http://www.shauninman.com/
 
 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file
$installPepper = "SI_iPhone";

function SI_iPhone_obStagger($buffer)
{
	return str_replace('SI.Mint.staggerPaneLoading(true);', 'SI.Mint.staggerPaneLoading(false);', SI_iPhone_obNoScroll($buffer));
}

function SI_iPhone_obNoScroll($buffer)
{
	return preg_replace('#<div class="scroll[^"]*">#', '<div>', $buffer);
}

class SI_iPhone extends Pepper
{
	var $version	= 122;
	var $info		= array
	(
		'pepperName'	=> 'iPhone',
		'pepperUrl'		=> 'http://www.haveamint.com/',
		'pepperDesc'	=> "The paneless iPhone Pepper enables single-column mode in Mint when browsing from an iPhone&mdash;leaving the default multi-column experience for the desktop. Now with custom WebClip bookmark icons! Original CSS and many subsequent tweaks contributed by Richard Herrera. Pepper by Shaun Inman.",
		'developerName'	=> 'Shaun Inman',
		'developerUrl'	=> 'http://www.shauninman.com/',
		'additionalDevelopers' => array
		(
			'Richard Herrera' => 'http://doctyper.com/'
		)
	);
	
	var $prefs 		= array
	(
		'webclip' => 'mint.png',
		'openNew' => true,
		'loadAll' => false,
	);
	
	var $isiPhone	= false;
	
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
				'explanation'	=> '<p>This Pepper requires Mint 2.03. Mint 2, a paid upgrade, is available at <a href="http://www.haveamint.com/">haveamint.com</a>.</p>'
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
		if (!isset($this->prefs['webclip']))
		{	
			$this->prefs['webclip'] = 'mint.png';
		}
		
		if (!isset($this->prefs['openNew']))
		{	
			$this->prefs['openNew'] = true;
		}
		
		if (!isset($this->prefs['loadAll']))
		{	
			$this->prefs['loadAll'] = false;
		}
	}
	
	/**************************************************************************
	 onPepperLoad()
	 **************************************************************************/
	function onPepperLoad()
	{
		$this->isiPhone = (isset($_SERVER['HTTP_USER_AGENT']) && (
			strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') ||
			strpos($_SERVER['HTTP_USER_AGENT'], 'iPod')   ||
			strpos($_SERVER['HTTP_USER_AGENT'], 'Android')
		) !== false);
		
		if ($this->isiPhone)
		{
			$openNew = ($this->prefs['openNew']) ? 'true' : 'false';
			$loadAll = ($this->prefs['loadAll']) ? 'true' : 'false';
			$iPhoneHead = <<<HTML
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<link rel="apple-touch-icon" href="pepper/shauninman/iphone/webclips/{$this->prefs['webclip']}" />
<link href="pepper/shauninman/iphone/style.css" rel="stylesheet" type="text/css" media="only screen and (max-device-width: 480px)" charset="utf-8" />
<script type="text/javascript" src="pepper/shauninman/iphone/script.js"></script>
<script type="text/javascript">
// <![CDATA[
SI.Mint.singleCol	= true;
SI.iPhone.openNew	= {$openNew};
SI.iPhone.loadAll	= {$loadAll};
// ]]>
</script>

HTML;
			// This is a hack, an abuse of a temporary variable used during the update process.
			// If you are a Pepper developer intersted in using this sort of functionality 
			// let me know so I can update the API. 
			if (!isset($this->Mint->tmp['headTags']))
			{
				$this->Mint->tmp['headTags'] = $iPhoneHead;
			}
			
			if (!$this->prefs['loadAll'])
			{
				ob_start('SI_iPhone_obStagger');
			}
			else
			{
				ob_start('SI_iPhone_obNoScroll');
			}
		}
	}
	
	/**************************************************************************
	 onDisplayPreferences() 
	 **************************************************************************/
	function onDisplayPreferences()
	{
		
		$select = <<<HTML
<select name="webclip" onchange="$('touch-webclip').src = 'pepper/shauninman/iphone/webclips/' + this.options[this.selectedIndex].value;">
HTML;
		$select .= $this->generateWebClipOptions();
		$select .= '</select>';

		$preferences['Mint WebClip Bookmark Icon'] = <<<HTML
<table class="snug">
	<tr>
		<td><span class="inline" style="position: relative; width:61px; margin-left: 0;">
				<img src="pepper/shauninman/iphone/webclip-mask.png" width="61" height="61" alt="" style="position: absolute; top:1px; left:1px;" />
				<img src="pepper/shauninman/iphone/webclips/{$this->prefs['webclip']}" width="61" height="61" id="touch-webclip" alt="" />
			</span></td>
		<td style="vertical-align: top;">
			<span style="margin-bottom: 4px">{$select}</span>
			Add custom WebClip bookmark icons to <code>pepper/shauninman/iphone/&#8629;<br/>webclips/</code>
		</td>
	</tr>
</table>
HTML;

		$openNew = ($this->prefs['openNew']) ? ' checked="checked"' : '';
		$preferences['Link Behavior'] = <<<HTML
<table>
	<tr>
		<td><label><input type="checkbox" name="openNew" value="1"{$openNew} /> Open links in new tab</label></td>
	</tr>
</table>
HTML;

		$loadAll = ($this->prefs['loadAll']) ? ' checked="checked"' : '';
		$preferences['Pane Behavior'] = <<<HTML
<table>
	<tr>
		<td><label><input type="checkbox" name="loadAll" value="1"{$loadAll} /> Load all panes automatically</label></td>
	</tr>
</table>
HTML;
		
		if ($this->isiPhone)
		{
			$preferences[''] = <<<HTML
<table><tr><td>Mint <em class="iphone">and</em> an iPhone? Talk about disposable income!</td></tr></table>
<script type="text/javascript" src="pepper/shauninman/iphone/script.js"></script>
<script type="text/javascript" language="javascript">
// <![CDATA[
SI.iPhone.updateLayout();
SI.iPhone.tidyPreferences();
setInterval(SI.iPhone.updateLayout, 400);
// ]]>
</script>

HTML;
		}
		
		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{	
		$this->prefs['webclip']	= $this->escapeSQL($_POST['webclip']);
		$this->prefs['openNew']	= (isset($_POST['openNew'])) ? true : false;
		$this->prefs['loadAll']	= (isset($_POST['loadAll'])) ? true : false;
	}
	
	/**************************************************************************
	 generateWebClipOptions()
	 
	 **************************************************************************/
	function generateWebClipOptions()
	{
		$html			= '';
		$pathToIcons	= 'pepper/shauninman/iphone/webclips/';
		
		// Check for the style diretory
		if (@is_dir($pathToIcons)) 
		{
			// Open the style directory
			if ($dirHandle = opendir($pathToIcons)) 
			{
				// Loop through style directory
				while (($iconImg = readdir($dirHandle)) !== false) 
				{
					// ignore hidden directories and files
					if
					(
						$iconImg == '.' ||
						$iconImg == '..'  || 
						$iconImg == 'CVS' || 
						$iconImg == '.svn'
					) 
					{ 
						continue; 
					}
					$html .= '<option value="'.$iconImg.'"'.(($this->prefs['webclip'] == $iconImg) ? ' selected="selected"' : '').'>'.ucwords(preg_replace('/[-_]/', ' ', preg_replace('/\.[^\.]{3,4}$/', '', $iconImg))).'</option>';
				}
				closedir($dirHandle);
			}
		}
		return $html;
	}
}