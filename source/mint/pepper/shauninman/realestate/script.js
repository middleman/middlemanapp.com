if (!Mint.SI) { Mint.SI = new Object(); }
Mint.SI.RealEstate = 
{
	onsave	: function() 
	{
		var width = -1;
		var height = -1;
		
		if (typeof window.innerWidth != "undefined")
		{
			width = window.innerWidth;
			height = window.innerHeight;
		}
		else if (document.documentElement && typeof document.documentElement.offsetWidth != "undefined" && document.documentElement.offsetWidth != 0)
		{
			width = document.documentElement.offsetWidth;
			height = document.documentElement.offsetHeight;
		}
		else if (document.body && typeof document.body.offsetWidth != "undefined")
		{
			width = d.body.offsetWidth;
			height = d.body.offsetHeight;
		};
		
		return '&window_width=' + width + '&window_height=' + height;
	}
};