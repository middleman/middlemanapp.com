<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Record
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 
 
header('Content-type: text/javascript');

if ($Mint->shouldIgnore())
{
	echo '// Mint is ignoring you :P';
	exit();
}

// used to populate the $Mint->acceptsCookies property
$Mint->bakeCookie('MintAcceptsCookies', 1);

// Live Debug toggle
$live_debug = isset($_COOKIE['MintLiveDebug']);

?>var Mint = new Object();
Mint.save = function() 
{
	var now		= new Date();
	var debug	= <?php echo ($live_debug) ? 'true' : 'false'; ?>; // this is set by php 
	if (window.location.hash == '#Mint:Debug') { debug = true; };
	var path	= '<?php echo $Mint->cfg['installFull']; ?>/?record&key=<?php echo $Mint->generateKey(); ?>';
	path 		= path.replace(/^https?:/, window.location.protocol);
	
	// Loop through the different plug-ins to assemble the query string
	for (var developer in this) 
	{
		for (var plugin in this[developer]) 
		{
			if (this[developer][plugin] && this[developer][plugin].onsave) 
			{
				path += this[developer][plugin].onsave();
			};
		};
	};
	// Slap the current time on there to prevent caching on subsequent page views in a few browsers
	path += '&'+now.getTime();
	
	// Redirect to the debug page
	if (debug) { window.open(path+'&debug&errors', 'MintLiveDebug'+now.getTime()); return; };
	
	var ie = /*@cc_on!@*/0;
	if (!ie && document.getElementsByTagName && (document.createElementNS || document.createElement))
	{
		var tag = (document.createElementNS) ? document.createElementNS('http://www.w3.org/1999/xhtml', 'script') : document.createElement('script');
		tag.type = 'text/javascript';
		tag.src = path + '&serve_js';
		document.getElementsByTagName('head')[0].appendChild(tag);
	}
	else if (document.write)
	{
		document.write('<' + 'script type="text/javascript" src="' + path + '&amp;serve_js"><' + '/script>');
	};
};
<?php $Mint->javaScript(); ?>
Mint.save();