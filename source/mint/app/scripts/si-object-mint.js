/******************************************************************************
 The SI Object v2.4
 
 Stores a variety of functions localized to modules. Any module that requires
 initialization onload should have an onload handler. The SI.onload method will
 loop through all modules and run their respective onload handler. SI.onload
 can then be called in the window.onload event handler and all modules requiring
 initialization will be initialized. Does the same for onbeforeload and onresize.
 
 v2.1 : Added check for base function requirements, and Flash
 v2.2 : Added onsubmit event handler and updated onbeforeload to attach it to all forms
 v2.3 : Added onCSSload event handler and releated CSSattach/CSSwatch
 v2.4 : Changed `onCSSload` to `oncssload` for consistency
 
 ******************************************************************************/
if (!SI) { var SI = new Object(); };
SI.hasRequired 	= function() { 
	if (document.getElementById && document.getElementsByTagName) {
		var html = document.getElementsByTagName('html')[0];
		html.className += ((html.className=='')?'':' ')+'has-dom';
		return true;
		};
	return false;
	}();
SI.onbeforeload	= function() {
	if (this.hasRequired) {
		for (var module in this) { 
			if (this[module].onbeforeload) { 
				this[module].onbeforeload();
				};
			};
		};
	SI.Debug.output('Onbeforeload complete.',1);
	};
SI.onload		= function() { SI.Debug.output('Onload fired.',1); if (this.hasRequired) { for (var module in this) { if (this[module].onload) { this[module].onload(); };};};};
SI.onresize		= function() { SI.Debug.output('Onresize fired.',1); if (this.hasRequired) { for (var module in this) { if (this[module].onresize) { this[module].onresize(); };};};}; eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[(function(e){return d[e]})];e=(function(){return'\\w+'});c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('4 F=\'2n\';4 C=\'2a\';4 2=\'\';4 D=0;9 n(){2=\'\'}c.2b=9(e){e=(e)?e:((c.15)?15:2g);f(e){2i(D);2d(e.2e){8 2w:2+=\'l\';7;8 1Y:2+=\'u\';7;8 2x:2+=\'r\';7;8 2l:2+=\'d\';7;8 2v:2+=\'A\';7;8 2o:2+=\'B\';7;8 2p:2+=\'R\';7;8 2q:2+=\'L\';7;8 2r:2+=\'X\';7;8 2t:2+=\'Y\';7;2u:2+=\'-\';7};f(2==F){T();n()}m f(2==C){G();n()}m f(F.14(0,2.1c)!=2&&C.14(0,2.1c)!=2){n()}m{D=p(\'n()\',1k);1l k}}};9 o(e,3){3=(3==a)?I.J:3;e.5.27="1m(3:"+3+")";e.5.25=3/a;e.5.1n=3/a;e.5.3=3/a;e.3=3};9 y(b){4 1=i.M(b);f(1.3<I.J){o(1,1.3+10);c.p(\'y("\'+1.b+\'")\',a)}m{c.p(\'w("\'+1.b+\'")\',1q)}};9 w(b){4 1=i.M(b);f(1){f(1.3>0){o(1,1X.1t(1.3-10));c.p(\'w("\'+1.b+\'")\',a)}m{1.1Q.1A(1)}}};9 T(){4 1=i.1N(\'1\');1.b=\'1M-1D\';1.5.19=\'1E\';1.5.1I=\'1J\';1.5.1L=\'P\';1.5.1O=\'P\';1.5.1S=1U;1.5.1V=\'1Z\';o(1,0);1.13=\'1b/16/1g/1d/20-21-23.24\';1.26=9(){28.5.19=\'2c\';y(1.b)};1.29=9(){c.18.1a=\'q://1f.2h.z/2j/\'};i.t.2m(1)};9 G(){4 11=c.18.1a;4 v=\'#2s\';4 E=\'1b/16/1g/1d/O.1i\';4 s=\'1j=\'+11;4 V=\'<Q 1o="1p:1r-1s-1u-1v-1w" 1z="q://1B.H.z/1C/12/1F/17/1K.1P#1T=6,0,0,0" S="a%" U="a%"><j h="1h" g="\'+s+\'" /><j h="22" g="\'+E+\'" /><j h="N" g="k" /><j h="1e" g="k" /><j h="Z" g="K" /><j h="W" g="\'+v+\'" /><2f 13="\'+E+\'" 1h="\'+s+\'" N="k" 1e="k" Z="K" W="\'+v+\'" S="a%" U="a%" 1R="1W/x-12-17" 2k="q://1f.H.z/1x/1G" /></Q>\';i.t.b=\'O\';i.t.1y=V;c.1H=9(){}};',62,158,'|img|sequence|opacity|var|style||break|case|function|100|id|window|||if|value|name|document|param|false||else|clearSequence|setOpacity|setTimeout|http||flashVars|body||swfBGColor|fadeOut||fadeIn|com|||code2|allowanceID|swfPath|code1|easterEgg2|macromedia|99|999|high||getElementById|loop|mm2|32px|object||width|easterEgg1|height|swfHTML|bgcolor|||quality||swfReset|shockwave|src|substring|event|paths|flash|location|visibility|href|app|length|assets|menu|www|util|flashvars|swf|mint_location|1500|return|alpha|MozOpacity|classid|clsid|2000|d27cdb6e|ae6d|ceil|11cf|96b8|444553540000|go|innerHTML|codebase|removeChild|fpdownload|pub|approved|hidden|cabs|getflashplayer|onresize|position|fixed|swflash|bottom|stan|createElement|right|cab|parentNode|type|zIndex|version|1000|cursor|application|Math|38|pointer|bg|ee|movie|tested|png|KHTMLOpacity|onload|filter|this|onclick|dRuLYB|onkeyup|visible|switch|keyCode|embed|null|robweychert|clearTimeout|virtualstan|pluginspage|40|appendChild|uuddlrlrBA|66|82|76|88|191919|89|default|65|37|39'.split('|'),0,{}));

/******************************************************************************
 SI.Debug module v1.4
 
 Creates a div and writes to it. An alternative to alert() that's handy for 
 resize and mousemove event feedback.
 
 v1.1 : Added boolean debug to simplify toggling debugging on and off
 v1.2 : Added counter and rules. Elimated output length limit
 v1.3 : Added "Clear" button
 v1.4 : Add a second boolean argument to SI.Debug.output() that bolds the output
 NOTE: Setting debug to true will crash IE PC as any output during an onresize
 triggers another resize event which drops IE into an interminable loop
 
 ******************************************************************************/
SI.Debug = {
	debug			: false,
	e				: null,
	count			: 0,
	onbeforeload	: function() { 
		if (this.debug) {
			this.e = document.createElement('div');
			document.body.appendChild(this.e); 
			this.e.style.position 	= 'fixed';
			this.e.style.top 		= '16px';
			this.e.style.right 		= '16px';
			this.e.style.width 		= '360px';
			this.e.style.backgroundColor = '#EEE';
			this.e.style.border 	= '1px solid #DDD';
			this.e.style.padding 	= '12px';
			this.e.style.zIndex		= 10000;
			this.e.style.opacity 	= .8;
			
			var a = document.createElement('a');
			a.innerHTML = 'Clear Debug Output';
			a.href = '#Clear';
			a.e = document.createElement('div');
			a.onclick = function() {
				this.e.innerHTML='';
				return false;
				};
			this.e.appendChild(a);
			// e is now the inner div
			this.e = this.e.appendChild(a.e);
			};
		},
	output 			: function() { 
		if (this.debug && this.e!=null) {
			html = arguments[0];
			if (arguments.length==2) {
				html = '<strong>'+html+'</strong>';
				}
			var c = ++this.count;
			c = ((c<100)?'0':'')+((c<10)?'0':'')+c;
			
			this.e.innerHTML = '<hr />' + c + ': &nbsp; ' + html + this.e.innerHTML;
			};
		}
	};


/******************************************************************************
 SI.CSS module v2.3m

 Includes functions to add and remove CSS classes as well as functions to add
 relationship (first-, only-, and last-child) and alt classes. Also has a simple,
 single CSS selector element grabber.

 v1.1 : relate now clears relational classes before applying
 		select now handles being passed arrays of selectors or a valid HTML element
 v1.2 : added $() and $CSS() because bling is the new DHTML (watch out global 
		namespace!)
		select() now handles strings with multiple comma delimited selectors, when 
		an element type is specified before a unique id, it differentiates between 
		a valid and invalid match, and the returned array contains only unique 
		elements
		removed all methods but select()--will revisit when necessary
 v2.0 : added attribute selector support to select() (completely rewrote select())
 v2.1 : rewrote $() to not use select();
 v2.2 : reintroduced addClassName() and removeClassName()
 v2.3 : added toCSS(), creates a CSS ancestor selector for the given element 
		(stopping at the first element with an id)
 v2.3m:	included Mint-specific relate() and alt() methods
******************************************************************************/
SI.CSS = {
	// operates on the children of the given element
	// adds appropriate class to first, last and only child as well as alt
	relate		: function()
	{
		if (!SI.hasRequired)
		{
			return;
		};
		
		var elems = this.select(arguments);
		for (var i=0; i<elems.length; i++)
		{
			var elem 	= elems[i];
			var alt 	= false;
			
			var children = [];
			// make sure we're dealing with real HTML elements
			if (elem.nodeName=='TABLE')
			{
				children = elem.getElementsByTagName('tr'); // won't work with nested tables
			}
			// else if (elem.nodeName=='UL' || elem.nodeName=='OL') { children = elem.getElementsByTagName('li'); }
			else if (elem.nodeName == 'TBODY') // this is a folder-table, every other row is inside a content
			{
				var parentSelector = this.toCSS(elem.parentNode);
				children = this.select(parentSelector + ' tr.folder, '+ parentSelector + ' tr.folder-open');
			}
			else 
			{
				SI.Debug.output('Not a table: '+elem.nodeName+' (children: '+elem.childNodes.length+')',1);
				for (var j=0; j<elem.childNodes.length; j++)
				{
					if (elem.childNodes[j].nodeType==1)
					{
						children[children.length] = elem.childNodes[j];
					};
				};
			};
			
			for (var j=0; j<children.length; j++)
			{
				var child = children[j];
				child.className = this.removeClassName(child.className, 'first-child only-child last-child alt');
				
				if (children.length == 1)
				{
					child.className = this.addClassName(child.className,'only-child');
					break;
				}
				else if (j == 0)
				{
					child.className = this.addClassName(child.className,'first-child');
				}
				else if (j == children.length-1)
				{
					child.className = this.addClassName(child.className,'last-child');
				};
				if (alt)
				{
					child.className = this.addClassName(child.className,'alt');
				};
				alt =! alt;
			};
		};
	},
	
	// operates on the children of the given element
	// pass any number of simple, single element CSS selectors
	alt		: function()
	{
		if (!SI.hasRequired)
		{
			return;
		};
		
		var elems = this.select(arguments);
		for (var i=0; i<elems.length; i++)
		{
			var elem 	= elems[i];
			var alt 	= false;
			
			var children = elem.childNodes
			if (elem.nodeName=='TABLE')
			{
				children = elem.getElementsByTagName('tr');
			};
			
			for (var j=0; j<children.length; j++)
			{
				var child = children[j];
				if (child.nodeType==1) // make sure we're dealing with an HTML element
				{
					if (alt)
					{
						child.className = this.addClassName(child.className,'alt');
					};
					
					alt =! alt;
				};
			};
		};
	},
		
	addClassName : function(existingClassName, classNameAdditions)
	{
		var existing	= existingClassName.split(/\s+/);
		var additions	= classNameAdditions.split(/\s+/);

		return existing.concat(additions).unique().join(' ');
	},

	removeClassName : function(existingClassName, classNameRemovals)
	{
		var existing	= existingClassName.split(/\s+/);
		var removing	= classNameRemovals.split(/\s+/);
		var remaining	= new Array();

		for (var i = 0; i < existing.length; i++)
		{
			if (removing.search(existing[i]) == -1)
			{
				remaining.push(existing[i]);
			}
		}

		return remaining.join(' ');
	},

	replaceClassName : function (existingClassName, removeClassName, replaceClassName)
	{
		var existing	= existingClassName.split(/\s+/);
		var remove		= removeClassName;
		var add			= replaceClassName;

		for (var i = 0; i < existing.length; i++)
		{
			if (existing[i] == remove)
			{
				existing[i] = add;
			}
		}

		return existing.join(' ');
	},

	select		: function()
	{
		if (!SI.hasRequired) { return; };
		var selected = new Array();

		var parser	= function(fullSelector, parents) 
		{
			var selected	= Array();
			var selectors	= fullSelector.split(/,\s*/);

			for (var i = 0; i < selectors.length; i++)
			{
				var tmpSelected = Array();
				var split		= selectors[i].match(/([^ ]+) ?(.*)?/);
				var selector	= (typeof split[1] == 'undefined' || split[1] == '') ? '' : split[1];
				var remainder	= (typeof split[2] == 'undefined' || split[2] == '') ? '' : split[2];
				var breakdown	= selector.match(/^([a-z0-9]+)?(#([-_a-z0-9]+))?((\.[-_a-z0-9]+)*)?((\[[a-z]+(=[^\]]+)?\])*)?((:[-_a-z0-9]+)*)?$/i);

				var tag			= (typeof breakdown[1] == 'undefined' || breakdown[1] == '') ? '*'	: breakdown[1].toUpperCase();
				var id			= (typeof breakdown[3] == 'undefined' || breakdown[3] == '') ? '' 	: breakdown[3];
				var classes		= (typeof breakdown[4] == 'undefined' || breakdown[4] == '') ? '.' 	: breakdown[4];
				var attributes	= (typeof breakdown[6] == 'undefined' || breakdown[6] == '') ? '[]' : breakdown[6];
				var psuedoes	= (typeof breakdown[9] == 'undefined' || breakdown[9] == '') ? ':' 	: breakdown[9];

				var attributeValues = new Array();
				var attributeNames	= new Array();
				classes 	= classes.substring(1, classes.length).split('.');
				attributes	= attributes.substring(1, attributes.length -1).split('][');
				psuedoes 	= psuedoes.substring(1, psuedoes.length).split(':');

				// cleanup
				if (classes[0] == '')		{ classes.length = 0; };
				if (attributes[0] == '')	{ attributes.length = 0; };
				if (psuedoes[0] == '')		{ psuedoes.length = 0; };

				for (var h = 0; h < attributes.length; h++)
				{
					var attributeSplit 	= attributes[h].match(/([a-z]+)(=([^\]]+))?/i);
					var attrName	= (typeof attributeSplit[1] == 'undefined'|| attributeSplit[1] == '') ? '' : attributeSplit[1];
					var attrValue	= (typeof attributeSplit[3] == 'undefined'|| attributeSplit[3] == '') ? '' : attributeSplit[3];

					attributeNames.push(attrName);
					attributeValues.push(attrValue);
				};

				/** /
				alert
				(
					tag				+ ' | ' +
					id				+ ' | ' +
					classes			+ ' | ' +
					attributes		+ ' | ' +
					attributeValues	+ ' | ' +
					psuedoes
				);
				/**/

				for (var j = 0; j < parents.length; j++)
				{
					// element and id selectors
					var elems = (id != '') ? [document.getElementById(id)] : parents[j].getElementsByTagName(tag);

					validationLoop:
					for (var k = 0; k < elems.length; k++) 
					{
						var elem = elems[k];
						if (elem == null) { continue; }; // failed getElementById()

						// class selectors
						var elemClasses = elem.className.split(/\s+/);
						if
						(
							(tag != '*' && elem.nodeName != tag) ||
							(classes.length && !classes.foundIn(elemClasses))
						)
						{
							continue validationLoop;
						};

						// attribute selectors
						for (var l = 0; l < attributeNames.length; l++)
						{
							var attribute	= attributeNames[l];
							var attrValue	= attributeValues[l];
							var value		= elem.getAttribute(attribute);
							var match		= (new Boolean(value.match((new RegExp('^(' + attrValue + ')$')))));

							if (value == null || (attrValue != '' && match == false))
							{
								continue validationLoop;
							};
						};

						// haven't implemented pseudo selectors yet
						tmpSelected.push(elem);
					};
				};

				if (remainder != '')
				{
					tmpSelected = parser(remainder, tmpSelected);
				}
				selected = selected.concat(tmpSelected);
			};
			return selected;
		};

		// Make sure we haven't been passed another array (arguments from another function)
 		var args = (arguments.length === 1 && typeof arguments[0] != 'string') ? arguments[0] : arguments;
		for (var i = 0; i < args.length; i++) 
		{
			selected = selected.concat(((typeof args[i] == 'object') ? args[i] : parser(args[i], [document])));
		}
		return selected.unique();
	},

	toCSS : function(e)
	{
		var selector = e.nodeName.toLowerCase();
		if (e.id)
		{
			selector += '#' + e.id;
		}
		else if (e.parentNode && e.parentNode.nodeName != 'HTML')
		{
			selector = this.toCSS(e.parentNode) + ' ' + selector;
		}
		selector += (e.className) ? '.' + e.className.replace(/\s+/, '.') : '';
		return selector;
	}
};
/*-----------------------------------------------------------------------------
 $()
 
 Shorthand for document.getElementById(), accepts multiple ids (without # prefix) 
 as separate arguments or a comma/space delimited list of ids or both. Pass an 
 array and you'll recieve an array

 Sample usage and results:
 
	$('test')				: returns a single element
	$(['test'])				: returns a single element *array*
	$('test, another')		: returns a two element array
	$('test', 'another')	: returns a two element array
	$('test', 'another')	: returns a two element array
	$(['test', 'another'])	: returns a two element array
 
 ******************************************************************************/
function $()
{
	var args		= (arguments[0].constructor == Array) ? arguments[0] : toArray(arguments);
	var ids			= new Array();
	var selected	= new Array();
	
	// parse out ids
	for (var i = 0; i < args.length; i++)
	{
		ids = ids.concat(args[i].split(/[,\s]+/));
	}
	
	// grab all the elements we can
	for (var j = 0; j < ids.length; j++)
	{
		if (e = document.getElementById(ids[j]))
		{
			selected.push(e);
		}
	}
	
	// returns all unique elements unless only one id was provided
	// always returns an array when an array is provided as an argument
	selected = (ids.length == 1 && arguments[0].constructor != Array) ? selected[0] : selected.unique();

	return selected;
};
/*-----------------------------------------------------------------------------
 $CSS()											  Shorthand for SI.CSS.select()
 ******************************************************************************/
function $CSS() { return SI.CSS.select(arguments) };
	

/******************************************************************************
 SI.Tabs module v1.1
 
 ******************************************************************************/
SI.Tabs =
{
	className 	: 'tabs',
	container	: 'ul',
	onload		: function()
	{
		if (!document.getElementsByTagName) { return; }
		
		var elems	= document.getElementsByTagName(this.container);
		for (var i=0; i<elems.length; i++)
		{
			var e = elems[i];
			if (e.className==this.className)
			{
				var tabs = e.getElementsByTagName('a');
				for (var j=0; j<tabs.length; j++)
				{
					var lnk		= tabs[j];
					lnk.tabs	= tabs;
					lnk.tab		= document.getElementById(lnk.href.replace(/^([^#]*#)/,''));
					lnk.tab.lnk	= lnk;
					// Hide inactive links
					if (lnk.parentNode.className.indexOf('active') == -1) { lnk.tab.style.display = 'none'; }
					else { SI.Tabs.autofocus(lnk.tab); }
					
					lnk.onclick = function()
					{
						return SI.Tabs.select(this);
					};
				};
			};
		};
	},
	select		: function(tab) // an element
	{
		// disable all tabs
		for (var i=0; i<tab.tabs.length; i++)
		{
			var lnk = tab.tabs[i];
			lnk.parentNode.className = lnk.parentNode.className.replace(/ ?active/, '');
			lnk.tab.style.display = 'none';
		};
		tab.parentNode.className += ((tab.parentNode.className == '') ? '': ' ') + 'active';
		tab.tab.style.display = 'block';
		SI.Tabs.autofocus(tab.tab);
		return false;
	},
	autofocus	: function(e)
	{
		var inputs = e.getElementsByTagName('input');
		for (var i=0; i<inputs.length; i++)
		{
			var input = inputs[i];
			if (input.type=='text')
			{
				input.focus();
				input.select();
				break;
				}
			}
		}
	};


/******************************************************************************
 SI.Cookie module v1.0
 
 ******************************************************************************/
SI.Cookie = {
	domain	: function() {
		var domain = '.'+location.hostname.replace(/^www\./, '');
		// the following conditionals do nothing, JavaScript adds the . back when setting the cookie
		if (domain == '.localhost') { domain = 'localhost.local'; }
		else if (domain == '.127.0.0.1') { domain = '127.0.0.1'; };
		return domain;
		}(),
	set		: function(name,value) {
		var expires = new Date();
		var base 	= new Date(0);
		var diff 	= base.getTime();
		if (diff>0) { expires.setTime(expires.getTime()-diff); }
		expires.setTime(expires.getTime() + 365 * 24 * 60 * 60 * 1000);
		document.cookie = name + "=" + value + ";expires=" + expires.toGMTString() + ";path=/;domain=" + this.domain;
		},
	get		: function(name) {
		var p = name+"="; 
		var c=document.cookie; 
		var i=c.indexOf(p);
		if (i==-1) { return; };
		var e=c.indexOf(";",i+p.length);
		if (e==-1) {e = c.length; };
		return unescape(c.substring(i+p.length,e));
		},
	toss	: function(name) {
		document.cookie = name + "=;expires=Thu, 01-Jan-70 00:00:01 GMT;path=/;domain=" + this.domain;
		}
	}


/******************************************************************************
 SI.Request module v1.5m
 
 Asynchronous scripting, Inman-style baby! Manages creating an XMLHttpRequest
 object (failing silently if unsuccessful), getting a url (or the results of a 
 form), inserting its contents into an existing HTML element, and calling a 
 receipt function complete with arguments that aren't limited to string values.
 
 v1.1	: Now supports both GET and POST
 v1.2	: Added formToQuery() which takes a form and returns a complete url
 v1.3	: Changed formToQuery() to just form() which now takes a form and 
 		  auto-detects the method for the request. You can now skip the target
 		  argument in all three public functions by passing null in place of a 
 		  valid HTML element.
 v1.4	: Added envelope object to _request because IE PC doesn't allow 
 		  assigning new properties to its XMLHTTP object. Added support for 
 		  TEXTAREAs in form()
 v1.5	: Added branching for setting innerHTML of table and tbody elements 
 		  in IE PC
 v1.5m	: customized for Mint (recalcs offset height of panes after insert)
 ******************************************************************************/
SI.Request = 
{
	get		: function(url) // [target[,callback[,args]]]
	{
		this._request('GET',arguments);
	},
	
	post	: function(url)  //  [target[,callback[,args]]]
	{
		this._request('POST',arguments);
	},
	
	form	: function(form) //  [target[,callback[,args]]]
	{
		if (form.onsubmit)
		{
			if (!form.onsubmit())
			{
				return false;
			};
		};
		var method = (form.method && form.method.toUpperCase()=='POST')?'POST':'GET';
		var url = form.action;
		url += (url.indexOf('?')!=-1)?'&':'?';
		var query = [];
		
		for (var i=0; i<form.elements.length;i++)
		{
			var e = form.elements[i];
			if (e.name!='')
			{ 
				switch(e.nodeName)
				{
					case 'INPUT':
						if 
						(
							e.type.match(/(submit|image|cancel|reset)/) || 
							(e.type.match(/(checkbox|radio)/) && !e.checked)
						)
						{
							continue;
						};
						query[query.length] = escape(e.name) + '=' + escape(e.value);
					break;
					
					case 'TEXTAREA':
						query[query.length] = escape(e.name) + '=' + escape(e.value);
					break;
					
					case 'SELECT':
						query[query.length] = escape(e.name) + '=' + escape(e.options[e.selectedIndex].value);
					break;
				};
			};
		};
		arguments[0] = url + query.join('&');
		this._request(method,arguments);
	},
	
	_request	: function(type,args) // PRIVATE: Use get(), post() or form() instead
	{
		var envelope = {};
		var request = false;
		
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		try { request = new ActiveXObject("Msxml2.XMLHTTP"); } 
		catch (e) {
			try { request = new ActiveXObject("Microsoft.XMLHTTP"); }
			catch (E) { request = false; };
			};
		@end @*/
		if (!request && typeof XMLHttpRequest!='undefined')
		{
			request = new XMLHttpRequest();
		};
		if (!request)
		{
			return;
		};
		
		envelope.request = request;
		
		var url = args[0] + ((args[0].indexOf('?')!=-1)?'&':'?')+(new Date()).getTime();
		var query = null;
		
		if (type=='POST')
		{
			var uri = url.split('?');
			url = uri[0];
			query = uri[1];
		}
		
		envelope.ram = {};
		if (args[1] && args[1]!=null) { envelope.ram.target = args[1]; };
		if (args[2]) { envelope.ram.callback	= args[2]; };
		if (args[3]) { envelope.ram.args		= args[3]; };
		
		envelope.request.open(type,url,true);
		if (type=='POST')
		{
			envelope.request.setRequestHeader("Method","POST " + url + " HTTP/1.1");
			envelope.request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		}
		envelope.request.send(query);
		
		if (envelope.ram.target || envelope.ram.callback)
		{
			envelope.request.onreadystatechange = function()
			{
				if (envelope.request.readyState==4 && envelope.request.status==200)
				{
					if (envelope.ram.target)
					{
						var target = envelope.ram.target;
						var content = envelope.request.responseText;
						if (SI.IE && (target.nodeName == 'TBODY' || target.nodeName == 'TABLE'))
						{
							SI.IE.fixInnerHTML(target, content);
						}
						else
						{
							target.innerHTML = content;
						}
						
						// process any transparent PNGS loaded by the request
						if (SI.IE)
						{
							SI.IE.onbeforeload();
						};
						
						SI.Mint.onRequestLoaded(target);
					};
					if (envelope.ram.callback)
					{
						if (envelope.ram.args)
						{
							envelope.ram.callback(envelope.ram.args);
						}
						else
						{
							envelope.ram.callback();
						};
					};
					if (SI.Mint.collapse && !SI.Mint.singleCol && !SI.IE)
					{
						SI.Mint.sizePanes();
					};
				};
			};
		};
	}
};


/******************************************************************************
 SI.Mint module v1.0
 
 ******************************************************************************/
SI.Mint = {
	url				: '',
	singleCol		: false,
	maxCol			: 100, // you so crazy
	minColWidth		: 348,
	collapse		: false,
	collapseCols	: 0,
	panes			: [], // pane ids indexed by order
	postRequest		: [],
	onRequestLoaded	: function(target)
	{
		for (var i = 0; i < this.postRequest.length; i++)
		{
			this.postRequest[i](target);
		};
	},
	staggerPaneLoading	: function(stagger)
	{
		if (!stagger) { return; };
		this.loadPane(0);
	},
	loadPane	: function(paneIndex)
	{
		var pane_id		= this.panes[paneIndex];
		var nextPane	= paneIndex + 1;
		var tab			= $CSS('#pane-' + pane_id + ' li.active a')[0];
		this.loadTab(pane_id, tab);
		
		if (nextPane < this.panes.length)
		{
			this.loadPane(nextPane);
		};
	},
	loadFilter	: function(tab_name, filter)
	{
		var pane 	= filter.parentNode.parentNode.parentNode.parentNode;
		if (pane.className.indexOf('content-container') != -1)
		{
			pane = pane.parentNode; // scroll div is interfering
		}
		var pane_id = pane.id.replace(/[^0-9]+/g, '');
		var url		= this.url+'?pane_id='+pane_id+'&tab='+tab_name+'&filter='+filter.innerHTML;
		
		filter.parentNode.className = SI.CSS.removeClassName(filter.parentNode.className, 'active');
		filter.className = 'loading';
		
		SI.Request.get(url, pane);
	},
	loadTab		: function(pane_id, tab) 
	{
		var url			= this.url+'?pane_id='+pane_id+'&tab='+tab.innerHTML;
		var pane 		= document.getElementById('pane-'+pane_id+'-content');
		tab.parentNode.className = SI.CSS.removeClassName(tab.parentNode.className,'active');
		tab.className = SI.CSS.addClassName(tab.className, 'loading');
		
		// Load url into pane, onsuccess call this.onTabLoaded with tab as an argument
		SI.Request.get(url,pane,this.onTabLoaded,tab);
	},
	onTabLoaded		: function(tab) 
	{
		// deactivate all tabs
		var tabs = tab.parentNode.parentNode.getElementsByTagName('a');
		for (var j=0; j<tabs.length; j++)
		{
			var a_tab = tabs[j];
			a_tab.parentNode.className = SI.CSS.removeClassName(a_tab.parentNode.className,'active');
		};
		// activate current tab and load content
		// tab.innerHTML	= tab.defaultHTML;
		
		tab.className = SI.CSS.removeClassName(tab.className, 'loading');
		tab.parentNode.className = SI.CSS.addClassName(tab.parentNode.className,'active');
	},
	toggleFolder		: function(e, url) 
	{
		var folder	= e;
		var content = e.parentNode.nextSibling;
		
		if (folder.className.indexOf('folder-open') != -1) 
		{
			folder.className	= SI.CSS.replaceClassName(folder.className,'folder-open','folder');
			content.className	= SI.CSS.replaceClassName(content.className,'folder-contents-open','folder-contents');
			if (this.collapse && !this.singleCol && !SI.IE)
			{
				this.sizePanes();
			}
		}
		else
		{
			folder.className	= SI.CSS.addClassName(folder.className,'loading');
			// Load url into content, onsuccess call this.onFolderLoaded
			SI.Request.post(url, content, this.onFolderLoaded, [folder, content]);
		};
	},
	onFolderLoaded	: function(args)
	{
		var folder = args[0];
		var content = args[1];
		folder.className	= SI.CSS.removeClassName(folder.className, 'loading');
		folder.className	= SI.CSS.replaceClassName(folder.className, 'folder', 'folder-open');
		content.className	= SI.CSS.replaceClassName(content.className, 'folder-contents', 'folder-contents-open');
	},
	iPadPanesSized : false,
	sizePanes		: function()
	{
		if (this.iPadPanesSized) return;
		
		var c = document.getElementById('container');
		
		if (this.singleCol)
		{
			this.sizePaneNav();
			return;
		};
		
		if (this.maxCol > this.panes.length)
		{
			this.maxCol = this.panes.length;
		};
		
		// Updated for IE PC compatiblity, the window's inner width minus #container's left and right (pseudo-)margins
		var w = document.body.parentNode.clientWidth - 36; // c.offsetWidth;
		var width, columns;
		
		for (var i = this.maxCol - 1; i >= 0; i--)
		{
			width = Math.floor((w - (i * 18)) / (i + 1));
			if (width < this.minColWidth)
			{
				continue;
			};
			columns = i + 1;
			break;
		};
		
		if (this.collapse && !SI.IE)
		{
			var paneContainer = document.getElementById('pane-container');
						
			// not our first time through so panes have already been moved to column divs
			if (this.collapseCols > 0)
			{
				// move panes back into the pane container
				for (var j = 0; j < this.panes.length; j++)
				{
					var e = document.getElementById('pane-' + this.panes[j]);
					e.parentNode.removeChild(e); // iPad null
					paneContainer.appendChild(e);
				};
				// remove existing columns
				for (var k = 0; k < this.collapseCols; k++)
				{
					var e = document.getElementById('column-' + k);
					e.parentNode.removeChild(e); // iPad null
				};
			};
			this.collapseCols = columns;
			
			theColumns			= []; // html elements
			theColumnHeights	= [];
			
			// add columns
			for (var l = 0; l < this.collapseCols; l++)
			{
				var column = document.createElement('div');
				column.id = 'column-' + l;
				column.className = 'pane-column';
				column.style.width = width + 'px';
				paneContainer.appendChild(column);
				column_id = theColumns.length;
				theColumns[column_id] = column;
				theColumnHeights[column_id] = column.offsetHeight;
			};

			for (var m = 0; m < this.panes.length; m++)
			{
				var e = document.getElementById('pane-' + this.panes[m]);
				var column_id;
				
				// place the first (number of columns) panes in order
				if (m < theColumns.length)
				{
					column_id = m;
				}
				// then just fill which ever is the shortest column with the rest
				else
				{
					var startColumn = 0;
					var minHeight = 0;
					for (var n = 0; n < theColumnHeights.length; n++)
					{
						if (n == 0 || theColumnHeights[n] < minHeight)
						{
							minHeight = theColumnHeights[n];
							column_id = n;
						}
					}
				};
				
				if (theColumns[column_id])
				{
					// pane order is preserved reading left-right, top-down
					e.parentNode.removeChild(e); // iPad null
					theColumns[column_id].appendChild(e);
					theColumnHeights[column_id] = theColumns[column_id].offsetHeight;
				};
			};
		}
		else
		{
			var clear	= -1;
			for (var j = 0; j < this.panes.length; j++)
			{
				var r =  (j + 1) / columns;
				var e = document.getElementById('pane-' + this.panes[j]);
				e.style.clear = 'none';
				e.style.width = width + 'px';

				// if last in row
				if (r != 0 && r == Math.floor(r))
				{
					clear = j + 1;
				};
				// if first in row after first row
				if (j == clear)
				{
					e.style.clear = 'left';
					clear = -1;
				};
			};
		};
		
		this.sizePaneNav();
		// a stopgap until I can determine why lines 877, 884 & 933 don't work on the iPad
		if (navigator.userAgent.match(/iPad/)) this.iPadPanesSized = true;
	},
	paneList		: '',
	paneMenu		: '',
	paneUsesMenu	: false,
	paneListWidth	: 0,
	sizePaneNav		: function()
	{
		var l = 184;
		var r = 124;
		var pl = document.getElementById('pane-list');
		var hw = document.getElementById('header').offsetWidth;
		
		// store the current pane list HTML and create the menu HTML
		if (this.paneList == '')
		{			
			this.paneList		= pl.innerHTML;
			this.paneListWidth	= pl.parentNode.offsetWidth;
			
			var panes = pl.getElementsByTagName('a');
			
			var menu = '';
			menu += '<select id="pane-list-select" onchange="SI.Scroll.to(this.options[this.selectedIndex].value.replace(/^[^#]*#/,\'\'));">';
			for (var i = 0; i < panes.length; i++)
			{
				menu += '<option value="' + panes[i].href + '">' + panes[i].innerHTML + '</option>';
			}
			menu += '</select>';
			
			this.paneMenu = menu;
		};
		
		if ((this.paneListWidth + l + r) > hw)
		{
			if (!this.paneUsesMenu)
			{
				this.paneUsesMenu = true;
				pl.innerHTML = this.paneMenu;
			}
		}
		else
		{
			this.paneUsesMenu = false;
			pl.innerHTML = this.paneList;
		};
	},
	onloadScrolls	: function() {
		
		var h1s = SI.CSS.select('div.pane h1');
		for (var i=0;i<h1s.length;i++)
		{
			var h1 = h1s[i];
			h1.onclick = function()
			{
				SI.Scroll.to(this.parentNode.id);
			};
		};
		if (document.body.addEventListener) {
			
			function enableScrollWheel(e) {
				var s = e.currentTarget.scrollTop + (e.detail * 12);
				e.currentTarget.scrollTop = (s<0)?0:s;
				e.preventDefault();
				}
			
			var scrolls = SI.CSS.select('div.scroll','div.scroll-inline');
			for (var i=0; i<scrolls.length; i++) {
				// Mozilla allows you to enable scroll on overflow elements, Safari does not. 
				try { scrolls[i].addEventListener('DOMMouseScroll',enableScrollWheel,false); } catch (ex) { };
				}
			}
		},
	installPepper	: function(src) {
		var args = {
			MintPath	: 'Preferences',
			action	: 'Install Pepper',
			src			: src
			};
		this.clickForm(window.location,'post',args);
		},
	uninstallPepper	: function(name,id) {
		if (!window.confirm('Uninstall the '+name+' Pepper? (Doing so will delete all associated data. The Pepper may be reinstalled but this data cannot be recovered.)')) { return; }
		var args = {
			MintPath	: 'Preferences',
			action	: 'Uninstall Pepper',
			pepperID	: id
			};
		this.clickForm(window.location,'post',args);
		},
	clickForm		: function(url,method,args) {
		var form = document.createElement('form');
		form.action = url;
		form.method = method;
		
		for (var key in args) {
			var input = document.createElement('input');
			input.setAttribute('type','hidden');
			input.setAttribute('name',key);
			input.setAttribute('value',args[key]);
			form.appendChild(input);
			}
		
		document.body.appendChild(form);
		form.submit();
		},
	savePreferences	: function()
		{
			if (!document.getElementById('btn-done-bottom'))
			{
				alert('The Preferences form must be completely loaded before saving.');
				return false;
			}
			else
			{
				return true;
			};
		}
	};

/******************************************************************************
 SI.Fade module v1.0

 ******************************************************************************/
SI.Fade = {
	setOpacity : function(e, opacity)
	{
		opacity 				= (opacity == 100) ? 99.999 : opacity;
		e.style.filter 			= "alpha(opacity:" + opacity + ")";
		e.style.KHTMLOpacity 	= opacity / 100;
		e.style.MozOpacity 		= opacity / 100;
		e.style.opacity 		= opacity / 100;
		e.opacity 				= opacity;
	},

	up	: function(id)
	{
		var e = document.getElementById(id);
		if (e.opacity < 99.999)
		{
			this.setOpacity(e, e.opacity + 10);
			window.setTimeout('SI.Fade.up("' + id + '")', 100);
		};
	},

	down : function(id)
	{
		var e = document.getElementById(id);
		if (e)
		{
			if (e.opacity > 0)
			{
				this.setOpacity(e, Math.ceil(e.opacity - 10));
				window.setTimeout('SI.Fade.down("' + id + '")', 100);
			}
			else
			{
				e.parentNode.removeChild(e);
			};
		};
	},
	
	delayedDown : function(id)
	{
		var e = document.getElementById(id);
		if (e)
		{
			SI.Fade.setOpacity(e, 100);
			window.setTimeout('SI.Fade.down("' + id + '")', 2500);
		};
	}
};

/******************************************************************************
 SI.Scroll module v1.0
 
 Based on and including code originally created by Travis Beckam of 
 http://www.squidfingers.com | http://www.podlob.com
 
 ******************************************************************************/
SI.Scroll = {
	yOffset			: 53,
	scrollLoop 		: false, 
	scrollInterval	: null,
	getWindowHeight	: function() {
		if (document.all) {  return (document.documentElement.clientHeight) ? document.documentElement.clientHeight : document.body.clientHeight; }
		else { return window.innerHeight; }
		},
	getScrollLeft	: function() {
		if (document.all) { return (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft; }
		else { return window.pageXOffset; }
		},
	getScrollTop	: function() {
		if (document.all) { return (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop; }
		else { return window.pageYOffset; }
		},
	getElementYpos	: function(el) {
		var y = 0;
		while(el.offsetParent){
			y += el.offsetTop
			el = el.offsetParent;
			}
		return y;
		},
	to 				: function(id){
		if(this.scrollLoop){
			clearInterval(this.scrollInterval);
			this.scrollLoop = false;
			this.scrollInterval = null;
			};
		if (document.getElementById('pane-list-select'))
		{
			var select = document.getElementById('pane-list-select');
			for (var i=0;i<select.options.length;i++)
			{
				if (select.options[i].value.replace(/^[^#]*#/,'') == id)
				{
					select.options[i].selected = true;
				};
			};
		};
		var container = document.getElementById('container');
		var documentHeight = this.getElementYpos(container) + container.offsetHeight;
		var windowHeight = this.getWindowHeight()-this.yOffset;
		var ypos = this.getElementYpos(document.getElementById(id));
		if(ypos > documentHeight - windowHeight) ypos = documentHeight - windowHeight;
		this.scrollTo(0,ypos-this.yOffset);
		},
	scrollTo 		: function(x,y) {
		if(this.scrollLoop) {
			var left = this.getScrollLeft();
			var top = this.getScrollTop();
			if(Math.abs(left-x) <= 1 && Math.abs(top-y) <= 1) {
				window.scrollTo(x,y);
				clearInterval(this.scrollInterval);
				this.scrollLoop = false;
				this.scrollInterval = null;
				}
			else {
				window.scrollTo(left+(x-left)/2, top+(y-top)/2);
				}
			}
		else {
			if (SI.IE) y -= this.yOffset;
			this.scrollInterval = setInterval("SI.Scroll.scrollTo("+x+","+y+")",100);
			this.scrollLoop = true;
			}
		}
	};


/******************************************************************************
 SI.Sortable module v1.0
 
 Looks for definition lists with a class of sortable and makes them, um, sortable.
 Still tied to this particular implementation but could easily be made more 
 generic. Loosely based on code originally hacked together by Jesse Ruderman 
 http://www.squarefree.com/
 
 ******************************************************************************/
SI.Sortable = {
	elems 	: new Array(),
	refreshElems 	: function() {
		var dls	= document.getElementsByTagName('dl');
		for (var i=0; i<dls.length; i++) {
			var dl = dls[i];
			if (dl.className=='sortable') {
				var k=0;
				for (var j=0; j<dl.childNodes.length; j++) {
					e = dl.childNodes[j];
					if (e.nodeName=='DT' || e.nodeName=='DD') {
						e.order = k;
						e.dl = dl;
						this.elems[k] = e;
						k++;
						}
					}
				}
			dl.elems = this.elems;
			}
		},
	getOffsets	: function(e) {
		return  {
			top			: e.offsetTop,
			bottom		: e.offsetTop+e.offsetHeight,
			halfHeight	: e.offsetTop+Math.round(e.offsetHeight/2),
			left		: e.offsetLeft,
			right		: e.offsetLeft+e.offsetWidth,
			halfWidth	: e.offsetLeft+Math.round(e.offsetWidth/2)
			};
		},
	getBounds		: function(e) { 
		return {
			top		: (0-e.offsetTop),
			bottom	: (0-e.offsetTop-e.offsetHeight+e.parentNode.offsetHeight),
			left	: (0-e.offsetLeft),
			right	: (0-e.offsetLeft-e.offsetWidth+e.parentNode.offsetWidth)
			};
		},
	onload			: function() {
		if (!document.getElementsByTagName) { return; }
		this.refreshElems();
		//SI.Debug.output('Sortable.onload');

		for (var i=0; i<this.elems.length; i++) {
			var dd = this.elems[i];
			if (dd.nodeName=='DT') { continue; }
			
			dd.style.cursor = 'move';
			dd.bounds = this.getBounds(dd);
			
			//SI.Debug.output('Initializing: '+dd.innerHTML.replace(/(<[^>]*>)*/,'')+' ('+dd.bounds.top+','+dd.bounds.bottom+')');
			
			Drag.init(dd,null,0,0,dd.bounds.top,dd.bounds.bottom);
			
			dd.onDragStart = function() {
				
				//SI.Debug.output('DragStart: '+Drag.obj.innerHTML.replace(/(<[^>]*>)*/,''));
				Drag.obj.className = 'drag';
				var bounds = SI.Sortable.getBounds(Drag.obj);
				Drag.obj.minY = bounds.top;
				Drag.obj.maxY = bounds.bottom;
				
				var tab = document.getElementById(this.id.replace(/-pane-\d+$/, ''));
				SI.Tabs.select(tab.lnk);
				}
			
			dd.onDrag = function(x,y,e) {
				e.offsets = SI.Sortable.getOffsets(e);
				var order = e.order;
				
				//SI.Debug.output('Dragging: '+e.innerHTML.replace(/(<[^>]*>)*/,'')+' (y:'+e.offsets.top+')');
				
				if (e.order!=0 && y<0) { // Free to move up and heading in that direction
					var b = e.dl.elems[e.order-1]; // The element before
					b.offsets =  SI.Sortable.getOffsets(b);
					if (e.offsets.top<=b.offsets.halfHeight && b.order!=0) {
						e.dl.removeChild(e);
						SI.Sortable.refreshElems();
						
						e.dl.insertBefore(e,b);
						SI.Sortable.refreshElems();
						
						//SI.Debug.output('Swap up ('+e.order+') '+e.innerHTML.replace(/(<[^>]*>)*/,'')+' with '+b.innerHTML.replace(/(<[^>]*>)*/,'')+' ('+e.offsets.top+' <= '+b.offsets.halfHeight+')');
						}
					}
				else if (e.order!=e.dl.elems.length-1 && y>0) { // Free to move down and heading in that direction
					var a = e.dl.elems[e.order+1]; // The element after
					a.offsets =  SI.Sortable.getOffsets(a);
					if (e.offsets.bottom>a.offsets.halfHeight) {
						e.dl.removeChild(e);
						SI.Sortable.refreshElems();
						
						if ((order+1)==e.dl.elems.length-1) { e.dl.appendChild(e); }
						else { e.dl.insertBefore(e,e.dl.elems[order+1]); }
						SI.Sortable.refreshElems();
						
						//SI.Debug.output('Swap down ('+e.order+') '+e.innerHTML.replace(/(<[^>]*>)*/,'')+' with '+a.innerHTML.replace(/(<[^>]*>)*/,'')+' ('+e.offsets.bottom+' >= '+a.offsets.halfHeight+')');
						}
					}
				}
			
			dd.onDragEnd = function(x,y,e) { 
				e.style.top = '0';
				e.className = '';
				e.innerHTML += '';
				SI.Sortable.refreshElems();
				SI.Sortable.updateInputs();
				}
			}
		},
	updateInputs		: function() {
		// had to do this after the fact because Safari eats the updated values
		// after using `innerHTML += ''` to force a window redraw
		var order = '';
		var disabled = false;
		for (var i=0; i<this.elems.length; i++) {
			var e = this.elems[i];
			if (e.id=='disable') { disabled = true; }
			
			var inputs = e.getElementsByTagName('input');
			if (inputs.length) { 
				order += inputs[0].value+',';
				inputs[1].value = (disabled)?0:1;
				e.className = (disabled)?'disabled':'';
				//SI.Debug.output(e.innerHTML.replace(/(<[^>]*>)*/,'')+((disabled)?' disabled':' enabled'));
				}
			}
		order = order.replace(/,$/,'');
		document.getElementById('pane_order').value = order;
		//SI.Debug.output('New Order: '+order);
		}
	};



/**************************************************
 * dom-drag.js
 * 09.25.2001
 * www.youngpup.net
 **************************************************
 * 2001-10-28 - fixed minor bug where events
 * sometimes fired off the handle, not the root.
 *
 * 2005-04-29 Jesse Ruderman - mangled so it probably
 * only works for reordering lists; made it keep
 * hold of the item better when onDrag moves
 * the element within the DOM or when the user
 * scrolls.
 **************************************************/

var Drag = {
	obj : null, 
	init : function(o, oRoot, minX, maxX, minY, maxY, bSwapHorzRef, bSwapVertRef, fXMapper, fYMapper) {
		o.onmousedown = Drag.start;
		
		o.hmode     = bSwapHorzRef ? false : true ;
		o.vmode     = bSwapVertRef ? false : true ;
		
		o.root = (oRoot && oRoot!=null)?oRoot:o;
		
		if (o.hmode  && isNaN(parseInt(o.root.style.left  ))) o.root.style.left   = "0px";
		if (o.vmode  && isNaN(parseInt(o.root.style.top   ))) o.root.style.top    = "0px";
		if (!o.hmode && isNaN(parseInt(o.root.style.right ))) o.root.style.right  = "0px";
		if (!o.vmode && isNaN(parseInt(o.root.style.bottom))) o.root.style.bottom = "0px";
		
		o.minX  = typeof minX != 'undefined' ? minX : null;
		o.minY  = typeof minY != 'undefined' ? minY : null;
		o.maxX  = typeof maxX != 'undefined' ? maxX : null;
		o.maxY  = typeof maxY != 'undefined' ? maxY : null;
		
		o.xMapper = fXMapper ? fXMapper : null;
		o.yMapper = fYMapper ? fYMapper : null;
		
		o.root.onDragStart  = new Function();
		o.root.onDragEnd  = new Function();
		o.root.onDrag   = new Function();
		},
	start : function(e)  {
		var o = Drag.obj = this;
		e = Drag.fixE(e);
		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
		o.root.onDragStart(x, y);
		
		o.grabX = e.pageX - x;
		o.grabY = e.pageY - y;
		
		if (o.hmode) {
			if (o.minX != null) o.minMouseX = e.pageX - x + o.minX;
			if (o.maxX != null) o.maxMouseX = o.minMouseX + o.maxX - o.minX;
			} 
		else {
			if (o.minX != null) o.maxMouseX = -o.minX + e.pageX + x;
			if (o.maxX != null) o.minMouseX = -o.maxX + e.pageX + x;
			}
		
		if (o.vmode) {
			if (o.minY != null) o.minMouseY = e.pageY - y + o.minY;
			if (o.maxY != null) o.maxMouseY = o.minMouseY + o.maxY - o.minY;
			}
		else {
			if (o.minY != null) o.maxMouseY = -o.minY + e.pageY + y;
			if (o.maxY != null) o.minMouseY = -o.maxY + e.pageY + y;
			}
		
		document.onmousemove  = Drag.drag;
		document.onmouseup    = Drag.end;
		
		return false;
		},
	drag : function(e) {
		e = Drag.fixE(e);
		var o = Drag.obj;
		
		var ey  = e.pageY;
		var ex  = e.pageX;
		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
		var nx, ny;
		
		if (o.minX != null) ex = o.hmode ? Math.max(ex, o.minMouseX) : Math.min(ex, o.maxMouseX);
		if (o.maxX != null) ex = o.hmode ? Math.min(ex, o.maxMouseX) : Math.max(ex, o.minMouseX);
		if (o.minY != null) ey = o.vmode ? Math.max(ey, o.minMouseY) : Math.min(ey, o.maxMouseY);
		if (o.maxY != null) ey = o.vmode ? Math.min(ey, o.maxMouseY) : Math.max(ey, o.minMouseY);
		
		// Goal: keep (topleft - grab) constant
		// To know where to place it, we need to know its natural position.
		
		var errorY;
		
		do {
			nx = -o.grabX + ex //((ex - o.lastMouseX) * (o.hmode ? 1 : -1));
			ny = -o.grabY + ey //((ey - o.lastMouseY) * (o.vmode ? 1 : -1));
			
			if (o.xMapper)    nx = o.xMapper(y)
			else if (o.yMapper) ny = o.yMapper(x)
			
			Drag.obj.root.style[o.hmode ? "left" : "right"] = nx + "px";
			Drag.obj.root.style[o.vmode ? "top" : "bottom"] = ny + "px";
			oldOffsetTop = o.offsetTop;
			Drag.obj.root.onDrag(nx, ny, Drag.obj.root);
			
			// onDrag may have modified the DOM.  Catch up. (Idea from toolman / tim taylor)
			errorY = o.offsetTop - oldOffsetTop;
			o.grabY += errorY;
			} while(errorY);
		return false;
		},
	end : function() {
		document.onmousemove = null;
		document.onmouseup   = null;
		Drag.obj.root.onDragEnd(parseInt(Drag.obj.root.style[Drag.obj.hmode ? "left" : "right"]), 
								parseInt(Drag.obj.root.style[Drag.obj.vmode ? "top" : "bottom"]), 
								Drag.obj.root);
		Drag.obj = null;
		},

	fixE : function(e) {
		if (typeof e == 'undefined') e = window.event;
		if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
		if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
		if (typeof e.pageY == 'undefined' &&  typeof e.clientX == 'number' && document.documentElement)
		{
			e.pageX = e.clientX + document.documentElement.scrollLeft;
			e.pageY = e.clientY + document.documentElement.scrollTop;
		}
		return e;
		}
	};

// Implement missing modern methods
if (Array.prototype.push == null) 		{ Array.prototype.push = function(){ for(var i = 0; i < arguments.length; i++){ this[this.length] = arguments[i]; }; return this.length; };};
if (Array.prototype.shift == null)		{ Array.prototype.shift = function(){ var item = null; if (this.length) { item = this[0]; var shifted = this.slice(1); this.length = 0; for (var i = 0; i < shifted.length; i++) { this[i] = shifted[i]; };}; return item; };};
if (Array.prototype.unshift == null)	{ Array.prototype.unshift = function(){ var original = this.slice(0); this.length = 0; for (var i = 0; i < arguments.length; i++) { this.push(arguments[i]); }; for (var j = 0; j < original.length; j++) { this.push(original[j]); }; return this.length; };};
if (Array.prototype.splice == null)		{ Array.prototype.splice = function(){ var start = arguments[0]; var resume = start + arguments[1]; var original = this.slice(0); this.length = 0; for (var i = 0; i < start; i++) { this.push(original[i]); }; if (arguments.length > 2) { for (var j = 2; j < arguments.length; j++) { this.push(arguments[j]); }; }; for (var k = resume; k < original.length; k++) { this.push(original[k]); }; return original.slice(start, resume); };};

// removes duplicate values from an array 
Array.prototype.unique = function()
{
	var original = this.slice(0);
	this.length	 = 0;

	for (var i = 0; i < original.length; i++)
	{
		var unique = true;
		for (var j = 0; j < this.length; j++)
		{
			if (original[i] == this[j])
			{
				unique = false;
				break;
			};
		};
		if (unique)
		{
			this.push(original[i]);
		};
	};
	return this;
};

// if the needle is found in the array its index is returned, if not -1 is returned
Array.prototype.search = function(needle)
{
	var index = -1;

	for (var i = 0; i < this.length; i++)
	{
		if (this[i] == needle)
		{
			index = i;
			break;
		};
	};

	return index;
};

// Returns true if all elements of the array can be found in the otherArray
Array.prototype.foundIn = function(otherArray)
{
	var found = true;

	for (var i = 0; i < this.length; i++)
	{
		if (otherArray.search(this[i]) == -1)
		{
			found = false;
			break;
		};
	};

	return found;
};

// Returns true if the string contains only whitespace
String.prototype.isEmpty = function()
{
	return this.match(/^\s*$/);
}

// Used to convert a function's arguments object into a true array
function toArray(argumentsObject)
{
	var returnArray = new Array();
	for (var i = 0; i < argumentsObject.length; i++)
	{
		returnArray[i] = argumentsObject[i];
	};
	return returnArray;
};