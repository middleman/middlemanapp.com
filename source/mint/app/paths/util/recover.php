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

function saferUnserialize($serialized)
{
	$serialized		= stripslashes($serialized);
	$unserialized	= @unserialize($serialized);
	if ($unserialized === false)
	{
		$serialized		= preg_replace('/s:\d+:"([^"]*)";/e', "'s:'.strlen('\\1').':\"\\1\";'", $serialized);
		$unserialized	= @unserialize(stripslashes($serialized));
	}
	if ($unserialized === false) // still failing? attempt to salvage what we can
	{
		$tmp	= preg_replace('/^a:\d+:\{/', '', $serialized);
		$data	= repairSerializedArray($tmp); // operates on the actual argument
		
		$unserialized = $data;
	}
	
	return $unserialized;
}

function repairSerializedArray(&$broken)
{
	// array and string length can be ignored
	// a:0:{}
	// s:4:"four";
	// i:1;
	// b:0; true/false
	// N; null
	$data		= array();
	$index		= null;
	$last_len 	= null;
	$len		= strlen($broken);
	$i			= 0;
	
	while(strlen($broken))
	{
		$i++;
		if ($i > $len)
		{
			break;
		}
		
		if (substr($broken, 0, 1) == '}') // end of array
		{
			$broken = substr($broken, 1);
			return $data;
		}
		else
		{
			$bite = substr($broken, 0, 2);
			switch($bite)
			{	
				case 's:': // key or value
					$re = '/^s:\d+:"([^\"]+)";/';
					if (preg_match($re, $broken, $m))
					{
						if ($index === null)
						{
							$index = $m[1];
						}
						else
						{
							$data[$index] = $m[1];
							$index = null;
						}
						$broken = preg_replace($re, '', $broken);
					}
				break;
				
				case 'i:': // key or value
					$re = '/^i:(\d+);/';
					if (preg_match($re, $broken, $m))
					{
						if ($index === null)
						{
							$index = (int) $m[1];
						}
						else
						{
							$data[$index] = (int) $m[1];
							$index = null;
						}
						$broken = preg_replace($re, '', $broken);
					}
				break;
				
				case 'b:': // value only
					$re = '/^b:[01];/';
					if (preg_match($re, $broken, $m))
					{
						$data[$index] = (bool) $m[1];
						$index = null;
						$broken = preg_replace($re, '', $broken);
					}
				break;
				
				case 'a:': // value only
					$re = '/^a:\d+:\{/';
					if (preg_match($re, $broken, $m))
					{
						$broken			= preg_replace('/^a:\d+:\{/', '', $broken);
						$data[$index]	= repairSerializedArray($broken);
						$index = null;
					}
				break;
				
				case 'N;': // value only
					$broken = substr($broken, 2);
					$data[$index]	= null;
					$index = null;
				break;
			}
		}
	}
	
	return $data;
}

if ($Mint->errors['fatal'] && strpos($Mint->errors['list'][0], "Pepper data may be damaged beyond repair"))
{
	$mint_says = "I don't feel so well.&nbsp;Gulp!";
	
	$report = "<p>Mint's Pepper data may be damaged beyond repair. Let's see what we can salvage&#8230;</p>";
	$report .= '<ul>';
	
	$Mint->errors['fatal'] = 0;
	$Mint->loadPepper();

	$query = "SELECT `data` FROM `{$Mint->db['tblPrefix']}_config` LIMIT 0,1";
	if ($result = $Mint->query($query))
	{
		if ($load = mysql_fetch_assoc($result))
		{
			$data	= saferUnserialize($load['data']);

			foreach($Mint->data as $key => $value)
			{
				if (isset($data[$key]))
				{
					$Mint->data[$key] = $data[$key];
					$report .= '<li>Recovered data from the '.$Mint->pepper[$key]->info['pepperName'].' Pepper!</li>';
				}
				else
				{
					$defaults = get_class_vars($Mint->cfg['pepperShaker'][$key]['class']);
					$Mint->data[$key] = $defaults['data'];
					$report .= '<li class="boo">Reverting to default data for the '.$Mint->pepper[$key]->info['pepperName'].' Pepper.</li>';
				}
			}
			$Mint->_save();
		}
	}
	$report .= '</ul>';
	
}
else
{
	$mint_says = 'I feel fine! No reason to be here.';
	$report = '';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Mint: Recover Mode</title>
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

.boo
{
	color: #A11;
}

/* ]]> */
</style>
</head>
<body>
<div>
<h1>Mint says, &#8220;<?php echo $mint_says; ?>&#8221;</h1>

<?php echo $report; ?>

<a href=".">Back to Mint.</a>

</div>
</body>
</html>