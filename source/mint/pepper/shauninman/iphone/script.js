SI.iPhone = 
{
	currentWidth	: 0,
	openNew			: false,
	loadAll			: false,
	onload			: function()
	{
		window.scrollTo(0, 1);
	},
	onbeforeload	: function()
	{
		window.scrollTo(0, 1);
		this.updateLayout();
		this.applyTarget();
		
		SI.Mint.postRequest.push(SI.iPhone.onAfterUpdate);
		setInterval(SI.iPhone.updateLayout, 400);
		
		// Set up spacers so panes aren't flush to the top of the phone screen after using the jump menu navigation
		var panes = $CSS('.pane h1');
		for (var i = 0; i < panes.length; i++)
		{
			// eliminate overlap in portrait mode
			var title = panes[i];
			switch(title.innerHTML)
			{
				case 'User Agents':
					title.title		= title.innerHTML;
					title.innerHTML	= '<abbr>UAs</abbr>';
				break;
				
				case 'Trends Internal':
					title.title		= title.innerHTML;
					title.innerHTML	= '<abbr>Internal</abbr>';
				break;
				
				case 'Trends External':
					title.title		= title.innerHTML;
					title.innerHTML	= '<abbr>External</abbr>';
				break;
			};

			var pane 			= title.parentNode;
			var spacer			= document.createElement('div');
			spacer.id			= pane.id + '-spacer';
			spacer.className	= 'spacer';
			pane.parentNode.insertBefore(spacer, pane);
			
			if (!this.loadAll && document.body.className.indexOf('display') != -1)
			{
				this.collapsePane(title);
			};
		};
		
		// eliminate overlap in portrait mode
		var tabs = $CSS('ul.tabs li a');
		for (var i = 0; i < tabs.length; i++)
		{
			var tab = tabs[i];
			tab.title = tab.innerHTML;
			tab.innerHTML = tab.innerHTML.replace(/^(Past|Most|Newest) /, '<span>$1</span> ');
		};
	},
	collapsePane : function(title)
	{
		var pane = title.parentNode;
		pane.className += ' unloaded';
		var button  = document.createElement('ul');
		button.id = pane.id + '-loader';
		button.className = 'load-tab-btn tabs';
		button.innerHTML = '<li><a>Click to load pane</a></li>';
		title.parentNode.insertBefore(button, title);
	},
	onAfterUpdate : function(target)
	{
		SI.iPhone.applyTarget(target);
		SI.iPhone.graphTitles();
	},
	applyTarget : function()
	{
		if (this.openNew)
		{
			var links = (arguments.length) ? arguments[0].getElementsByTagName('a') : $CSS('div.content-container a');
			for (var i = 0; i < links.length; i++)
			{
				var link = links[i];
				if (link.href.indexOf('#') == -1) // ignore filters
				{
					link.target = '_blank';
				}
			};
		};
	},
	graphTitles		: function() // optional target argument
	{
		var targetId = (!arguments.length) ? '' : '#' + arguments[0].id + ' ';
		if (targetId != '# ') // folders don't have an id! but they also don't contain graphs!
		{
			var bars = $CSS(targetId + '.bar .unique');
			for (var i = 0; i < bars.length; i++)
			{
				var total		= bars[i].parentNode;
				total.alert		= total.title.replace(/'s? (Total)/, '\n$1') + '\n' + bars[i].title.replace(/^.+(Unique)/, '$1')
				total.onclick	= function() { alert(this.alert); };
			};
			
			var bars = $CSS(targetId + '.compare .increase', targetId + '.compare .decrease');
			for (var i = 0; i < bars.length; i++)
			{
				bars[i].onclick	= function() { alert(this.title.replace(/([^0-9]:)/, '$1\n')); };
			};
		};
	},
	updateLayout	: function() // via Joe Hewitt, who else? http://www.joehewitt.com/files/liquid1.html
	{
		if (window.innerWidth != this.currentWidth)
	    {
	        this.currentWidth = window.innerWidth;

	        var orientation = (this.currentWidth < 480) ? "portrait" : "landscape";
	        document.body.setAttribute("orientation", orientation);        
	  		
			// Safari has trouble redrawing tables when coming from display: block; table cells so force the issue
			var visits = $CSS('table.visits');
			for (var i = 0; i < visits.length; i++)
			{
				visits[i].innerHTML += '';
			}
	    };
	},
	tidyPreferences	: function()
	{
		// disable the default active tab
		$CSS('ul.tabs li.active')[0].className = '';
		
		// Add a Mint tab
		var tabs	= $CSS('ul.tabs')[0];
		tabs.innerHTML = '<li class="active"><a href="#mint-iphone">Mint</a></li>' + tabs.innerHTML;
		var mintColumn = $CSS('div.mint-column')[0];
		mintColumn.id = 'mint-iphone';
		var defaultColumn = document.getElementById('pepper-0');
		defaultColumn.parentNode.insertBefore(mintColumn, defaultColumn);
	}
};

// Override some default behaviors for the iPhone
SI.Scroll.to = function(id)
{
	location.href = '#' + id + '-spacer';
	if (event.target.nodeName == 'SELECT')
	{
		event.target.selectedIndex = 0;
		event.target.blur();
	};
};

SI.Mint.loadTab	= function(pane_id, tab) 
{
	var url			= this.url+'?pane_id='+pane_id+'&tab='+tab.title;
	var pane 		= document.getElementById('pane-'+pane_id+'-content');
	tab.parentNode.className = SI.CSS.removeClassName(tab.parentNode.className,'active');
	tab.className = SI.CSS.addClassName(tab.className, 'loading');
	
	// Load url into pane, onsuccess call this.onTabLoaded with tab as an argument
	SI.Request.get(url,pane,this.onTabLoaded,tab);
};

if (!SI.iPhone.loadAll)
{
	SI.Mint.onloadScrollsOrig = SI.Mint.onloadScrolls;
	SI.Mint.onloadScrolls = function()
	{
		this.onloadScrollsOrig();
		var h1s = SI.CSS.select('div.pane h1');
		for (var i=0;i<h1s.length;i++)
		{
			var h1 = h1s[i];
			h1.onclick = function()
			{
				var parent_class = this.parentNode.className;
				
				// if collapsed
				if (parent_class.match(/ unloaded$/))
				{
					// restore default styling
					this.parentNode.className = parent_class.replace(/ unloaded$/, '');
					this.parentNode.removeChild(document.getElementById(this.parentNode.id + '-loader'));

					// load last active tab
					var pane_id	= this.parentNode.id.replace(/pane-/, '');
					var tab		= $CSS('#pane-' + pane_id + ' li.active a')[0];
					SI.Mint.loadTab(pane_id, tab);
				}
				else
				{
					SI.iPhone.collapsePane(this);
				};
			};
		};
	};
};