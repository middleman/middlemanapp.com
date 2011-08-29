<?php
/******************************************************************************
 Search Engines
 
 Developer		: Shaun Inman
 Plug-in Name	: Default Pepper
 
 http://www.shauninman.com/
 ******************************************************************************/
$SI_SearchEngines[] = array
(
	'name'			=> 'Google',
	'url'			=> 'http://www.google.com/search',
	'domain'		=> 'google',
	'query'			=> 'q|as_q',
	'images'		=> '/images',
	'image_results'	=> '/images?q='
);
$SI_SearchEngines[] = array
(
	'name'			=> 'Yahoo!',
	'url'			=> 'http://search.yahoo.com/search',
	'domain'		=> 'yahoo',
	'query'			=> 'p',
	'images'		=> 'images.search.yahoo',
	'image_results'	=> '/search/images?p='
);
$SI_SearchEngines[] = array
(
	'name'			=> 'MSN',
	'url'			=> 'http://search.msn.com/results.aspx',
	'domain'		=> 'search.msn',
	'query'			=> 'q',
	'images'		=> '/images/',
	'image_results'	=> '/images/results.aspx?q='
);
$SI_SearchEngines[] = array
(
	'name'          => 'Bing',
	'url'           => 'http://www.bing.com/search',
	'domain'        => 'bing',
	'query'         => 'q',
	'images'		=> '/images/',
	'image_results'	=> '/images/search?q='
);
$SI_SearchEngines[] = array
(
	'name'			=> 'AlltheWeb',
	'url'			=> 'http://www.alltheweb.com/search',
	'domain'		=> 'alltheweb',
	'query'			=> 'q',
	'images'		=> '/search?cat=img',
	'image_results'	=> '/search?cat=img&q='
);
$SI_SearchEngines[] = array
(
	'name'			=> 'AOL',
	'url'			=> 'http://search.aol.com/aolcom/search',
	'domain'		=> 'search.aol',
	'query'			=> 'query',
	'images'		=> '/aolcom/imageDetails',
	'image_results'	=> '/aolcom/image?query='
);
$SI_SearchEngines[] = array
(
	'name'			=> 'Ask Jeeves',
	'url'			=> 'http://web.ask.com/web',
	'domain'		=> 'ask',
	'query'			=> 'q',
	'images'		=> 'images.ask',
	'image_results'	=> '/pictures?q='
);          	
$SI_SearchEngines[] = array
(
	'name'			=> 'AltaVista',
	'url'			=> 'http://www.altavista.com/web/results',
	'domain'		=> 'altavista',
	'query'			=> 'q',
	'images'		=> '/image/detail',
	'image_results'	=> '/image/results?q='
);          	
$SI_SearchEngines[] = array
(
	'name'			=> 'BBC',
	'url'			=> 'http://www.bbc.co.uk/cgi-bin/search/results.pl',
	'domain'		=> 'bbc',
	'query'			=> 'q'
);          	
$SI_SearchEngines[] = array
(
	'name'			=> 'HotBot',
	'url'			=> 'http://www.hotbot.com/',
	'domain'		=> 'hotbot',
	'query'			=> 'query'
);          	
$SI_SearchEngines[] = array
(
	'name'			=> 'Lycos',
	'url'			=> 'http://search.lycos.com/',
	'domain'		=> 'search.lycos',
	'query'			=> 'query'
);
$SI_SearchEngines[] = array
(
	'name'			=> 'Blingo',
	'url'			=> 'http://www.blingo.com/search',
	'domain'		=> 'blingo',
	'query'			=> 'q',
	'images'		=> '/images',
	'image_results'	=> '/images?q='
);
$SI_SearchEngines[] = array
(
	'name'          => 'Live.com',
	'url'           => 'http://search.live.com/results.aspx',
	'domain'        => 'search.live',
	'query'         => 'q'
);
$SI_SearchEngines[] = array
(
	'name'          => 'Search.com',
	'url'           => 'http://www.search.com/search',
	'domain'        => 'www.search',
	'query'         => 'q'
);