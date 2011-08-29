/******************************************************************************
 IE REPAIRS
 
 ******************************************************************************/
 SI.IE = 
 {
 	onbeforeload : function()
 	{
 		var inputs = document.getElementsByTagName('input');
 		for (var i = 0; i < inputs.length; i++)
 		{
 			var input = inputs[i];
 			if (input.getAttribute('type') == 'image')
 			{
 				if (input.getAttribute('src').indexOf('.png') != -1)
 				{
 					this.filterPNGs(input);
 				};
 			};
 		};
 		
 		var imgs = document.getElementsByTagName('img');
 		for (var j = 0; j < imgs.length; j++)
 		{
 			var img = imgs[j];
 			if (img.getAttribute('src').indexOf('.png') != -1)
			{
				this.filterPNGs(img);
			};
 		};
 	},
 	
 	filterPNGs : function(e)
 	{
		e.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + e.getAttribute('src') + "',sizingMethod='scale')";
		e.setAttribute('src', 'app/images/iepc.gif');
 	},
 	
 	fixInnerHTML : function (elem, contentHTML)
	{
		// Remove all existing rows
		while (elem.rows.length > 0)
		{
			elem.deleteRow(0);
		};
		// Create temporary table
		var div = document.createElement("div");
		document.body.appendChild(div);
		div.innerHTML = '<table style="display: none">' + contentHTML + '</table>';
		
		// Copy temporary table's rows to target
		var table = div.getElementsByTagName('table')[0];
		for (var i = 0; i < table.rows.length; i++)
		{
			elem.appendChild(table.rows[i].cloneNode(true));
		};
		
		// Remove temporary elements
		document.body.removeChild(div);
 	}
 };