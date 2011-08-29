<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Utility Path
 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file 

$html ='';

if (isset($_POST['action']) && $_POST['action'] == 'import-visits')
{
	$data = $Mint->safeUnserialize($_POST['data']);
	$existing_visits = $data[0]['visits'];
	$visits = $Mint->data[0]['visits'];
	
	foreach ($existing_visits as $span => $spanData)
	{
		foreach ($spanData as $stamp => $stampData)
		{
			// visits don't exist for this timespan and timestamp
			if (!isset($visits[$span][$stamp]))
			{
				$visits[$span][$stamp] = $stampData;
			}
			
			else
			{
				$visits[$span][$stamp]['total']		+= $stampData['total'];
				$visits[$span][$stamp]['unique']	+= $stampData['unique'];
			}
		}
	}
	
	$Mint->data[0]['visits'] = $visits;
	$Mint->_save();
	$html .= '<p id="notice">Visits imported.</p>';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Mint: Import Existing Visits</title>
<style type="text/css" title="text/css" media="screen">
/* <![CDATA[ */
body
{
	position: relative;
	background-color: #FFF;
	margin: 0;
	padding: 48px 0;
	font: 76%/1.6em "Lucida Grande", Verdana, Arial, sans-serif;
	color: #333;
	text-align: center;
}

div
{
	width: 400px;
	margin: 0 auto;
	text-align: left;
}

h1
{
	font-weight: normal;
	line-height: 1.2em;
}

#notice
{
	border: 1px solid #F2F2C2;
	background-color: #FFC;
	padding: 0.6em 1.0em;
}

/* ]]> */
</style>
</head>
<body>
<div>
<h1>Mint says, &#8220;While you were out&#8230;</h1>

<p>Moved your site to another server? Already install Mint? Want to import aggregate Total/Unique Visits from a previous Mint installation on a separate database/server?</p>

<p>Copy the contents of the data column from the existing Mint _config table into the textarea below and click "Import Existing Visits". This will add the existing Visits to the aggregate Visits in this Mint install.</p>

<?php echo $html; ?>

<form method="post" action="">
	<textarea name="data" rows="12" cols="48"></textarea>
	<input type="hidden" name="action" value="import-visits" />
	<input type="submit" value="Import Existing Visits" />
</form>
</div>

<script type="text/javascript">
// <![CDATA[
function clearNotice()
{
	var notice	= document.getElementById('notice');
	if (notice)
	{
		notice.style.display = 'none';
	}
}

window.onload = function()
{
	window.setTimeout('clearNotice()', 8 * 1000);
};

// ]]>
</script>
</body>
</html>