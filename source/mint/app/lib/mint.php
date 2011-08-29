<?php
/******************************************************************************
 Mint
  
 Copyright 2004-2011 Shaun Inman. This code cannot be redistributed without
 permission from http://www.shauninman.com/
 
 More info at: http://www.haveamint.com/
 
 ******************************************************************************
 Mint Constructor
 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file

class Mint
{
	var $version		= 219;
	var $db				= array
	(
		'server'	=> 'localhost',
		'username'	=> '',
		'password'	=> '',
		'database'	=> '',
		'tblPrefix'	=> 'mint_',
		'connected'	=> 0
	);
	var $cfg 			= array
	(
		'activationKey'	=> '',
		'siteRoot'		=> '', // unnecessary?
		'siteDisplay'	=> '',
		'siteDomains'	=> '', // comma delimited list
		'installed'		=> false,
		'installDir'	=> '',
		'installDomain'	=> '',
		'installTrim'	=> '',
		'installFull'	=> 'http://',
		'installDate'	=> 0,
		'offset' 		=> -5,
		'email'			=> '',
		'password'		=> '',
		'version'		=> 0,
		'lastOptimized'	=> 0,
		'lastChecked'	=> 0,
		'debug'			=> false,
		'mode'			=> 'default',
		'manifest'		=> array
		(
			'_config'	=> -1,
			'visit'		=> array
			(
				'id'	=> -1,
				'dt'	=> -1
			)
		),
		'moderate'		=> array
		(
			'visit'		=> -1
		),
		'panes'			=> array(),
		'pepperShaker'	=> array(),
		'pepperLookUp'	=> array(),
		'preferences'	=> array
		(
			'paneOrder'	=> array
			(
				'enabled'	=> array(),
				'disabled'	=> array()
			),
			'staggerPanes'		=> true,
			'fixHeight'			=> false,
			'singleColumn'		=> false,
			'singleColumnWidth'	=> 480,
			'collapseVert'		=> true,
			'stripeAll'			=> false,
			'expiry'			=> array
			(
				'visit'			=> 5 // weeks
			),
			'maxSize'			=> array
			(
				'visit'			=> 25, // MB, set to zero to limit based on time alone
			),
			'rows'				=> 18,
			'rssRows'			=> 36,
			'secondary'			=> 1,
			'style'				=> 'vanilla_mint',
			'ignoreIPsMint'		=> '65.55.165.*',
			'ignoredIPsLong'	=> array
			(
				array('1094165760', '1094166015') // 65.55.165.*
			)
		),
		'update'		=> array
		(
			'available'	=> 0,
			'lastPing'	=> 0,
			'dismissed' => 0
		)
	);
	var $domains	= array(); // Used to ignore mirrored domains in referrers
	var $pepper		= array();
	var $data		= array();
	var $bench		= array();
	var $errors		= array
	(
		'fatal'	=> false,
		'note'	=> '',
		'list'	=> array()
	);
	var $feedback	= array
	(
		'feedback'		=> '',
		'button'		=> '',	// optional, '', 'back', 'okay', 'done'
		'destination'	=> ''	// optional
	);
	var $queries		= array();
	var $acceptsCookies = false; // only available in the Pepper onRecord handler
	var $paranoid		= false;
	var $tmp			= array();
	
	
	/**************************************************************************
	 Mint Constructor
	 
	 Takes an array of database connection info
	 **************************************************************************/
	function Mint($args = array())
	{
		$this->logBenchmark('Mint() {');
		
		// Database configs
		$this->db['server']		= $args['server'];
		$this->db['username'] 	= $args['username'];
		$this->db['password'] 	= $args['password'];
		$this->db['database'] 	= $args['database'];
		$this->db['tblPrefix']	= $args['tblPrefix'];
		$this->db['connected']	= $this->_makeConnection();
		
		// Optional configs
		if (isset($args['gatewayOverride'])) // curl || socket
		{
			$this->tmp['useCURL'] = ($args['gatewayOverride'] == 'curl')?true:false;
		}
		if (isset($args['paranoid'])) // true or false
		{
			$this->paranoid = ($args['paranoid'])?true:false;
		}
		$this->tmp['sysArch']	= ($this->is64bit())?64:32;
		
		if ($this->db['connected'])
		{
			if (!$this->_load())
			{
				// Not installed, set up some defaults for installation
				$this->_refreshInstallPaths();
				$this->cfg['installDate']	= time();
			}
		}
	}
	
	/**************************************************************************
	 _makeConnection() [private]
	 
	 Connects to the database, returns false if the connection couldn't be made
	 **************************************************************************/
	function _makeConnection() 
	{
		$this->logBenchmark('_makeConnection() {');
		
		$connected = false;
		if (@mysql_connect($this->db['server'],$this->db['username'],$this->db['password']))
		{
			if (@mysql_select_db($this->db['database']))
			{
				$connected = true;
			}
			else
			{
				$this->logError('MySQL Error: '.mysql_error().'. ('.mysql_errno().')', 2);
			}
		}
		else
		{
			$this->logError('MySQL Error: '.mysql_error().'. ('.mysql_errno().')', 2);
		}
		if (!$connected)
		{
			$this->logErrorNote('<p>Mint was unable to connect to the database. Please make sure that the correct values have been added to <code>/config/db.php</code>.</p>');
		}
		$this->logBenchmark('}');
		return $connected;
	}
	
	function dbEngine()
	{
		$mysqlVersion = mysql_get_client_info();
		$mysqlVersion = preg_replace('#(^\D*)([0-9.]+).*$#', '\2', $mysqlVersion); // strip extra-version cruft
		return ($mysqlVersion > 4) ? 'ENGINE' : 'TYPE';
	}
		
	/**************************************************************************
	 query()
	 
	 Handler for mysql_query, writes query to $Mint->queries
	 **************************************************************************/
	function query($query) 
	{
		$this->logBenchmark('query("'.substr($query, 0, 24).'...") {');
		
		$this->queries[] = $query;
		if (!($result = mysql_query($query)))
		{
			$this->logError('MySQL Error: '.mysql_error().'. ('.mysql_errno().')<br />Query: '.$query);
			$result = false;
		}
		
		$this->logBenchmark('}');
		return $result;
	}
	
	/**************************************************************************
	 escapeSQL()
	 
	 **************************************************************************/
	function escapeSQL($str) 
	{	
		if (get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}
		return (!is_callable('mysql_real_escape_string'))?mysql_escape_string($str):mysql_real_escape_string($str);
	}
	
	/**************************************************************************
	 _load() [private]
	 
	 Pulls the config data from the database and loads installed Pepper.
	 **************************************************************************/
	function _load() 
	{
		$this->logBenchmark('_load() {');
		
		$query = "SELECT `cfg`,`data` FROM `{$this->db['tblPrefix']}_config` LIMIT 0,1";
		if ($result = $this->query($query))
		{
			if ($load = mysql_fetch_assoc($result))
			{
				$cfg	= $this->safeUnserialize($load['cfg']);
				$data	= $this->safeUnserialize($load['data']);
				
				if ($cfg !== false)
				{
					$this->cfg	= $cfg;
					
					if (isset($this->cfg['siteDomains']))
					{
						$this->_reloadDomains();
					}
					else // Check added for update from pre-120
					{
						// cache the old cfg and data
						$this->tmp['cache'] = array
						(
							'cfg' => $cfg,
							'data' => $data
						);
						
						// Reset cfg and data back to the default
						$defaultMint = get_class_vars('Mint');
						$this->cfg	= $defaultMint['cfg'];
						$this->cfg['version'] = $cfg['version']; // except the version
						$this->data	= array();
						$this->logBenchmark('}');
						return false;
					}
				}
				else
				{
					$this->logError('Mint\'s configuration data appears to be damaged beyond repair.', 2);
					$this->logBenchmark('}');
					return false;
				}
				
				if ($data !== false)
				{
					$this->data	= $data;
				}
				else
				{
					$this->logError('Mint\'s Pepper data may be damaged beyond repair. <a href="?recover"><strong>Try to recover&#8230;</strong></a>', 2);
					$this->logBenchmark('}');
					return false;
				}
			}
		}
		
		$this->logBenchmark('}');
		
		// Should be part of update but then missing Pepper will have broken images in their uninstall error message
		if (!isset($this->cfg['preferences']['style']))
		{
			$this->cfg['preferences']['style'] = 'vanilla_mint';
		}
		
		return $this->cfg['installed'];
	}
	
	/**************************************************************************
	 loadPepper() [private]
	 
	 Broken out from _load so that the $Mint object will already exist in the
	 global name space before any Pepper try to reference it.
	 **************************************************************************/
	function loadPepper()
	{
		$this->logBenchmark('loadPepper() {');
		
		if ($this->db['connected'])
		{
			// Uses Pepper Shaker and not $this->pepper so the correct Pepper ID is passed into the constructor
			foreach ($this->cfg['pepperShaker'] as $pepperId=>$pepper) 
			{
				$this->logBenchmark('loadPepper('.$pepper['class'].') {');
				
				if (@include_once(MINT_ROOT.$pepper['src']))
				{
					if (@class_exists($pepper['class']))
					{
						$this->pepper[$pepperId] 		= new $pepper['class']($pepperId);
						$this->pepper[$pepperId]->prefs	=& $this->cfg['preferences']['pepper'][$pepperId];
						$this->pepper[$pepperId]->data	=& $this->data[$pepperId];
						$this->pepper[$pepperId]->onPepperLoad();
					}
					else
					{
						$error  = "Mint could not instantiate the \"{$pepper['class']}\" Pepper class. <br />";
						$error  = "If you recently updated this Pepper and are experiencing this issue please contact the Pepper developer. <br />";
						$error .= "<a href=\"#Install\" class=\"uninstall\" onclick=\"SI.Mint.uninstallPepper('missing {$pepper['class']}','$pepperId'); return false;\"><img src=\"styles/{$this->cfg['preferences']['style']}/images/btn-uninstall.png\" alt=\"Uninstall\" width=\"62\" height=\"22\" /></a>";
						$this->logError($error);
					}
				}
				else 
				{
					$error  = "Mint could not locate \"{$pepper['class']}\" Pepper. <br />";
					$error .= "Missing from: <code>".str_replace('class.php', '', $pepper['src'])."</code> <br />";
					$error .= 'If you recently updated Mint, you may have forgotten to copy over the Pepper from the previous version. ';
					$error .= "<a href=\"#Install\" class=\"uninstall\" onclick=\"SI.Mint.uninstallPepper('missing {$pepper['class']}','$pepperId'); return false;\"><img src=\"styles/{$this->cfg['preferences']['style']}/images/btn-uninstall.png\" alt=\"Uninstall\" width=\"62\" height=\"22\" /></a>";
					$this->logError($error);
				}
				$this->logBenchmark('}');
			}
		}
		$this->logBenchmark('}');
	}
	
	/**************************************************************************
	 _save() [private]
	 
	 Writes the config and Pepper data back to the database
	 **************************************************************************/
	function _save() 
	{
		$this->logBenchmark('_save() {');
		if (!$this->errors['fatal']) // don't save if we've encountered a fatal error
		{
			$default  = get_class_vars('Mint'); 
			if (!empty($this->data) && $this->cfg != $default['cfg'])
			{
				$this->query("UPDATE `{$this->db['tblPrefix']}_config` SET `cfg` = '".addslashes(serialize($this->cfg))."', `data` = '".addslashes(serialize($this->data))."' WHERE `id`=1"); 
			}
			else
			{
				$this->logError('Save cancelled to prevent data loss.');
			}
		}
		$this->logBenchmark('}');
	}
	
	/**************************************************************************
	 _tidyDB() [deprecated]
	 
	 See _tidySave() below.
 	 **************************************************************************/
	function _tidyDB() 
	{
		$this->logError('The _tidyDB() method has been eliminated in favor of the new _tidySave() method. Please report sighting this message on the <a href="http://www.haveamint.com/forum/">Mint Forum</a>.');
	}
	
	/**************************************************************************
	 _tidySave() [private]
	 
	 "Rolls" our database tables and gives it a physical. Saves before 
	 performing any database maintenance to prevent redundant, concurrent 
	 OPTIMIZE and CHECK threads in MySQL.
 	 **************************************************************************/
	function _tidySave() 
	{
		$this->logBenchmark('_tidySave() {');
		
		// Determine if tidying is necessary
		$doOptimize	= ($this->cfg['lastOptimized'] < time() - (60 * 60 * 24 * 7));
		$doCheckup	= ($this->cfg['lastChecked'] < time() - (60 * 60));
		
		// Update configs accordingly
		if ($doOptimize) 
		{
			$this->cfg['lastOptimized'] = time();
		}
		if ($doCheckup) 
		{	
			$this->cfg['lastChecked'] = time();
		}
		
		// Save
		$this->_save();
		
		foreach($this->cfg['moderate'] as $table => $tablePepperId)
		{
			$likePrefix	= str_replace('_', '\_', $this->db['tblPrefix']);
			$likeTable 	= str_replace('_', '\_', $table);
			
			$forceOptimize = false;
			// Hourly check-up
			if ($doCheckup) 
			{
				// just in case this table slipped by without having an expiry or maxSize set
				if (!isset($this->cfg['preferences']['expiry'][$table]))	{ $this->cfg['preferences']['expiry'][$table] = 5; }
				if (!isset($this->cfg['preferences']['maxSize'][$table]))	{ $this->cfg['preferences']['maxSize'][$table] = 25; }
				
				// Safe-guard weeks against bad Mint updates, default back to 5
				$weeks = (isset($this->cfg['preferences']['expiry'][$table]))?(0 + $this->cfg['preferences']['expiry'][$table]):5;
				$expiration = time() - (60 * 60 * 24 * 7 * $weeks);

				$this->query("DELETE FROM `{$this->db['tblPrefix']}{$table}` WHERE `dt` < $expiration");

				// Keep db under max size
				if ($this->cfg['preferences']['maxSize'][$table])
				{
					$query="SHOW TABLE STATUS LIKE '{$likePrefix}{$likeTable}'";
					if ($result = $this->query($query)) 
					{
						$size = 0;
						$rows = 0;
						if ($tableStatus = mysql_fetch_assoc($result)) 
						{ 
							$size = $tableStatus['Data_length'] + $tableStatus['Index_length']; 
							$rows = $tableStatus['Rows'];
						}

						if ($rows)
						{
							// Get hits from the last recorded full hour
							$DefaultPepper = $this->getPepperByClassName('SI_Default');
							$hours = $DefaultPepper->data['visits'][1];

							$totalHours = 0;
							$totalHits	= 0;
							foreach ($hours as $visits)
							{
								$totalHits += $visits['total'];
								$totalHours++;
							}

							if ($totalHours)
							{
								$avgRowSize = round($size / $rows);
								$avgRowsHour = round($totalHits / $totalHours);
								$avgGrowth = $avgRowsHour * $avgRowSize;

								// If the database size is already above the max size
								$tableSize = $this->getTableSize($table);
								if ($tableSize > ($this->cfg['preferences']['maxSize'][$table] * 1024 * 1024))
								{
									$sizeDiff	= $tableSize - ($this->cfg['preferences']['maxSize'][$table] * 1024 * 1024);
									$removeRows	= round($sizeDiff / $avgRowSize) + $avgRowsHour;

									$query = "SELECT `dt` FROM `{$this->db['tblPrefix']}{$table}` ORDER BY `dt` ASC LIMIT {$removeRows}";
									if ($result = $this->query($query))
									{
										mysql_data_seek($result, (mysql_num_rows($result) - 1));
										$startRow = mysql_fetch_assoc($result);

										$query = "DELETE FROM `{$this->db['tblPrefix']}{$table}` WHERE `dt` <= {$startRow['dt']}";
										$this->query($query);
										$forceOptimize = true;
									}
								}
								// If the database is likely to exceed the max size in the next hour
								else if (($tableSize + $avgGrowth) > ($this->cfg['preferences']['maxSize'][$table] * 1024 * 1024))
								{
									$query = "SELECT `dt` FROM `{$this->db['tblPrefix']}{$table}` ORDER BY `dt` ASC LIMIT {$avgRowsHour}";
									if ($result = $this->query($query))
									{
										mysql_data_seek($result, (mysql_num_rows($result) - 1));
										$startRow = mysql_fetch_assoc($result);

										$query = "DELETE FROM `{$this->db['tblPrefix']}{$table}` WHERE `dt` <= {$startRow['dt']}";
										$this->query($query);
										$forceOptimize = true;
									}
								}
							}
						}
					}
				}
			}


			// Optimize the database every 7 days
			if ($doOptimize || $forceOptimize) 
			{
				$this->query("OPTIMIZE TABLE `{$this->db['tblPrefix']}{$table}`");
			}

			// Hourly check-up
			if ($doCheckup) 
			{	
				// Reduced optimized statements should prevent crashes but just to be sure
				$query = "CHECK TABLE {$this->db['tblPrefix']}{$table} FAST";
				if ($result = $this->query($query)) 
				{
					mysql_data_seek($result,mysql_num_rows($result)-1);
					$status = mysql_fetch_assoc($result);

					if ($status['Msg_type']=='error') { $this->query("REPAIR TABLE {$this->db['tblPrefix']}{$table}"); }
				}
			}
		}
		
		$this->logBenchmark('}');
	}
	
	/**************************************************************************
	 _verifyLicense() [private]
	 
	 Makes sure that this domain is licensed to use Mint. Returns false if not.
	 
	 REMOVING OR MODIFYING THIS CODE WILL TERMINATE YOUR LICENSE.
	 That's right, I didn't even try to obfuscate the activation code. I figure 
	 this way, if you do decide to remove or modify this bit then there can 
	 be no confusion--you're not being clever, you're just taking food off this 
	 honest, hardworking developer's table.
	 **************************************************************************/
	function _verifyLicense()
	{
		if ($this->isInstalledLocally())
		{
			return true;
		}
		
		$installDir		= urlencode("http://{$this->cfg['installDomain']}{$this->cfg['installDir']}");
		$activationKey	= urlencode($_POST['activationKey']);
		$sendDomain		= urlencode($this->cfg['installTrim']);
		$version		= urlencode($this->version);
		
		$response = $this->_gateway('Activation Ping', "activation_key=$activationKey&domain=$sendDomain&version=$version&install_dir=$installDir");
		if ($response != 'VERIFIED')
		{
			$this->dropError('MySQL'); // Silences confusing MySQL error when Activation fails
			
			// prevent a confusing unlicensed message when the Gateway can't be reached
			if (!$this->hasError('Gateway'))
			{
				$note = <<<HERE
<p>This domain does not appear to be licensed to install Mint 2. <strong>Mint 2 is a paid upgrade from Mint.</strong> Please double-check your Activation Key. If you do not have an Activation Key for this domain please visit <a href="http://www.haveamint.com/">haveamint.com</a> to purchase one.</p>
<p>If you think you may have received this message in error I apologize for the inconvenience and encourage you to <a href="http://www.haveamint.com/contact">contact me</a> to resolve the issue.</p>
HERE;
				$this->logErrorNote($note);
				$this->logError('The Activation Key <strong>'.$_POST['activationKey'].'</strong> is not valid for Mint 2 on: '.$this->cfg['installTrim']);
			}
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**************************************************************************
	 _updatesAvailable() [private]
	 
	 Returns the number of updates currently available for this Mint installation
	 **************************************************************************/
	function _updatesAvailable() 
	{
		// Check once a week
		if ($this->cfg['update']['lastPing'] < time() - (7 * 24 * 60 * 60)) 
		{
			$response = $this->_gateway('Request Update Ping', 'mint=2&pepper='.join(',', array_keys($this->cfg['pepperLookUp'])));
			if ($response)
			{
				$response = explode("\r\n", $response);
				$versions = array();

				foreach($response as $version)
				{
					$item = explode(' ', $version);
					$versions[$item[0]] = $item[1];
				}

				$update_available = 0;

				if ($versions['Mint'] > $this->version)
				{
					$update_available++;
				}
				foreach($versions as $pepperClass => $version)
				{
					if
					(
						isset($this->cfg['pepperLookUp'][$pepperClass]) &&
						$version > $this->cfg['pepperShaker'][$this->cfg['pepperLookUp'][$pepperClass]]['version']
					)
					{
						$update_available++;
					}
				}

				$this->cfg['update'] = array
				(
					'available'	=> $update_available,
					'lastPing'	=> time(),
					'dismissed' => 0
				);
				$this->_save();
				$this->dropError();
			}
		}
		
		return $this->cfg['update']['available'];
	}
	
	/**************************************************************************
	 upToDate()
	 
	 Dismisses the Update(s) Available button and redirects to the Peppermill.
	 **************************************************************************/
	function upToDate()
	{
		$this->cfg['update']['dismissed'] = 1;
		$this->_save();
		header('Location: http://www.haveamint.com/peppermill/uptodate/'.$this->cfg['installDomain'].$this->cfg['installDir']);
	}
	
	/**************************************************************************
	 _gateway() [private]
	 
	 Used for passing and requesting data from haveamint.com
	 
	 REMOVING OR MODIFYING THIS CODE WILL TERMINATE YOUR LICENSE.
	 That's right, I didn't even try to obfuscate the activation code. I figure 
	 this way, if you do decide to remove or modify this bit then there can 
	 be no confusion--you're not being clever, you're just taking food off this 
	 honest, hardworking developer's table.
	 **************************************************************************/
	function _gateway($action, $post = '')
	{
		$host	 	= 'www.haveamint.com';
		$gateway	= '/gateway';
		$useCURL 	= (isset($this->tmp['useCURL']))?$this->tmp['useCURL']:in_array('curl', get_loaded_extensions());
		$mintPing 	= "X-mint-ping: $action";
		$response	= '';
		$method		= (empty($post))?'GET':'POST';
		
		// There's a bug in the OS X Server/cURL combination that results in 
		// memory allocation problems so don't use cURL even if it's available
		if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Darwin') !== false)
		{
			$useCURL = false;
		}
		
		if ($useCURL)
		{
			$handle		= curl_init("http://{$host}{$gateway}");
			curl_setopt($handle, CURLOPT_HTTPHEADER, array($mintPing));
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
		}
		else
		{
			$headers	 = "{$method} {$gateway} HTTP/1.0\r\n";
			$headers	.= "Host: $host\r\n";
			$headers	.= "{$mintPing}\r\n";
		}
		
		if (!empty($post)) // This is a POST request
		{
			if ($useCURL)
			{
				curl_setopt($handle, CURLOPT_POST, true);
				curl_setopt($handle, CURLOPT_POSTFIELDS, $post);
			}
			else
			{
				$headers	.= "Content-type: application/x-www-form-urlencoded\r\n";
				$headers	.= "Content-length: ".strlen($post)."\r\n";
			}
		}
		
		$gatewayFAQ = ' Please see this <a href="http://www.haveamint.com/faqs/troubleshooting/gateway_connectivity">FAQ</a> for assistance.';
		
		if ($useCURL)
		{
			$response = curl_exec($handle);
			if (curl_errno($handle))
			{
				$error = 'Could not connect to Gateway (using cURL): '.curl_error($handle).$gatewayFAQ;
				$this->logError($error);
			}
			curl_close($handle);
		}
		else
		{
			$headers	.= "\r\n";
			$socket		 = @fsockopen($host, 80, $errno, $errstr, 10);
			if ($socket)
			{
				fwrite($socket, $headers.$post);
				while (!feof($socket)) 
				{
					$response .= fgets ($socket, 1024);
				}
				$response = explode("\r\n\r\n", $response, 2);
				$response = trim($response[1]);
			}
			else
			{
				$error = 'Could not connect to Gateway (using fsockopen): '.$errstr.' ('.$errno.')'.$gatewayFAQ;
				$this->logError($error);
				$response = 'FAILED';
			}
		}
		return $response;
	}
	
	/**************************************************************************
	 pinged()
	 
	 **************************************************************************/
	function pinged($ping)
	{
		switch($ping)
		{
			case 'Installed Versions':
				$response['siteDisplay'] 		= $this->cfg['siteDisplay'];
				$response['versions']['Mint']	= $this->version;
				foreach($this->cfg['pepperShaker'] as $pepper)
				{
					$response['versions'][$pepper['class']] = $pepper['version'];
				}
				echo serialize($response);
			break;
			
			case 'Transfer Authorization':
				echo 'INSTALLED';
			break;
		}
	}
	
	/**************************************************************************
	 update()
	 
	 **************************************************************************/
	function update() 
	{
		$additionalFeedback = '';
		
		// If we don't have an Activation Key on record or if this is the first update to Mint 2
		if (empty($this->cfg['activationKey']) || ($this->cfg['version'] < 200 && $this->cfg['version'] != 119))
		{
			
			$this->_refreshInstallPaths();
			
			if ($this->isInstalledLocally())
			{
				$_POST['activationKey'] = 'LOCALHOST';
			}
			
			if (isset($_POST['activationKey']))
			{
				if ($this->_verifyLicense())
				{
					$this->cfg['activationKey'] = $_POST['activationKey'];
				}
				else
				{
					global $Mint;
					include(MINT_ROOT.'app/paths/errors/index.php');
					exit();
				}
			}
			else
			{
				global $Mint;
				include(MINT_ROOT.'app/paths/install/activation.php');
				exit();
			}
		}
		
		
		if ($this->cfg['version'] < 114)
		{
			// Present the import message and auto refresh
			global $Mint;
			$Mint->tmp['pageTitle']	= 'Unable to update from this version';
			
			$feedback  = '<p><strong>To ensure a successful update to Mint 1.2 or greater, please update to 1.14 first. Thank you.</strong></p>';
			$feedback .= '<p>This is a one time inconvenience meant to make future updates easier.</p>';
			
			$this->logFeedback($feedback);
			include(MINT_ROOT.'app/paths/feedback/index.php');
			exit();
		}
		if ($this->cfg['version'] < 120)
		{
			if (isset($_GET['import120']))
			{	
				$start = (isset($_GET['startRow']))?$_GET['startRow']:0;
				$limit = 3000;
				// Import existing visit data
				$query = "SELECT * FROM `pre120_{$this->db['tblPrefix']}visit` ORDER BY `id` ASC LIMIT $start, $limit";
				if ($result = $this->query($query))
				{
					if (mysql_num_rows($result))
					{
						while ($row = mysql_fetch_assoc($result))
						{
							$save = array();
							foreach($row as $oldColumn => $value)
							{
								switch($oldColumn)
								{
									case 'ip':
										$save['ip_long'] = ip2long($value);
										break;
									case 'domain':
										$save['domain_checksum'] = crc32($value);
										break;
									case 'referer':
										$save['referer_checksum'] = crc32($value);
										if (!empty($value))
										{
											$save['referer_is_local'] = (preg_match("/^([^:]+):\/\/([a-z0-9]+[\._-])*(".str_replace('.', '\.', implode('|', $this->domains)).")/i", $value))?1:0;
										}
									case 'resource':
										$save['resource_checksum'] = crc32($value);
									default:
										if (isset($this->cfg['manifest']['visit'][$oldColumn]))
										{
											$save[$oldColumn] = $value;
										}
								}
							}
							
							$cols	= "(`".implode("`, `", array_keys($save))."`)";
							$vals	= "('".implode("', '", $save)."')";
							
							mysql_query("INSERT INTO `{$this->db['tblPrefix']}visit` $cols VALUES $vals");
						}
						
						$rows = 0;
						$likePrefix = str_replace('_', '\_', $this->db['tblPrefix']);
						$query="SHOW TABLE STATUS LIKE 'pre120\_{$likePrefix}visit'";
						if ($result = $this->query($query)) 
						{
							if ($table = mysql_fetch_assoc($result)) 
							{ 
								$rows = $table['Rows'];
							}
						}
						$totalRows = (!isset($_GET['totalRows']))?$rows:$_GET['totalRows'];
						
						// Present the import message and auto refresh
						global $Mint;
						$Mint->tmp['pageTitle']	= 'Importing Data';
						$Mint->tmp['headTags']	= '<meta http-equiv="refresh" content="0;url='.$this->cfg['installDir'].'/?import120&amp;startRow='.($start + $limit).'&amp;totalRows='.$totalRows.'" />'."\r";
						
						$feedback  = '<p><strong>Importing visits data from a previous installation. This may take more than five minutes depending on the size of your Mint database.</strong></p>';
						$feedback .= '<p class="progress"><span class="bar" style="background-position: '.round(($totalRows - $start) / $totalRows * 100).'% 0;">'.$start.'/'.$totalRows.'</span></p>';
						$feedback .= '<p>Do not cancel or refresh this page. You will be presented with confirmation once the update is complete.</p>';
						
						$this->logFeedback($feedback);
						include(MINT_ROOT.'app/paths/feedback/index.php');
						// This update is non-destructive. 
						// Old tables should be removed after a successful update
						exit();
					}
				}
			}
			else
			{
				// Rename old tables
				$this->query("ALTER TABLE `{$this->db['tblPrefix']}_config` RENAME `pre120_{$this->db['tblPrefix']}_config`;");
				$this->query("ALTER TABLE `{$this->db['tblPrefix']}visit` RENAME `pre120_{$this->db['tblPrefix']}visit`;");
				
				// If we've lost the config data somehow, get it from the backup
				$defaultMint = get_class_vars('Mint');
				if 
				(
					!isset($this->tmp['cache']['cfg'])					||
					!isset($this->tmp['cache']['data'])					||
					$this->tmp['cache']['cfg'] =  $defaultMint['cfg']	||
					empty($this->tmp['cache']['data'])
				)
				{
					$query = "SELECT `cfg`,`data` FROM `pre120_{$this->db['tblPrefix']}_config` LIMIT 0,1";
					$this->queries[] = $query;
					if ($result = mysql_query($query))
					{
						if ($load = mysql_fetch_assoc($result))
						{
							$this->tmp['cache']['cfg']	= $this->safeUnserialize($load['cfg']);
							$this->tmp['cache']['data']	= $this->safeUnserialize($load['data']);
						}
					}
				}
				
				// Capture some existing values
				$this->cfg['installDate']				= $this->tmp['cache']['cfg']['install_dt'];
				$this->cfg['preferences']['fixHeight']	= $this->tmp['cache']['cfg']['preferences']['fix_height'];
				$this->cfg['preferences']['expiry']		= $this->tmp['cache']['cfg']['preferences']['expiry'];
				$this->cfg['preferences']['rows']		= $this->tmp['cache']['cfg']['preferences']['rows'];
				$this->cfg['preferences']['rssRows']	= $this->tmp['cache']['cfg']['preferences']['rss_rows'];
				$this->cfg['preferences']['secondary']	= $this->tmp['cache']['cfg']['preferences']['secondary'];
				$this->cfg['preferences']['maxSize']	= 0; // Default to 0 since it's a new setting and may delete data the user doesn't want deleted yet
				
				$_POST['siteDisplay']	= $this->tmp['cache']['cfg']['site_display'];
				$_POST['siteDomains']	= $this->tmp['cache']['cfg']['cookie_domain'];
				$_POST['offset']		= $this->tmp['cache']['cfg']['offset'];
				$_POST['email']			= $this->tmp['cache']['cfg']['email'];
				$_POST['password']		= $this->tmp['cache']['cfg']['password'];
				
				// Install
				$this->install();
				$this->loadPepper();
				
				// Import existing Pepper preferences and data
				foreach($this->tmp['cache']['cfg']['plugins'] as $oldPepperId => $pepperData)
				{
					if ($pepperData['class'] == 'SI_DefaultPepper')
					{
						$pepperData['class'] = 'SI_Default';
					}
					
					$pepper = $this->getPepperByClassName($pepperData['class']);
					
					if ($pepper)
					{
						if (isset($this->tmp['cache']['cfg']['preferences']['plugin-'.$oldPepperId]))
						{
							$pepper->prefs = $this->tmp['cache']['cfg']['preferences']['plugin-'.$oldPepperId];
							$this->cfg['preferences']['pepper'][$pepper->pepperId] = $pepper->prefs;
						}
						if (isset($this->tmp['cache']['data'][$oldPepperId]))
						{
							$pepper->data = $this->tmp['cache']['data'][$oldPepperId];
							$this->data[$pepper->pepperId] = $pepper->data;
						}
					}
				}
				
				// knock the version back one (this is a no-no but a necessary evil in this case)
				$this->cfg['version'] = 119;
				$this->_save();
				
				// Import old data
				header("Location:{$this->cfg['installDir']}/?import120");
				exit();
			}
		}
		if ($this->cfg['version'] < 126)
		{
			if ($this->is64bit()) // If we're on a 64-bit machine
			{
				// Change checksum column types to unsigned
				$requiresUpdate = array('ip_long');
				
				// Get names of checksum columns that need updating
				$query = "SHOW COLUMNS FROM `{$this->db['tblPrefix']}visit` LIKE '%\_checksum'";
				if ($result = $this->query($query))
				{
					while ($row = mysql_fetch_assoc($result))
					{
						$requiresUpdate[] = $row['Field'];
					}
				}
				
				// Change SIGNED to UNSIGNED
				foreach ($requiresUpdate as $column)
				{
					$query = "ALTER TABLE `{$this->db['tblPrefix']}visit` CHANGE `$column` `$column` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL";
					$this->query($query);
				}
				// $additionalFeedback .= 'Updated checksums to accommodate 64-bit server architecture. ';
			}
		}
		
		if ($this->cfg['version'] < 200)
		{
			$this->cfg['preferences']['singleColumn'] = false;
			$this->cfg['preferences']['stripeAll'] = false;
			$this->cfg['preferences']['singleColumnWidth'] = 480;
			$this->cfg['preferences']['staggerPanes'] = true;
			$this->cfg['preferences']['style'] = 'vanilla_mint';

			$this->cfg['moderate'] = array
			(
				'visit'	=> -1
			);
			
			if (!is_array($this->cfg['preferences']['expiry']))
			{
				$this->cfg['preferences']['expiry'] = array
				(
					'visit'	=> $this->cfg['preferences']['expiry']
				);
			}
			
			if (!is_array($this->cfg['preferences']['maxSize']))
			{
				$this->cfg['preferences']['maxSize'] = array
				(
					'visit'	=> $this->cfg['preferences']['maxSize']
				);
			}

			$this->cfg['preferences']['collapseVert'] = true;
		}
		if ($this->cfg['version'] < 212)
		{
			$this->cfg['preferences']['ignoreIPsMint']	= '65.55.165.*';
			$this->cfg['preferences']['ignoredIPsLong']	= array
			(
				array('1094165760', '1094166015') // 65.55.165.*
			);
		}
		if ($this->cfg['version'] < 213)
		{
			$query = "ALTER TABLE `{$this->db['tblPrefix']}_config` CHANGE `cfg` `cfg` MEDIUMTEXT, CHANGE  `data` `data` MEDIUMTEXT";
			$this->query($query);
		}
		
		// Allows Pepper to react to Mint updates
		foreach ($this->pepper as $pepperId => $pepper)
		{
			$pepper->onUpdate();
		}
		
		$this->cfg['version'] = $this->version;
		$this->cfg['update'] = array
		(
			'available'	=> 0,
			'lastPing'	=> 0,
			'dismissed'	=> 0
		);
		$this->logFeedback('<p>Mint has been updated successfully! '.$additionalFeedback.'</p>', 'okay', $this->cfg['installDir'].'/');
		$this->_save();
	}
	
	/**************************************************************************
	 install()
	 
	 You guessed it!
	 **************************************************************************/
	function install() 
	{
		$this->cfg['activationKey']	= $_POST['activationKey'];
		$this->cfg['siteDisplay']	= stripslashes($_POST['siteDisplay']);
		$this->cfg['siteDomains']	= $_POST['siteDomains'];
		$this->cfg['offset'] 		= $_POST['offset'];
		$this->cfg['email']			= $_POST['email'];
		$this->cfg['password']		= $_POST['password'];
		
		$this->cfg['version']		= $this->version;
		
		$dbEngine = $this->dbEngine();
		$this->query("CREATE TABLE `{$this->db['tblPrefix']}_config` (`id` int(10) unsigned NOT NULL auto_increment, `cfg` MEDIUMTEXT NOT NULL, `data` MEDIUMTEXT NOT NULL, PRIMARY KEY  (`id`)) {$dbEngine}=MyISAM;");
		$this->query("INSERT INTO `{$this->db['tblPrefix']}_config` VALUES (1, '".addslashes(serialize($this->cfg))."', '')");
 		$this->query("CREATE TABLE `{$this->db['tblPrefix']}visit` (`id` int(11) unsigned NOT NULL auto_increment, `dt` int(10) unsigned NOT NULL default '0', PRIMARY KEY  (`id`), KEY `dt` (`dt`)) {$dbEngine}=MyISAM;");
		
		// Install the required Default Pepper first
		$this->installPepper('pepper/shauninman/default/class.php');
		
		// Then grab any optional Pepper and install them
		$uninstalledPepper = $this->getPathsToPepper();
		if (!empty($uninstalledPepper))
		{
			foreach ($uninstalledPepper as $pathToPepper)
			{
				$this->installPepper($pathToPepper);
			}
		}
		$this->cfg['installed'] = true;
		$this->_save();
		$this->_setLoginCookie();
		return $this->_load();
	}
	
	/**************************************************************************
	 _refreshInstallPaths() [private]
	 
	 Determines Mint's location on the server, used prior to installation and
	 by the ?moved query command
	 **************************************************************************/
	function _refreshInstallPaths()
	{
		$self	= (isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF']))?$_SERVER['PHP_SELF']:((isset($_SERVER['SCRIPT_NAME']) && !empty($_SERVER['SCRIPT_NAME']))?$_SERVER['SCRIPT_NAME']:$_SERVER['SCRIPT_URL']);
		$domain	= (!empty($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] != $_SERVER['SERVER_NAME'])?$_SERVER["HTTP_HOST"]:$_SERVER['SERVER_NAME'];
		
		$this->cfg['installDir']	= preg_replace("/\/+[^\/]*$/", '', $self);
		$this->cfg['installDomain']	= $domain;
		$this->cfg['installTrim']	= preg_replace('/(^www\.|:\d+$)/', '', $this->cfg['installDomain']);
		$this->cfg['installFull']	= 'http://'.$this->cfg['installDomain'].$this->cfg['installDir'];
	}
	
	/**************************************************************************
	 updateAfterMove()
	 
	 Updates the installVars and keeps your account in sync with the changes
	 **************************************************************************/
	function updateAfterMove()
	{
		$old_install = $this->cfg['installFull'];
		$this->_refreshInstallPaths();
		$this->_save();
			
		if (!$this->isInstalledLocally() && $this->cfg['installFull'] != $old_install)
		{
			$this->_gateway('Moved Ping', 'activation_key='.urlencode($this->cfg['activationKey']).'&domain='.urlencode($this->cfg['installTrim']).'&install_dir='.urlencode("http://{$this->cfg['installDomain']}{$this->cfg['installDir']}"));
		}
		$this->logFeedback("<p>Mint configuration successfully updated after move!<p>", 'okay', $this->cfg['installDir'].'/');
	}
	
	/**************************************************************************
	 uninstall()
	 
	 Completely removes the Mint database
	 **************************************************************************/
	function uninstall($confirm = false) 
	{
		if (!$this->cfg['installed']) 
		{
			$this->logError('Could not find Mint installation to remove.', 2);
			return; 
		}
		if ($confirm) 
		{
			foreach($this->cfg['manifest'] as $table => $unused)
			{
				$tables[] = $this->db['tblPrefix'].$table;
			}
			$this->query("DROP TABLE `".implode("`, `", $tables)."`");
			$this->cfg['installed'] = false;
			$this->logout();
			$this->logError('Uninstalled');
			return;
		}
	}
	
	/**************************************************************************
	 getPathsToPepper()
	 
	 Looks for Pepper that hasn't been installed. Returns an array of paths to 
	 the uninstalled Pepper
	 **************************************************************************/
	function getPathsToPepper()
	{
		$uninstalledPepper	= array();
		$pathToPepper		= 'pepper/';
		
		// Check for the Pepper diretory
		if (@is_dir($pathToPepper)) 
		{
			// Open the Pepper directory
			if ($dirHandle = opendir($pathToPepper)) 
			{
				// Loop through Pepper directory
				while (($devDir = readdir($dirHandle)) !== false) 
				{
					// make sure this item isn't . or  ..
					if ($devDir == '.' || $devDir == '..') 
					{ 
						continue; 
					}
					// if the item is another directory
					if (@is_dir($pathToPepper.$devDir)) 
					{
						// Open the developer's directory
						if ($devHandle = opendir($pathToPepper.$devDir.'/')) 
						{
							// Loop through developer's directory
							while (($pepperDir = readdir($devHandle)) !== false) 
							{
								// make sure this item isn't . or  ..
								if ($pepperDir == '.' || $pepperDir == '..') 
								{
									continue;
								}
								// if the item is another directory
								if (@is_dir($pathToPepper.$devDir.'/'.$pepperDir)) 
								{
									$pepperClass = $pathToPepper.$devDir.'/'.$pepperDir.'/class.php';
									if (@file_exists($pepperClass)) 
									{
										$uninstalledPepper[] = $pepperClass;
									}
								}
							}
							closedir($devHandle);
						}
					}
				}
				closedir($dirHandle);
			}
		}
		return $uninstalledPepper;
	}
	
	/**************************************************************************
	 installPepper()
	 
	 If I have to tell you...
	 **************************************************************************/
	function installPepper($pathToPepper)
	{
		include_once(MINT_ROOT.$pathToPepper);
		if (isset($installPepper))
		{
			$pepperId		= $this->array_last_index($this->cfg['pepperShaker'])+1;
			$pepper			= new $installPepper($pepperId);
			$compatibility	= $pepper->isCompatible();
			
			if ($compatibility['isCompatible'])
			{
				// Add columns/tables as necessary
				$this->syncManifest($pepperId, $pepper->manifest, $pepper->moderate);
				
				$this->cfg['pepperShaker'][$pepperId] = array
				(
					'src' 		=> $pathToPepper,
					'class'		=> $installPepper,
					'version'	=> $pepper->version
				);
				$this->cfg['pepperLookUp'][$installPepper] = $pepperId;
				
				foreach ($pepper->panes as $paneName => $tabsArray)
				{
					$paneId = $this->array_last_index($this->cfg['panes']) + 1;
					$this->cfg['panes'][$paneId] = array
					(
						'pepperId'	=> $pepperId,
						'name'		=> $paneName,
						'tabs'		=> $tabsArray
					);
					$this->cfg['preferences']['paneOrder']['enabled'][] = $paneId;
					$this->cfg['pepperShaker'][$pepperId]['panes'][] = $paneId;
				}
				
				$this->cfg['preferences']['pepper'][$pepperId] = $pepper->prefs;
				$this->data[$pepperId] = $pepper->data;
				$pepper->onInstall();
				$this->_save();
				$this->logFeedback('<p>'.$pepper->info['pepperName'].' successfully installed!</p>', 'okay');
				$this->bakeCookie('MintConfigurePepper', $pepperId);
			}
			else
			{
				if (isset($compatibility['explanation']) && !empty($compatibility['explanation']))
				{
					$this->logErrorNote($compatibility['explanation']);
				}
				else
				{
					$this->logErrorNote('<p>The '.$pepper->info['pepperName'].' Pepper is not compatible with this installation. No reason was given.</p>');
				}
			}
			unset($pepper);
		}
	}
	
	/**************************************************************************
	 syncPepper()
	 
	 Should follow the Pepper update() method. Synchronizes Mint's internal
	 tab and manifest lookups as well as Pepper version.
	 **************************************************************************/
	function syncPepper($pepperId)
	{
		$pepper 	= $this->pepper[$pepperId];
		$freshPanes	= $pepper->panes;
		$stalePanes	= array();
		
		// sync Pepper pane tabs (easy compared to the panes themselves)
		foreach ($this->cfg['panes'] as $paneId => $paneInfo)
		{
			// make sure we're only touching this Pepper's panes
			if ($paneInfo['pepperId'] == $pepperId)
			{
				if (isset($freshPanes[$paneInfo['name']]))
				{	
					$this->cfg['panes'][$paneId]['tabs'] = $freshPanes[$paneInfo['name']];
					unset($stalePanes[$paneId]);
					unset($freshPanes[$paneInfo['name']]);
				}
				else
				{
					$stalePanes[$paneId] = $paneInfo;
				}
			}
		}
		
		// likely that a pane has been renamed
		if (!empty($freshPanes) && !empty($stalePanes) && count($freshPanes) == count($freshPanes))
		{
			// loop through each stale pane
			foreach($stalePanes as $paneId => $paneInfo)
			{
				// and compare its tabs agains each of the fresh panes tabs
				foreach($freshPanes as $paneName => $tabs)
				{
					if ($paneInfo['tabs'] == $tabs)
					{
						// if you find a match, update the pane name and remove the matches
						$this->cfg['panes'][$paneId]['name'] = $paneName;
						unset($stalePanes[$paneId]);
						unset($freshPanes[$paneName]);
					}
				}
			}
		}

		// a pane has been added to this Pepper
		if (!empty($freshPanes))
		{
			foreach($freshPanes as $paneName => $tabs)
			{
				$paneId = $this->array_last_index($this->cfg['panes']) + 1;
				$this->cfg['panes'][$paneId] = array
				(
					'pepperId'	=> $pepperId,
					'name'		=> $paneName,
					'tabs'		=> $tabs
				);
				$this->cfg['preferences']['paneOrder']['enabled'][] = $paneId;
				$this->cfg['pepperShaker'][$pepperId]['panes'][] = $paneId;
			}
		}
		
		// a pane has been removed from this Pepper
		if (!empty($stalePanes))
		{
			$enabledOrder	= $this->cfg['preferences']['paneOrder']['enabled'];
			$disabledOrder	= $this->cfg['preferences']['paneOrder']['disabled'];
			
			foreach ($stalePanes as $paneId => $paneInfo) 
			{
				unset($this->cfg['panes'][$paneId]);
				
				// Remove from enabled pane order
				foreach ($enabledOrder as $j => $orderedPaneId) 
				{
					if ($paneId == $orderedPaneId) 
					{
						unset($enabledOrder[$j]);
					}
				}
				$enabledOrder = $this->array_reindex($enabledOrder);
				
				// Remove from disabled pane order
				foreach ($disabledOrder as $j => $orderedPaneId) 
				{
					if ($paneId == $orderedPaneId) 
					{
						unset($disabledOrder[$j]);
					}
				}
				$disabledOrder = $this->array_reindex($disabledOrder);
				
			}
			$this->cfg['preferences']['paneOrder']['enabled']	= $enabledOrder;
			$this->cfg['preferences']['paneOrder']['disabled']	= $disabledOrder;
		}
		/**/
		
		// update tables according to manifest
		$this->syncManifest($pepperId, $pepper->manifest, $pepper->moderate);
		
		// update Pepper version
		$this->cfg['pepperShaker'][$pepperId]['version'] = $pepper->version;
		$this->_save();
	}
	
	/**************************************************************************
	 syncManifest()
	 
	 Used when installing new Pepper and when synchronizing updated Pepper 
	 (which is why the PepperShaker can't be used to lookup the manifest)
	 **************************************************************************/
	function syncManifest($pepperId, $pepperManifest, $pepperModerate)
	{
		if (!empty($pepperManifest))
		{
			foreach($pepperManifest as $table => $columns)
			{
				$tableName = $this->db['tblPrefix'].$table;
				$query = '';
				
				// if the table already exists add columns
				if ($result = $this->query("SHOW TABLES LIKE '$tableName'"))
				{
					if ($tableExists = mysql_fetch_assoc($result))
					{
						// Build an arry of existing columns in the table to mirror the manifest table array
						$existingColumns = array();
						if ($result = $this->query("SHOW COLUMNS FROM `{$tableName}`"))
						{
							while ($exisitingColumn = mysql_fetch_assoc($result))
							{
								$existingColumns[$exisitingColumn['Field']] = $exisitingColumn['Type'];
							}
						}
						
						$addColumns = array();
						foreach($columns as $column => $type)
						{
							// This column already exists in this table, skip it
							if (isset($existingColumns[$column]))
							{
								continue;
							}
							
							if ($this->is64bit())
							{
								if (preg_match('/(^ip_long|.+_checksum)$/i', $column))
								{
									$type = "INT(10) UNSIGNED NOT NULL"; // overrides column definition on 64-bit architectures
								}
							}
							
							$partialQuery = "ADD `$column` $type";
							if (preg_match('/^[a-z]*INT\s*\(/i', $type))
							{
								$partialQuery .= ", ADD INDEX (`$column`)";
							}
							$addColumns[] = $partialQuery;
							
							// Add column to the manifest
							$this->cfg['manifest']['visit'][$column] = $pepperId;
						}
						
						$query = (!empty($addColumns)) ? "ALTER TABLE `$tableName` ".implode(', ', $addColumns) : '';
					}
					// otherwise create the table
					else
					{
						$query 		= "CREATE TABLE `$tableName` (";
						$queryTail	= '';
						$addColumns	= array();
						
						$firstColumn = true;
						foreach($columns as $column => $type)
						{
							$partialQuery = "`$column` $type";
							$addColumns[] = $partialQuery;
							
							if ($firstColumn)
							{
								$queryTail .= ", PRIMARY KEY (`$column`)";
								$firstColumn = false;
							}
							else if (preg_match('/^[a-z]*INT\s*\(/i', $type))
							{
								$queryTail .= ",  KEY `$column` (`$column`)";
							}
						}
						
						$queryTail	.= ') '.$this->dbEngine().'=MyISAM;';
						$query .= implode(', ', $addColumns).$queryTail;
						
						// Add the table to the manifest
						$this->cfg['manifest'][$table] = $pepperId;
						
						// If this table requires moderation
						if (in_array($table, $pepperModerate) && !isset($this->cfg['moderate'][$table]))
						{
							$this->cfg['moderate'][$table] = $pepperId;
							$this->cfg['preferences']['expiry'][$table]		= 5;
							$this->cfg['preferences']['maxSize'][$table]	= 25;	
						}
					}
					
					if (!empty($query))
					{
						$this->query($query);
					}
				}
			}
		}
		
		if (!empty($pepperModerate))
		{
			foreach($pepperModerate as $table)
			{
				// If this table isn't already being moderated
				if (!isset($this->cfg['moderate'][$table]))
				{
					$this->cfg['moderate'][$table] = $pepperId;
					$this->cfg['preferences']['expiry'][$table]		= 5;
					$this->cfg['preferences']['maxSize'][$table]	= 25;
				}
			}
		}
	}
	
	/**************************************************************************
	 uninstallPepper()
	 
	 **************************************************************************/
	function uninstallPepper($pepperId)
	{
		if (isset($this->cfg['pepperShaker'][$pepperId])) 
		{
			if (isset($this->pepper[$pepperId]))
			{
				$this->pepper[$pepperId]->onUninstall();
			}
			$pepper = $this->cfg['pepperShaker'][$pepperId];
			
			if ($pepper['class'] == 'SI_Default') // Don't delete Default
			{ 
				$this->logError('Default Pepper cannot be uninstalled.', 2);
				return;
			}
			
			// Remove Pepper database columns and tables
			foreach($this->cfg['manifest'] as $table => $pepperIdOrColumns)
			{
				$tableName = $this->db['tblPrefix'].$table;
				
				if (is_array($pepperIdOrColumns)) // Visit table, remove columns
				{
					$dropColumns = array();
					foreach($pepperIdOrColumns as $column => $thisPepperId)
					{
						if ($thisPepperId == $pepperId)
						{
							$dropColumns[] = $column;
							unset($this->cfg['manifest'][$table][$column]);
						}
					}
					
					if (!empty($dropColumns))
					{
						$query = "ALTER TABLE `$tableName` DROP `".implode('`, DROP `', $dropColumns)."`";
						$this->query($query);
					}
				}
				else // Other table, remove table if pepperId matches
				{
					if ($pepperIdOrColumns == $pepperId)
					{
						if ($this->query("DROP TABLE `$tableName`"))
						{
							unset($this->cfg['manifest'][$table]);
						}
					}
				}
			}
			
			// remove from moderation
			foreach($this->cfg['moderate'] as $table => $tablePepperId)
			{
				if ($tablePepperId == $pepperId)
				{
					unset($this->cfg['moderate'][$table]);
					unset($this->cfg['preferences']['expiry'][$table]);
					unset($this->cfg['preferences']['maxSize'][$table]);
				}
			}
			
			// Lose the panes
			if (isset($pepper['panes']) && !empty($pepper['panes']))
			{
				$panes 			= $pepper['panes'];
				$enabledOrder	= $this->cfg['preferences']['paneOrder']['enabled'];
				$disabledOrder	= $this->cfg['preferences']['paneOrder']['disabled'];
				
				foreach ($panes as $i => $paneId) 
				{
					unset($this->cfg['panes'][$paneId]);
					
					// Remove from enabled pane order
					foreach ($enabledOrder as $j => $orderedPaneId) 
					{
						if ($paneId == $orderedPaneId) 
						{
							unset($enabledOrder[$j]);
						}
					}
					$enabledOrder = $this->array_reindex($enabledOrder);
					
					// Remove from disabled pane order
					foreach ($disabledOrder as $j => $orderedPaneId) 
					{
						if ($paneId == $orderedPaneId) 
						{
							unset($disabledOrder[$j]);
						}
					}
					$disabledOrder = $this->array_reindex($disabledOrder);
					
				}
				$this->cfg['preferences']['paneOrder']['enabled']	= $enabledOrder;
				$this->cfg['preferences']['paneOrder']['disabled']	= $disabledOrder;
			}
			$pepperClass	= $pepper['class'];
			$pepperDir 		= str_replace('class.php', '', $pepper['src']);
			unset($this->cfg['pepperShaker'][$pepperId]);
			unset($this->cfg['pepperLookUp'][$pepperClass]);
			unset($this->cfg['preferences']['pepper'][$pepperId]);
			unset($this->data[$pepperId]);
			
			// Remove missing Pepper error
			foreach ($this->errors['list'] as $i => $error)
			{
				if (strpos($error, $pepperClass) !== false)
				{
					unset($this->errors['list'][$i]);
				}
			}
			
			$feedback = <<<HERE
<p>{$pepper->info['pepperName']} Pepper successfully uninstalled. You may now safely remove its directory:</p>
<p><code>{$this->cfg['installDir']}/$pepperDir</code></p>
<p>Alternately, you can leave the directory alone and re-install this Pepper at a later date.</p>
HERE;
			$this->logFeedback($feedback, 'okay');
			
			if (isset($this->pepper[$pepperId]))
			{
				unset($this->pepper[$pepperId]);
			}
			$this->_save();
			$this->bakeCookie('MintConfigurePepper', '', time() - (365 * 24 * 60 * 60));
		}
		else
		{
			$this->logError('Could not find the specified Pepper. ('.$pepperId.')');
		}
	}
	
	/**************************************************************************
	 javaScript()
	 
	 Returns the JavaScript strings provided by the plug-ins onJavaScript event
	 handlers.
	 **************************************************************************/
	function javaScript() 
	{
		if (!$this->db['connected']) 
		{ 
			echo "// Could not connect to the database\r";
			return;
		}
		foreach ($this->pepper as $pepper) 
		{
			$pepper->onJavaScript();
		}
	}
	
	
	/**************************************************************************
	 shouldIgnore()
	 
	 Whether or not Mint should ignore this hit. Let the arms race begin.
	 **************************************************************************/
	function shouldIgnore()
	{
		$ignore = false;
		
		// Ignore my visits cookie
		if (isset($_COOKIE['MintIgnore']) && $_COOKIE['MintIgnore']=='true')
		{
			$ignore = true;
		}
		
		// Ignore these IPs setting
		$ipLong = $this->getIPLong();
		foreach($this->cfg['preferences']['ignoredIPsLong'] as $ignored)
		{
			if 
			(
				(is_array($ignored) && $ipLong >= $ignored[0] && $ipLong <= $ignored[1]) ||
				$ipLong == $ignored
			)
			{
				$ignore = true;
				break;
			}
		}
		
		return $ignore;
	}
	
	/**************************************************************************
	 record()
	 
	 The workhorse. Loops through the installed plug-ins calling their onRecord
	 event handlers, storing their return values in an array that is used to
	 construct the INSERT query
	 **************************************************************************/
	function record() 
	{
		$this->logBenchmark('record() {');
		
		// Only proceed if we're connected, there's no cookie indicating that this hit should be ignored or we can verify the unique key
		if 
		(
			!$this->db['connected'] || 
			$this->shouldIgnore() || 
			!$this->verifyDomain() ||
			!$this->verifyKey()
		)
		{
			$this->logBenchmark('}');
			return;
		}
		
		if (isset($_COOKIE['MintAcceptsCookies']))
		{
			$this->acceptsCookies = true;
		}
		
		$save = array();
		foreach ($this->pepper as $pepper) 
		{
			$this->logBenchmark('record('.$pepper->info['pepperName'].') {');
			
			// collect the effected column/value pairs returned from the plug-in
			$pepper_data = $pepper->onRecord();
			if (is_array($pepper_data)) // protect against uninformed Pepper developers
			{
				$save = array_merge($save, $pepper_data);
			}
			$this->logBenchmark('}');
		}
		
		$cols	= "(`".implode("`, `", array_keys($save))."`, `dt`)";
		$vals	= "('".implode("', '", $save)."', ".time().")";
		
		$this->query("INSERT INTO `{$this->db['tblPrefix']}visit` $cols VALUES $vals");
		$this->_tidySave();
		$this->logBenchmark('}');
	}
	
	/**************************************************************************
	 display()
	 
	 Returns an HTML string that displays all the recorded stats including 
	 navigation etc.
	 **************************************************************************/
	function display() 
	{
		if (!$this->db['connected']) 
		{ 
			return;
		}
		
		$this->logBenchmark('display() {');
		
		$this->_saveIEFromItself("I'm not laughing.");
		
		$update = '';
		$h2style = '';
		$updatesAvailable = $this->_updatesAvailable();
		if ($updatesAvailable && !$this->cfg['update']['dismissed'])
		{
			$update = "<a href=\"?uptodate\" class=\"update-btn\"><span>Update".(($updatesAvailable > 1) ? 's' : '')." Available</span></a>";
			$h2style = ' style="visibility: hidden;"';
		}
		
		$header  = "<div id=\"header-container\">\r";
		$header .= "\t<div id=\"header\"";
		$header .= ($this->cfg['preferences']['singleColumn']) ? ' style="width: '.($this->cfg['preferences']['singleColumnWidth'] - 72).'px;"' : '';
		$header .= ">\r";
		$header .= "\t\t<h1><a href=\"{$this->cfg['installDir']}/\" class=\"refresh\">MINT</a></h1>\r";
		$header .= "\t\t<h2{$h2style}>A Fresh Look at Your Site</h2>\r";
		$header .= "\t\t{$update}\r";
		$header .= "\t\t<div class=\"panes\">\r\t\t\t<div>\r\t\t\t\tPanes: &nbsp; \r\t\t\t\t<span id=\"pane-list\">";
		
		$js  = "<script type=\"text/javascript\" language=\"javascript\">\r";
		$js .= "// <![CDATA[\r";
		$js .= "SI.Mint.panes = [";
		
		$html = '';
		$html .= '<div id="pane-container">';
		foreach ($this->cfg['preferences']['paneOrder']['enabled'] as $paneId) 
		{
			$pepperId	= $this->cfg['panes'][$paneId]['pepperId'];
			$paneName	= $this->cfg['panes'][$paneId]['name'];
			$tabs		= $this->cfg['panes'][$paneId]['tabs'];
			if (isset($this->pepper[$pepperId]))
			{
				$pepper 	= $this->pepper[$pepperId];
				
				if (!is_object($pepper))
				{
					$this->logError('Could not load "'.$paneName.'" pane.');
					continue;
				}
				
				$header .= "\r\t\t\t\t\t<a href=\"#pane-$paneId\" onclick=\"SI.Scroll.to(this.href.replace(/^[^#]*#/,'')); return false;\">$paneName</a> &nbsp; ";
				$js .= "$paneId,";
				
				$html .= "<div id=\"pane-$paneId\" class=\"pane\">\r";
				$html .= "\t<h1>$paneName</h1>\r";
				$html .= "\t<ul class=\"tabs\">";
				
				$active_tab = (isset($_COOKIE["MintPane$paneId"]) && isset($tabs[$_COOKIE["MintPane$paneId"]]))?$_COOKIE["MintPane$paneId"]:0;
				$tab_count = count($tabs);
				for ($j = 0; $j < $tab_count; $j++) 
				{
					$classes = array();
					if ($j == $active_tab)
					{
						$classes[] = 'active';
					}
					if ($tab_count == 1)
					{
						$classes[] = 'only-child';
					}
					else if ($j == 0)
					{
						$classes[] = 'first-child';
					}
					else if ($j == $tab_count - 1)
					{
						$classes[] = 'last-child';
					}
					
					$class = (!empty($classes))?' class="'.join(' ', $classes).'"':'';
					$html .= "<li$class><a href=\"#\" onclick=\"SI.Mint.loadTab($paneId,this); SI.Cookie.set('MintPane$paneId',$j); return false;\">{$tabs[$j]}</a></li>";
				}
				$html .= "</ul>\r";
				$html .= "<div id=\"pane-$paneId-content\" class=\"content\">\r<div class=\"content-container\">\r";
				$html .= $pepper->onDisplaySupplemental($paneName);
				$scroll_class = (in_array($paneName, $pepper->oddPanes)) ? 'scroll-inline' : 'scroll';
				
				$this->logBenchmark('display('.$pepper->info['pepperName'].' : '.$paneName.' : '.$tabs[$active_tab].') {');
				$pane_html = ($this->cfg['preferences']['staggerPanes']) ? '&nbsp;' : $pepper->onDisplay($paneName, $tabs[$active_tab]);
				$this->logBenchmark('}');
				
				if (strpos($pane_html, '<ul class="filters">'))
				{
					$scroll_class .= ' scroll-filters';
				}
				if (strpos($pane_html, 'class="search-form"'))
				{
					$scroll_class .= ' scroll-form';
				}
				$html .= ($this->cfg['preferences']['fixHeight'])?"<div class=\"$scroll_class\">$pane_html</div>\r":$pane_html;
				
				$html .= "</div>\r";
				$html .= "</div>\r"; // content-container
				$html .= "\t<div class=\"footer\">\r";
				$html .= "\t\t<div></div>\r";
				$html .= "\t</div>\r";
				$html .= "</div>\r";
			}
		}
		
		$html .= '</div>';
		
		$header  = preg_replace('/ &nbsp; $/','',$header);
		$header .= "\r\t\t\t\t</span>\r\t\t\t</div>\r\t\t\t<ul id=\"page-list\" class=\"pages\">\r";
		if ($this->cfg['mode'] != 'client' || $this->isLoggedIn())
		{
			$header .= "\t\t\t\t<li class=\"first-child\"><a href=\"?preferences\">Preferences</a></li>\r";
			$header .= "\t\t\t\t<li class=\"last-child\"><a href=\"?logout\">Logout</a></li>\r";
		}
		else
		{
			$header .= "\t\t\t\t<li class=\"only-child\"><a href=\"?preferences\">Login</a></li>\r";
		}
		
		$header .= "\t\t\t</ul>\r\t\t</div>\r";
		$header .= "\t</div>\r";
		$header .= "</div>\r\r";
		
		$js  = preg_replace('/,$/','',$js);
		$js .= "];\r";
		$js .= "// ]]>\r";
		$js .= "</script>\r";
		
		$after = '';
		foreach($this->pepper as $pepper)
		{
			$after .= $pepper->onAfterDisplay();
		}
		
		if (!empty($this->errors['list'])) 
		{
			$header .= "<div class=\"notice\">".$this->getFormattedErrors()."</div>";
		}
		
		$html = $header.$html.$js.$after;
		
		$this->logBenchmark('}');
		return $html;
	}
	
	/**************************************************************************
	 displayTab()
	 
	 Returns just the contents of the requested tab
	 **************************************************************************/
	function displayTab()
	{
		if (isset($_GET['pane_id']) && isset($_GET['tab']))
		{
			$this->_saveIEFromItself("Grow up you POS browser.");
			
			$html  = "<div class=\"content-container\">\r";
			
			$paneId 	= $_GET['pane_id'];
			$tab		= $_GET['tab'];
			
			$pepperId	= $this->cfg['panes'][$paneId]['pepperId'];
			$paneName	= $this->cfg['panes'][$paneId]['name'];
			$pepper		=& $this->pepper[$pepperId];
			
			$scroll_class = (in_array($paneName, $pepper->oddPanes)) ? "scroll-inline" : "scroll";
			$pane_html = $pepper->onDisplay($paneName, $tab);
			
			if (strpos($pane_html, '<ul class="filters">'))
			{
				$scroll_class .= ' scroll-filters';
			}
			if (strpos($pane_html, 'class="search-form"'))
			{
				$scroll_class .= ' scroll-form';
			}
			if ($this->cfg['preferences']['fixHeight']) 
			{
				$html .= "<div class=\"$scroll_class\">$pane_html</div>";
			}
			else
			{
				$html .= $pane_html;
			}
			
			$html .= "</div>";
			
			return $html;
		}
	}
	
	/**************************************************************************
	 _saveIEFromItself()
	 
	 Prevents IE from exploding when fixed height panes are enabled.
	 By disabling them. How quaint.
	 **************************************************************************/
	function _saveIEFromItself($witticism = '')
	{
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$ua = $_SERVER['HTTP_USER_AGENT'];
			
			// This detection will probably win the ire of some fringe browser user
			// I'm sure this would be a lot more amusing if I were drunk
			// Stay in school kids
			if (strpos($ua, 'MSIE') && strpos($ua, 'Opera') === false)
			{
				$this->cfg['preferences']['fixHeight'] = 0;
			}
		}
	}
	
	/**************************************************************************
	 preferences()
	 
	 Returns the HTML string that displays all the Pepper-specific preferences
	 **************************************************************************/
	function preferences() 
	{
		if (!$this->db['connected']) 
		{ 
			echo "Could not connect to the database\r";
			return;
		}
				
		$header  = "<ul class=\"tabs\">";
		$html = '';
		
		$first = true;
		$preconfigure = (isset($_COOKIE['MintConfigurePepper'])) ? true : false;
		foreach ($this->pepper as $pepperId=>$pepper) 
		{
			$configure = ($preconfigure && $_COOKIE['MintConfigurePepper'] == $pepperId) ? true : false;
			
			$header .= "<li".((($first && !$preconfigure) || $configure)?' class="active"':'')."><a href=\"#pepper-$pepperId\">{$pepper->info['pepperName']}</a></li>";
			
			$html .= "<div id=\"pepper-$pepperId\" class=\"pepper-prefs\">\r";
			$html .= "\t<h3><a href=\"{$pepper->info['pepperUrl']}\">{$pepper->info['pepperName']} Pepper</a></h3>\r";
			$html .= "\t<h4>Version <a href=\"{$pepper->info['pepperUrl']}\">".$pepper->getFormattedVersion()."</a> by <a href=\"{$pepper->info['developerUrl']}\">{$pepper->info['developerName']}</a>";
			if (!empty($pepper->info['additionalDevelopers']))
			{
				foreach($pepper->info['additionalDevelopers'] as $developerName => $developerUrl)
				{
					$html .= " &amp; <a href=\"{$developerUrl}\">{$developerName}</a>";
				}
			}
			$html .= "</h4>\r";
			// Description
			if (!empty($pepper->info['pepperDesc'])) 
			{
				$html .= "\t<p class=\"desc\">{$pepper->info['pepperDesc']}</p>\r";
			}
			
			// Uninstall button
			if ($pepper->info['pepperName']!="Default")
			{
				$html .= "\t<a href=\"#Uninstall\" class=\"uninstall\" onclick=\"SI.Mint.uninstallPepper('{$pepper->info['pepperName']}','$pepperId'); return false;\"><img src=\"styles/{$this->cfg['preferences']['style']}/images/btn-uninstall.png\" alt=\"Uninstall\" width=\"62\" height=\"22\" /></a>";
			}
			
			// Pane preferences
			$preferences = $pepper->onDisplayPreferences();
			if (is_array($preferences)) 
			{
				foreach ($preferences as $preferenceName => $preference)
				{
					if (!empty($preference))
					{
						$html .= "\t<h5>$preferenceName</h5>\r";
						$html .= "\t<fieldset>{$preference}</fieldset>\r";
					}
				}
			}
			
			// If this Pepper has any tables in need of moderation
			// provide the appropriate preferences
			if (!empty($pepper->moderate))
			{
				foreach($pepper->moderate as $pepperTableName)
				{
					$html .= $this->databaseTablePreferences($pepper->info['pepperName'].' '.ucwords($pepperTableName).' Table', $pepperTableName);
				}
			}
			
			$html .= "</div>\r"; // pepper-prefs div
			
			$first = false;
		}
		
		$uninstalledPepper = $this->getPathsToPepper();
		$uninstalled = '';
		if (!empty($uninstalledPepper))
		{
			foreach ($uninstalledPepper as $pathToPepper)
			{
				include_once(MINT_ROOT.$pathToPepper);
				if (isset($installPepper))
				{
					$pepper = new $installPepper(NULL);
					
					$compatibility = $pepper->isCompatible();
					
					$item  = "<h3><a href=\"{$pepper->info['pepperUrl']}\">{$pepper->info['pepperName']} Pepper</a></h3>\r";
					$item .= "<h4>Version <a href=\"{$pepper->info['pepperUrl']}\">".$pepper->getFormattedVersion()."</a> by <a href=\"{$pepper->info['developerUrl']}\">{$pepper->info['developerName']}</a>";
					if (!empty($pepper->info['additionalDevelopers']))
					{
						foreach($pepper->info['additionalDevelopers'] as $developerName => $developerUrl)
						{
							$item .= " &amp; <a href=\"{$developerUrl}\">{$developerName}</a>";
						}
					}
					$item .= "</h4>\r";
					
					if (isset($compatibility['explanation']) && !empty($compatibility['explanation']))
					{
						$item .= '<div class="explanation">'.$compatibility['explanation'].'</div>';
					}
					
					$item .= "<p class=\"desc\">{$pepper->info['pepperDesc']}</p>\r";
					
					if ($compatibility['isCompatible'])
					{
						$item .= "<a href=\"#Install\" class=\"install\" onclick=\"SI.Mint.installPepper('$pathToPepper'); return false;\"><img src=\"styles/{$this->cfg['preferences']['style']}/images/btn-install.png\" alt=\"Install\" width=\"62\" height=\"22\" /></a>";
					}
					
					$data['items'][] = $item;
					unset($installPepper,$pepper);
				}
			}
			
			if (isset($data['items'])) 
			{
				$uninstalled .= $this->generateUnorderedList($data);
			}
		}
		
		if (!empty($uninstalled)) 
		{
			$html .= "<div id=\"pepper-uninstalled\" class=\"pepper-prefs\">";
			$html .= $uninstalled;
			$html .= "</div>\r";
		
			$header .= '<li'.(($first)?' class="active"':'').'><a href="#pepper-uninstalled">Install</a></li>';
		}
		
		$header  = preg_replace('/ &nbsp; $/','',$header)."</ul>\r";
		$html = $header.$html;
				
		return $html;
	}
	
	/**************************************************************************
	 databaseTablePreferences()
	 
	 **************************************************************************/
	function databaseTablePreferences($name = 'Mint Database', $table = 'visit')
	{
		$html = '';
		
		if (isset($_GET['advanced']))
		{
			$sizeDisplay	= $this->getFormattedTableSize($table);
			$expiry			= (isset($this->cfg['preferences']['expiry'][$table])) ? $this->cfg['preferences']['expiry'][$table] : 5;
			$maxSize		= (isset($this->cfg['preferences']['maxSize'][$table])) ? $this->cfg['preferences']['maxSize'][$table] : 25;
		
			$html .= <<<HTML
		<h5>{$name} (Current size: {$sizeDisplay})</h5>
		<fieldset>
			<table class="snug">
				<tr>
					<td>Remove {$table} data more than </td>
					<td><span class="inline"><input type="text" id="expiry-{$table}" name="expiry[{$table}]" maxlength="4" value="{$expiry}" class="cinch" /></span></td>
					<td>weeks old</td>
				</tr>
			</table>
			<table class="snug">
				<tr>
					<td>Attempt to keep {$table} table size under </td>
					<td><span class="inline"><input type="text" id="maxSize-{$table}" name="maxSize[{$table}]" maxlength="4" value="{$maxSize}" class="cinch" /></span></td>
					<td>MB</td>
				</tr>
			</table>
		</fieldset>
HTML;
		}
		
		return $html;
	}
	
	/**************************************************************************
	 savePreferences()
	 
	 **************************************************************************/
	function savePreferences() 
	{
		foreach ($this->pepper as $pepperId => $pepper) 
		{
			$pepper->onSavePreferences();
			$this->cfg['preferences']['pepper'][$pepperId] = $pepper->prefs;
		}
		
		if (!isset($_POST['advanced']))
		{
			// Need to validate input, encode HTML entities, etc
			$save = array('siteDisplay','siteDomains','installFull','offset','email','password');
			foreach ($save as $cfgName)
			{
				if (isset($_POST[$cfgName])) 
				{
					if ($cfgName == 'installFull')
					{
						$_POST[$cfgName] = preg_replace('/\/$/', '', $_POST[$cfgName]);
					}
					$this->cfg[$cfgName] = stripslashes($_POST[$cfgName]);
				}
			}
			
			// Update domain list
			$this->_reloadDomains();
			$this->cfg['mode'] = (isset($_POST['mode']))?$_POST['mode']:'default';
			
			$this->cfg['preferences']['rows'] 	   			= round($_POST['rows']);
			$this->cfg['preferences']['rssRows']   			= $_POST['rss_rows'];
			$this->cfg['preferences']['style']   			= $_POST['style'];
			$this->cfg['preferences']['secondary'] 			= (isset($_POST['secondary']))?1:0;
			$this->cfg['preferences']['fixHeight'] 			= (isset($_POST['fix_height']))?1:0;
			$this->cfg['preferences']['collapseVert'] 		= (isset($_POST['collapse_vert']))?1:0;
			$this->cfg['preferences']['singleColumn']		= (isset($_POST['single_column']))?1:0;
			$this->cfg['preferences']['singleColumnWidth']	= round($_POST['single_column_width']);
			$this->cfg['preferences']['stripeAll']			= (isset($_POST['stripe_all']))?1:0;
			$this->cfg['preferences']['staggerPanes']		= (isset($_POST['stagger_panes']))?1:0;
		}
		else
		{
			// clean the database settings for individual tables
			foreach($this->cfg['moderate'] as $table => $tablePepperId)
			{
				if (isset($_POST['expiry'][$table]))
				{
					$this->cfg['preferences']['expiry'][$table] = ($_POST['expiry'][$table] > 1 / 7) ? $_POST['expiry'][$table] : 1 / 7;
				}

				if (isset($_POST['maxSize'][$table]))
				{
					$this->cfg['preferences']['maxSize'][$table] = (float) $_POST['maxSize'][$table];
				}
			}
			$this->cfg['preferences']['ignoreIPsMint']	= $this->escapeSQL(preg_replace('/[\s,]+/', ' ', $_POST['ignoreIPsMint']));
			$ips = explode(' ', $this->cfg['preferences']['ignoreIPsMint']);
			$ipLongs = array();
			foreach($ips as $ip)
			{
				if (preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $ip))
				{
					$ipLongs[] = ip2long($ip);
				}
				else if (preg_match('/^(\d{1,3}(-\d{1,3})?|\*)(\.(\d{1,3}(-\d{1,3})?|\*)){0,3}$/', $ip))
				{
					$ipParts = explode('.', $ip);
					for ($i = 0; $i < 4; $i++)
					{
						if (!isset($ipParts[$i]) || $ipParts[$i] == '*')
						{
							$ipParts[$i] = '0-255';
						}
					}
					$ipRange 		= join('.', $ipParts);
					$ipRangeBottom	= preg_replace('/-\d{1,3}/', '', $ipRange);
					$ipRangeTop		= preg_replace('/\d{1,3}-/', '', $ipRange);

					$ipLongs[] = array(ip2long($ipRangeBottom), ip2long($ipRangeTop));
				}
			}
			$this->cfg['preferences']['ignoredIPsLong'] = $ipLongs;
		}
		
		if (isset($_POST['pane_order'])) 
		{
			$enabledOrder	= array();
			$disabledOrder	= array();
			
			$order = array_unique(explode(",",$_POST['pane_order']));
			foreach ($order as $paneId) 
			{
				if ($_POST["pane{$paneId}enabled"])
				{
					$enabledOrder[] = $paneId;
				}
				else
				{
					$disabledOrder[] = $paneId;
				}
			}
			$this->cfg['preferences']['paneOrder']['enabled']	= $enabledOrder;
			$this->cfg['preferences']['paneOrder']['disabled']	= $disabledOrder;
		}
		$this->_tidySave();
		$this->_setLoginCookie();
	}
	
	
	/**************************************************************************
	 rss()
	
	 **************************************************************************/
	 function rss()
	 {	
	 	$rss 				= '<?xml version="1.0" encoding="utf-8"?'.">\r";
		$rssData			= array();
		$rssData['items']	= array();
		
		if (isset($_GET['pepper']))
		{
			$pepperId = $_GET['pepper'];
			if (isset($this->pepper[$pepperId]))
			{
				$rssData = $this->pepper[$pepperId]->onRss();
			}
		}
		
		if (empty($rssData['title']))
		{
			$rssData['title'] = "Mint (on {$this->cfg['siteDisplay']})";
		}
		else
		{
			$rssData['title'] = "Mint: {$rssData['title']} (on {$this->cfg['siteDisplay']})";
		}
		
		if (empty($rssData['description']))
		{
			$rssData['description'] = "A Fresh Look at {$this->cfg['siteDisplay']}";
		}
		
		$copyright	= "Copyright 2004-".date("Y");
	 	$date		= gmdate("D, d M Y H:i:s")." GMT";
		
		$rss .= <<<HERE
<rss version="2.0">
	<channel>
		<title>{$rssData['title']}</title>
		<link>{$this->cfg['installFull']}/</link>
		<description>{$rssData['description']}</description>
		<copyright>$copyright  Shaun Inman</copyright>
		<generator>http://www.haveamint.com/?v={$this->version}</generator>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<lastBuildDate>$date</lastBuildDate>

HERE;
	
		foreach($rssData['items'] as $item)
		{
			$title 	= str_replace('&', '&amp;', $item['title']);
			$link 	= str_replace('&', '&amp;', $item['link']);
			$date	= gmdate("D, d M Y H:i:s", $item['date'])." GMT";
			
			$rss .= <<<HERE
		<item>
			<title>{$title}</title>
			<description><![CDATA[{$item['body']}]]></description>
			<link>{$link}</link>
			<pubDate>{$date}</pubDate>
		</item>

HERE;
		}

		$rss .= <<<HERE
	</channel>
</rss>

HERE;
		return $rss;
	 }
	 
	/**************************************************************************
	 custom()
	
	 This function allows plug-ins to handle custom events. This function is
	 called if $_REQUEST['MintPath'] is set to "custom". The onCustom event handler 
	 should expect additional $_POST and/or $_GET variables to branch as needed.
	 
	 Return value is optional.
	 **************************************************************************/
	function custom() 
	{
		foreach ($this->pepper as $pepper) 
		{
			$pepper->onCustom();
		}
		
		$this->_save();
	}
	
	/**************************************************************************
	 widget()
	
	 **************************************************************************/
	function widget()
	{
		$html = '';
		
		if ($this->authenticate()) 
		{
			foreach ($this->pepper as $pepper) 
			{
				$html .= $pepper->onWidget();
			}
			$html = "<a id=\"mint\" href=\"{$this->cfg['installFull']}/\">Mint</a><div id=\"fresh\">A Fresh Look at <a href=\"http://{$this->cfg['installDomain']}\">{$this->cfg['siteDisplay']}</a></div>$html";
			
			$updatesAvailable = $this->_updatesAvailable();
			if ($updatesAvailable && !$this->cfg['update']['dismissed'])
			{
				$html .= '<a href="'.$this->cfg['installFull'].'/?uptodate" id="update-available"><span>Update'.(($updatesAvailable > 1) ? 's' : '').' Available</span></a>';
			}
		}
		else
		{
			$errors = $this->errors['list'];
			$html .= '<!-- Mint Error --><div id="fresh">'.implode(' ', $errors).'</div><div id="visits-list"><div class="total">Error</div></div>';
		}
		
		$html = "<!-- Mint 2 Request Received -->$html";
		return $html;
	}
	
	/**************************************************************************
	 isLoggedIn()
	 
	 Returns false if the user isn't logged in.
	 **************************************************************************/
	function isLoggedIn() 
	{
		return (isset($_COOKIE['MintAuth']) && $_COOKIE['MintAuth']==md5($this->cfg['password']))?true:false; 
	}
	
	/**************************************************************************
	 authenticate()
	 
	 Handles login/logout/password reminders
	 **************************************************************************/
	function authenticate() 
	{
		if (isset($_POST['action']) && $_POST['action']=="Login") 
		{
			if ($_POST['email']==$this->cfg['email'] && $_POST['password']==$this->cfg['password']) 
			{
				$this->_setLoginCookie();
				return true;
			}
			else 
			{ 
				$this->logError('Email/password combo is incorrect.'); 
			}
		}
		else if (isset($_POST['action']) && $_POST['action']=="Send Password") 
		{
			if ($_POST['emailReminder']==$this->cfg['email']) 
			{
				$to		= $this->cfg['email'];
				$subj	= 'Your Mint Password';
				$msg	= 'Password: '.$this->cfg['password'];
				$msg   .= "\n\n\nThank you for using Mint";
				$msg   .= "\n.............................";
				$msg   .= "\n".$this->cfg['installFull'];
				$from 	= 'From: Mint <mint@'.$this->cfg['installTrim'].'>';
				
				mail($to, $subj, $msg, $from);
				$this->logError('Password sent.');
			}
			else 
			{
				$this->logError('Incorrect email.');
			}
		}
		return false;
	}
	
	/**************************************************************************
	 authenticateRSS()
	 
	 **************************************************************************/
	function authenticateRSS() 
	{
		return (isset($_GET['RSS']) && $_GET['RSS'] == md5($this->cfg['password']))?true:false;
	}
	
	/**************************************************************************
	 logout()
	 
	 **************************************************************************/
	function logout() 
	{
		$this->bakeCookie('MintAuth', '', (time() - (60 * 60 * 24 * 365)));
		unset($_COOKIE['MintAuth']);
	}
	
	/**************************************************************************
	 _setLoginCookie()
	 
	 **************************************************************************/
	function _setLoginCookie() 
	{
		$this->bakeCookie('MintAuth', md5($this->cfg['password']), (time() + ( 60 * 60 * 24 * 365)));
		$_COOKIE['MintAuth'] = md5($this->cfg['password']);
	}
	
	/**************************************************************************
	 bakeCookie()
	 
	 A proxy for PHP's setcookie function that tries to ensures the most
	 compatible domain name
	 **************************************************************************/
	function bakeCookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false)
	{
		// Don't use a cfg value for the domain, will break mirrored sites
		$currentDomain = preg_replace('/(^www\.|:\d+$)/', '', (!empty($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!=$_SERVER['SERVER_NAME'])?$_SERVER["HTTP_HOST"]:$_SERVER['SERVER_NAME']);
		if (!preg_match('/(?:\d{1,3}\.){3}\d{1,3}/', $currentDomain) && $currentDomain != 'localhost')
		{
			$domain = '.'.$currentDomain; // add . to allow subdomains of this domain to share this cookie
		}
		$this->logBenchmark('bakeCookie('."{$name}, {$value}, {$expire}, {$path}, {$domain}, {$secure}".') {');
		$this->logBenchmark('}');
		return setcookie($name, $value, $expire, $path, $domain, $secure);
	}
	
	/**************************************************************************
	 detectDocumentRoot() [private]
	 
	 **************************************************************************/
	function detectDocumentRoot()
	{
		$candidates = array();
		
		$candidates[] = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT']:'';
		$candidates[] = preg_replace('/\/$/', '', str_replace($this->cfg['installDir'], '', realpath('.')));
		$candidates[] = (isset($_SERVER['PATH_TRANSLATED'], $_SERVER['SCRIPT_NAME'])) ? substr($_SERVER['PATH_TRANSLATED'], 0, strlen($_SERVER['SCRIPT_NAME']) * -1) : '';
		$candidates[] = (isset($_SERVER['SCRIPT_FILENAME'])) ? substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], $this->cfg['installDir'])) : '';
		
		foreach ($candidates as $candidate)
		{
			$pathToMint = $candidate.$this->cfg['installDir'].'/index.php';
			if (file_exists($pathToMint))
			{
				return $candidate;
			}
		}
		return false;
	}
	
	/**************************************************************************
	 getPepperByClassName()
	 
	 Returns a reference to the named Pepper
	 **************************************************************************/
	function &getPepperByClassName($className)
	{
		$null = false;
		
		if (isset($this->cfg['pepperLookUp'][$className]))
		{
			return $this->pepper[$this->cfg['pepperLookUp'][$className]];
		}
		else
		{
			return $null;
		}
	}
	
	/**************************************************************************
	 _reloadDomains() [private]
	
	 Reloads the domains from siteDomains, stripping invalid url parts and then
	 normalizes siteDomains
	 **************************************************************************/
	function _reloadDomains()
	{
		$this->domains = preg_split('/[\s,]+/', $this->cfg['siteDomains']);
		foreach($this->domains as $key => $domain)
		{
			$this->domains[$key] = preg_replace('#(^(https?://)?(www.)?|(:\d+)?/.*$)#i', '', $domain);
		}
		$this->cfg['siteDomains'] = implode(', ', $this->domains);
	}
	
	/**************************************************************************
	 logError()
	 
	 Keeps track of errors
	 **************************************************************************/
	function logError($error, $level = 0)
	{
		if ($level == 2)
		{
			$this->errors['fatal'] = true;
		}
		
		if ($level) // higher priority, add to the beginning of the errors list
		{
			array_unshift($this->errors['list'], $error);
		}
		else
		{
			$this->errors['list'][] = $error;
		}
	}
	
	/**************************************************************************
	 dropError()
	 
	 Remove all errors containing the passed string or the most recent error
	 if empty
	 **************************************************************************/
	function dropError($containing = '')
	{
		if (empty($containing))
		{
			array_pop($this->errors['list']);
		}
		else
		{
			foreach ($this->errors['list'] as $i => $error)
			{
				if (strpos($error, $containing) !== false)
				{
					unset($this->errors['list'][$i]);
					$this->errors['list'] = $this->array_reindex($this->errors['list']);
				}
			}
		}
	}
	
	/**************************************************************************
	 hasError()
	 
	 Whether or not a certain error has been logged already.
	 **************************************************************************/
	function hasError($containing)
	{
		foreach ($this->errors['list'] as $error)
		{
			if (strpos($error, $containing) !== false)
			{
				return true;
			}
		}
		return false;
	}
	
	/**************************************************************************
	 logErrorNote()
	 
	 Keeps track of a descriptive error note
	 **************************************************************************/
	function logErrorNote($note)
	{
		$this->errors['note'] = $note;
	}
	
	/**************************************************************************
	 logFeedback()
	 
	 The $destination argument is optional. Leave blank to redirect to the
	 current url.
	 **************************************************************************/
	function logFeedback($feedback, $button = '', $destination = '')
	{
		$this->feedback = array
		(
			'button'	=> $button,
			'feedback'		=> $feedback,
			'destination'	=> $destination
		);
	}
	
	/**************************************************************************
	 logBenchmark()
	 
	 **************************************************************************/
	function logBenchmark($event)
	{
		$this->bench[] = array
		(
			$event,
			$this->microtime_float()
		);
	}
	
	/**************************************************************************
	 safeUnserialize()
	 
	 Tries to unserialze an array/object. If broken it attempts to repair.
	 **************************************************************************/
	function safeUnserialize($serialized)
	{
		$serialized		= stripslashes($serialized);
		$unserialized	= @unserialize($serialized);
		if ($unserialized === false)
		{
			$serialized		= preg_replace('/s:\d+:"([^"]*)";/e', "'s:'.strlen('\\1').':\"\\1\";'", $serialized);
			$unserialized	= @unserialize(stripslashes($serialized));
		}
		
		return $unserialized;
	}
	
	/**************************************************************************
	 generateKey()
	 
	 Turns the current time into a seemingly random string of numbers
	 **************************************************************************/
	function generateKey() 
	{
		// Get current time
		$key = time();
		// Add random characters to the key
		$s = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		for ($i=0; $i<10; $i++ ) 
		{
			$r = '';
			$j = rand(0,3);
			for ($k=0; $k<$j; $k++) 
			{
				$r .= $s[rand(0,strlen($s)-1)];
			}
			$l = rand(0,strlen($key));
			$key = substr($key,0,$l).$r.substr($key,$l,strlen($key));
		}		
		// Reverse the key
		$key = strrev($key);
		// Hex the key
		$key = bin2hex($key);	
		return $key;
	}

	/**************************************************************************
	 verifyKey()
	 
	 Analyzes the key passed back from the JavaScript to make sure it's valid
	 **************************************************************************/
	function verifyKey() 
	{
		// make sure a key is set
		if (!isset($_GET['key'])) { return false; }
		$key = $_GET['key'];
		// Decode the hex
		$key = preg_replace("/(.{2})/","%$1",$key);
		$key = rawurldecode($key);
		// Reverse the key
		$key = strrev($key);
		// Strip the random characters
		$key = preg_replace("/([a-z])/i",'',$key);
		
		// If this key is from the past 10 seconds we consider it valid
		// might need to increase depending on bandwidth conditions...
		return ((time()-$key) < 10)?true:false;
	}
	
	/**************************************************************************
	 verifyDomain()
	 
	 Ensures Mint only records hits from listed site domains.
	 **************************************************************************/
	function verifyDomain()
	{
		# Causing problems for people tracking mirrored domains and those with Mint installed on a sub- or other domain
		return true;
		# $domain = (isset($_SERVER['HTTP_REFERER']))?preg_replace('#(^(https?://)?(www\.)?|(:\d+)?/.*$)#i', '', $_SERVER['HTTP_REFERER']):'';
		# return (preg_match("/^([a-z0-9]+[\._-])*(".str_replace('.', '\.', implode('|', $this->domains)).")/i", $domain))?true:false;
	}
	
	/**************************************************************************
	 generateCSSClassName()
	 
	 Based on the current numbers relation to the total, this script determines 
	 an elements relationshipt to it's siblings. Additional classNames may be 
	 provided via the optional `$className` param.
	 **************************************************************************/
	function generateCSSClassName($num, $total, $className = '') 
	{
		$className .= ' ';
		$className .= ($total==1)?'only-child ':'';
		$className .= ($num==0&&$total!=1)?'first-child ':'';
		$className .= ($num%2)?'alt ':'';
		$className .= ($num==$total-1 && $total!=1)?'last-child':'';
		$className = trim($className);
		$className = (!empty($className))?" class=\"$className\"":'';
		return $className;
	}
	
	/**************************************************************************
	 generateUnorderedList()
	 
	 Takes a specially formatted multi-dimensionally associative array and
	 returns and HTML string containing an unordered list with the necessary
	 CSS class names.
	 **************************************************************************/
	function generateUnorderedList($data)
	{
		$html = "<ul";
		if (isset($data['list']))
		{
			if (isset($data['list']['id']) && !empty($data['list']['id']))
			{
				$html .= " id=\"{$data['list']['id']}\"";
			}
			if (isset($data['list']['class']) && !empty($data['list']['class']))
			{
				$html .= " class=\"{$data['list']['class']}\"";
			}
		}
		$html .= ">\r";
		
		if (isset($data['items']))
		{
			$c = count($data['items']);
			for ($i=0; $i<$c; $i++) 
			{
				$html .= "\t<li".$this->generateCSSClassName($i,$c).">{$data['items'][$i]}</li>\r";
			}
		}
		
		$html .= "</ul>\r";
		return $html;
	}
	
	/**************************************************************************
	 generateTable()
	 
	 Takes a specially formatted multi-dimensionally associative array and
	 returns and HTML string containing a well-formed table with the necessary
	 CSS class names.
	 **************************************************************************/
	function generateTable($data) 
	{
		
		$hasFolders = (isset($data['hasFolders']) && $data['hasFolders']===true)?true:false;
		
		$focussed = false;
		$tableClasses = array();
		if ($this->cfg['preferences']['stripeAll'])
		{
			$tableClasses[] = 'striped';
		}
		/** /
		// If a sort column follows a focus column, stripe all rows
		else if (isset($data['thead']))
		{
			foreach($data['thead'] as $column)
			{
				if ($column['class'] == 'focus')
				{
					$focussed = true;
					continue;
				}
				
				if ($column['class'] == 'sort' && $focussed)
				{
					$tableClasses[] = 'striped';
				}
			}
		}
		/**/
		
		// Open the table
		$html = "<table cellspacing=\"0\"";
		if (isset($data['table']))
		{
			if (isset($data['table']['id']) && !empty($data['table']['id']))
			{
				$html .= " id=\"{$data['table']['id']}\"";
			}
			if (isset($data['table']['class']) && !empty($data['table']['class']))
			{
				$tableClasses[] = $data['table']['class'];
			}
		}
		$html .= (!empty($tableClasses)) ? ' class="'.join(' ', $tableClasses).'"' : '';
		$html .= ">\r";
		
		
		// Create the thead
		if (isset($data['thead'])) 
		{
			$html .= "\t<thead>\r\t\t<tr>\r";
			$c = count($data['thead']);
			for ($i=0; $i<$c; $i++) 
			{
				$html .= "\t\t\t<th".$this->generateCSSClassName($i,$c,$data['thead'][$i]['class']).">{$data['thead'][$i]['value']}</th>\r";
			}
			$html .= "\t\t</tr>\r\t</thead>\r";
		}
		
		// Create the tbody
		if (isset($data['tbody'])) 
		{
			$html .= "\t<tbody>\r";
			$d = count($data['tbody']);
			
			// Rows
			for ($j=0;$j<$d;$j++) 
			{
				if ($hasFolders) 
				{
					$args = array();
					foreach ($data['tbody'][$j]['folderargs'] as $key=>$value) 
					{
						$args[] = "$key=$value";
					}
					unset($data['tbody'][$j]['folderargs']);
					$query = implode('&',$args);
					$onclick = " onclick=\"SI.Mint.toggleFolder(this,'{$this->cfg['installDir']}/?MintPath=Custom&$query');\"";
				}
				
				$rowClass = '';
				if (isset($data['tbody'][$j]['class']))
				{
					$rowClass = $data['tbody'][$j]['class'];
					unset($data['tbody'][$j]['class']);
				}
				
				$html .= "\t\t<tr".$this->generateCSSClassName($j,$d,(($hasFolders)?'folder ':'').$rowClass).(($hasFolders)?$onclick:'').">\r";
				// Columns
				$e = count($data['tbody'][$j]);
				for ($k=0;$k<$e;$k++) 
				{ 
					$class = (isset($data['thead'][$k]['class']))?$data['thead'][$k]['class']:'';
					$f = (preg_match("/^[0-9]{4,}$/",$data['tbody'][$j][$k]))?number_format($data['tbody'][$j][$k]):$data['tbody'][$j][$k];
					$html .= "\t\t\t<td".$this->generateCSSClassName($k,$e,$class).">$f</td>\r"; 
				}
				
				$html .= "\t\t</tr>\r";
				
				if ($hasFolders) 
				{
					$html .= "\t</tbody><tbody class=\"folder-contents\">";
					for ($k = 0; $k < $e; $k++) 
					{
						$html .= "<td></td>";
					}
					$html .= "</tbody>\r";
					$html .= "\t<tbody>\r";
				}
			}
			$html .= "\t</tbody>\r";
		}
		
		// Close the table
		$html .= "</table>\r";
		return $html;
	}
	
	/**************************************************************************
	 generateInfoRow()
	 
	 Takes a specially formatted associative array and returns and HTML string 
	 containing a well-formed, nested Additional Info table with appropriate 
	 CSS class names. Should only be used as a lead-in row of an expanded 
	 accordion/folder view.
	 **************************************************************************/
	function generateInfoRow($data, $tableColumnCount = 3, $leadColumns = 1)
	{
		$html = '';
		if (!empty($data))
		{
			$html .= '<tr class="additional-info">';
			if ($leadColumns)
			{
				$colspan = ($leadColumns > 1) ? ' colspan="'.$leadColumns.'"': '';
				$html .= '<td'.$colspan.'>&nbsp;</td>';
			}
			$infoColumns = $tableColumnCount - $leadColumns;
			$colspan = ($infoColumns > 1) ? ' colspan="'.$infoColumns.'"': '';
			$html .= '<td'.$colspan.' class="additional-info-content">';
			$html .= '<table class="snug">';
			
			foreach($data as $label => $content)
			{
				$html .= '<tr><th>'.$label.'</th><td>'.$content.'</td></tr>';
			}
			
			$html .= '</table>';
			$html .= '</td></tr>';
		}
		return $html;
	}
	
	/**************************************************************************
	 generateTableRows()
	 
	 Takes a specially formatted multi-dimensionally associative array and
	 returns and HTML string containing a well-formed group of table rows with 
	 relative CSS class names.
	 **************************************************************************/
	function generateTableRows($data) 
	{
		$html = '';
		$d = count($data['tbody']);
		for ($j=0; $j < $d; $j++) 
		{
			$rowClass = '';
			if (isset($data['tbody'][$j]['class']))
			{
				$rowClass = $data['tbody'][$j]['class'];
				unset($data['tbody'][$j]['class']);
			}
			
			$html .= "\t\t<tr".$this->generateCSSClassName($j, $d, $rowClass).">\r";
			// Columns
			$e = count($data['tbody'][$j]);
			for ($k=0;$k<$e;$k++) 
			{ 
				$f = (preg_match("/^[0-9]{4,}$/",$data['tbody'][$j][$k]))?number_format($data['tbody'][$j][$k]):$data['tbody'][$j][$k];
				$html .= "\t\t\t<td".$this->generateCSSClassName($k, $e, $data['classes'][$k]).">$f</td>\r"; 
			}
			$html .= "\t\t</tr>\r";
		}
		return $html;
	}
	
	/**************************************************************************
	 generateRSSLink()
	 
	 Take a Pepper ID, a descriptive title of what the feed provides, and an 
	 associative array thats values map to any additional query string name/value 
	 pairs needed to generate the feed. Returns a link to that feed.
	 **************************************************************************/
	function generateRSSLink($pepper_id, $title = '', $additional = array()) 
	{
		$add = '';
		foreach ($additional as $query=>$value) 
		{
			$add .= "&amp;$query=$value";
		}
		if ($title)
		{
			if (!preg_match("/ feed$/i",$title))
			{
				$title .= ' Feed';
			}
			$title = " title=\"$title\"";
		}
		return (!$this->isLoggedIn())?'':"<a href=\"{$this->cfg['installDir']}/?RSS=".md5($this->cfg['password'])."&amp;pepper=$pepper_id$add\" class=\"rss feed-btn snap_nopreview\"$title><span>Feed</span></a>";
	}
	
	/**************************************************************************
	 generatePaneOrderList()
	 
	 Returns an HTML string for a sortable list of enabled/disabled panes.
	 **************************************************************************/
	function generatePaneOrderList() 
	{		
		$paneIds = array();
		$hidden  = "<input type=\"hidden\" id=\"pane_order\" name=\"pane_order\" value=\"";
		$html 	 = "<dl class=\"sortable\">\r";
		$html 	.= "\t<dt>Enabled</dt>\r";
		
		$paneTemplate = "\t\t<dd id=\"pepper-%5\$d-pane-%2\$d\"%3\$s><span>%1\$s</span><input type=\"hidden\" value=\"%2\$d\" /><input type=\"hidden\" id=\"pane%2\$denabled\" name=\"pane%2\$denabled\" value=\"%4\$d\" /></dd>\r";

		foreach ($this->cfg['preferences']['paneOrder']['enabled'] as $paneId)
		{
			$paneName	= $this->cfg['panes'][$paneId]['name'];
			$pepperId	= $this->cfg['panes'][$paneId]['pepperId'];
			$paneIds[]	= $paneId;
			$html 	.= sprintf($paneTemplate, $paneName, $paneId, '', 1, $pepperId);
		}
		
		$html .= "\t<dt id=\"disable\">Disabled <span>(Drag panes below to disable)</span></dt>\r";
		
		foreach ($this->cfg['preferences']['paneOrder']['disabled'] as $paneId)
		{
			$paneName	= $this->cfg['panes'][$paneId]['name'];
			$pepperId	= $this->cfg['panes'][$paneId]['pepperId'];
			$paneIds[]	= $paneId;
			$html 	.= sprintf($paneTemplate, $paneName, $paneId, ' class="disabled"', 0, $pepperId);
		}
		
		$hidden .= implode(',', $paneIds);
		$hidden	.= "\" />\r";
		$html	.= "</dl>\r";
		return $hidden.$html;
	}
	
	/**************************************************************************
	 generateStyleOptions()
	 
	 **************************************************************************/
	function generateStyleOptions()
	{
		$html			= '';
		$pathToStyles	= 'styles/';
		
		// Check for the style diretory
		if (@is_dir($pathToStyles)) 
		{
			// Open the style directory
			if ($dirHandle = opendir($pathToStyles)) 
			{
				// Loop through style directory
				while (($styleDir = readdir($dirHandle)) !== false) 
				{
					// ignore hidden directories and files
					if
					(
						$styleDir == '.' ||
						$styleDir == '..'  || 
						$styleDir == 'CVS' || 
						$styleDir == '.svn'
					) 
					{ 
						continue; 
					}
					// if the item is another directory
					if (@is_dir($pathToStyles.$styleDir)) 
					{
						$html .= '<option value="'.$styleDir.'"'.(($this->cfg['preferences']['style'] == $styleDir) ? ' selected="selected"' : '').'>'.ucwords(str_replace('_', ' ', $styleDir)).'</option>';
					}
				}
				closedir($dirHandle);
			}
		}
		return $html;
	}
	
	/**************************************************************************
	 generateOffsetOptions()
	 
	 **************************************************************************/
	function generateOffsetOptions() 
	{
		$html = "";
		for ($o = -12; $o <= 14; $o++) 
		{
			$html .= "<option value=\"$o\"";
			if ($o == $this->cfg['offset']) 
			{
				$html .= ' selected="selected"';
			}
			$html .=  ">";
			$html .= $this->offsetDate("h:ia \o\\n M d, Y", null, $o);
			$html .= "</option>\n\t\t\t";
		}
		return $html;
	}
	
	/**************************************************************************
	 offsetMakeGMT()
	 
	 Acts like PHP's buit-in mktime() but uses $Mint->cfg['offset'] and treats
	 its arguments like offset times. So for the GMT timestamp for 8pm EST today 
	 just call: $Mint->offsetMakeGMT(20, 0, 0);
	 **************************************************************************/
	function offsetMakeGMT($hours = false, $minutes = false, $seconds = false, $month = false, $day = false, $year = false)
	{
		$offset = $this->cfg['offset'];
		list
		(
			$nowDay,
			$nowMonth,
			$nowYear,
			$nowHours,
			$nowMinutes,
			$nowSeconds
		) = explode(' ', $this->offsetDate('d m Y H i s'));
		
		$hours		= ($hours 	!== false)	? $hours 	: $nowHours;
		$minutes	= ($minutes	!== false)	? $minutes	: $nowMinutes;
		$seconds 	= ($seconds	!== false)	? $seconds	: $nowSeconds;
		$month		= ($month	!== false)	? $month	: $nowMonth;
		$day		= ($day		!== false)	? $day		: $nowDay;
		$year		= ($year	!== false)	? $year		: $nowYear;
		
		// gmmktime is only reliable as long as all arguments are provided otherwise 
		// it uses the server's local time to fill in the gaps which is just asinine
		$offsetTime = gmmktime($hours - $offset, $minutes, $seconds, $month, $day, $year);
		return $offsetTime;
	}
	
	/**************************************************************************
	 offsetDate()
	 
	 Exactly like PHP's built-in date() function but uses $Mint->cfg['offset']
	 to return adjusted time.
	 **************************************************************************/
	function offsetDate($format = 'U', $time = null, $offset = null)
	{	
		
		$offset	= ($offset	!== null)	? $offset 	: $this->cfg['offset'];
		$time	= ($time	!== null)	? $time		: time();
		// Timestamp should be passed through without the offset
		$format	= preg_replace('#(?!:[^\\\]?)U#', $time, $format);
		$time 	+= ($offset * 60 * 60);
		
		return gmdate($format, $time);
	}
	
	/**************************************************************************
	 getOffsetTime()
	 
	 **************************************************************************/
	function getOffsetTime($when = '')
	{
		$time = time();
		switch($when)
		{
			case 'today':
				$time = $this->offsetMakeGMT(0, 0, 0);
			break;
			
			case 'hour':
				$time = $this->offsetMakeGMT(false, 0, 0);
			break;
			
			case 'week':
				$time = $this->offsetMakeGMT(0, 0, 0, $this->offsetDate('n'), $this->offsetDate('j') - $this->offsetDate('w') /* Monday -1 */, $this->offsetDate('Y'));
			break;
			
			case 'month':
				$time = $this->offsetMakeGMT(0, 0, 0, $this->offsetDate('n'), 1);
			break;
		}
		return $time;
	}
	
	/**************************************************************************
	 formatDateRelative()
	 
	 **************************************************************************/
	function formatDateRelative($time = null, $scale = "day", $i = 0) 
	{
		$relative	= '';
		$isToday	= false; 
		if ($time == null)
		{
			$isToday	= true;
			$time		= time();
		}
		
		if ($time >= $this->offsetMakeGMT(0, 0, 0))
		{
			$isToday	= true;
		}
		
		switch($scale)
		{
			case 'hour':
				$relative = $this->offsetDate('g:00a', $time); // G:00 24 hour
			break;
			
			case 'day':
				$relative = ($isToday)?'Today':$this->offsetDate('l', $time);
			break;
			
			case 'week':
				$relative = ($i == 0)?'This Week':(($i == 1)?'Last Week':$i.' weeks ago');
			break;
			
			case 'month':
				$relative = ($i == 0)?'This Month':$this->offsetDate('M &#8217;y', $time);
			break;
		}
		
		return $relative;
	}
	
	/**************************************************************************
	 formatDateTimeRelative()
	 
	 Returns a string relative to the current date/time in the closest unit 
	 eg. mins within the hour, hours within the day, days within a week, etc
	 **************************************************************************/
	function formatDateTimeRelative($time)
	{
		$diff = time() - $time;
		if ($diff < 10) 
		{
			return "Just Now";
		}
		else if ($diff < 60) 
		{
			return "&lt; 1 min ago";
		}
		
		$diff = round($diff / 60);
		if ($diff < 60) 
		{ 
			$min = "min".(($diff > 1) ? 's' : '');
			return "$diff $min ago";
		}
		
		$diff = round($diff / 60);
		if ($diff < 24) 
		{
			$hr = "hour".(($diff > 1) ? 's' : '');
			return "$diff $hr ago";
		}
		
		$diff = round($diff / 24);
		if ($diff < 7) 
		{
			return ($diff > 1) ? "$diff days ago" : 'Yesterday';
		}
		
		$diff = round($diff / 7);
		if ($diff < 52) 
		{
			return ($diff > 1) ? "$diff weeks ago" : 'Last Week';
		}
		
		$diff = round($diff / 52);
		return ($diff > 1) ? "Around $diff years ago" : 'Last Year';
	}
	
	/**************************************************************************
	 formatDateTimeSpan()
	 
	 Returns a formatted approximation of the amount of time passed between 
	 the two timestamps.
	 **************************************************************************/
	function formatDateTimeSpan($startTime, $endTime)
	{
		$span = $endTime - $startTime;
		
		if ($span < 60) // seconds
		{
			return $span.' sec'.(($span > 1) ? 's' : '');
		}
		
		$span = round($span / 60);
		if ($span < 60) // minutes
		{
			return $span.' min'.(($span > 1) ? 's' : '');
		}
		
		$span = round($span / 60);
		return $span.' hour'.(($span > 1) ? 's' : '');
	}
	
	/**************************************************************************
	 formatPercents()
	 
	 **************************************************************************/
	function formatPercents($num)
	{
		if (floor($num)<1)
		{
			return "&lt;1%";
		}
		else
		{
			return round($num)."%";
		}
	}
	
	/**************************************************************************
	 getFormattedErrors()
	 
	 **************************************************************************/
	function getFormattedErrors()
	{
		$html = '';
		
		if (!empty($this->errors['note']))
		{
			$html .= $this->errors['note'];
		}
		
		if (!empty($this->errors['list']))
		{
			$data['list']['class'] = 'errors';
			$data['items'] = $this->errors['list'];
			
			$html .= $this->generateUnorderedList($data);
		}
		
		return $html;
	}
	
	/**************************************************************************
	 getFormattedFeedback()
	 
	 **************************************************************************/
	function getFormattedFeedback()
	{
		$html = '';
		
		if (!empty($this->feedback['feedback']))
		{
			$html .= $this->feedback['feedback'];
			if (!empty($this->feedback['button']))
			{
				$html .= '<p><a href="'.$this->feedback['destination'].'"><img src="styles/'.$this->cfg['preferences']['style'].'/images/btn-'.$this->feedback['button'].'.png" width="62" height="22" alt="'.ucfirst($this->feedback['button']).'" /></a></p>';
			}
		}
		
		return $html;
	}
	
	/**************************************************************************
	 getFormattedBenchmark()
	 
	 **************************************************************************/
	function getFormattedBenchmark()
	{
		$html = '';
		
		$this->logBenchmark('}');
		
		$benchStart = $this->microtime_float();
		
		if (!empty($this->bench))
		{
			$xml = '<?xml version="1.0" ?'.">\r<bench>\r";
			$indent = 0;
			foreach($this->bench as $mark)
			{
				if (preg_match('/(.+)\{$/', $mark[0], $match))
				{
					$indent++;
					$name = trim($match[1]);
					$xml .= str_repeat("\t", $indent).'<mark event="'.str_repeat("    ", $indent - 1).htmlentities($name).'">'."\r";
					$xml .= str_repeat("\t", $indent)."\t".'<start time="'.$mark[1].'" />'."\r";
				}
				else if (trim($mark[0]) == '}')
				{
					$xml .= str_repeat("\t", $indent)."\t".'<stop time="'.$mark[1].'" />'."\r";
					$xml .= str_repeat("\t", $indent).'</mark>'."\r";
					$indent--;
				}
			}
			$xml .= '</bench>';
			
			/** /
			$html .= '<pre>';
			$html .= htmlentities($xml);
			$html .= '</pre>';
			echo $html;
			/**/
			
			include_once(MINT_ROOT.'app/debug/si-dom.php');
			
			$DOM =& new SI_Dom($xml);
			$marks =& $DOM->bench->getNodesByNodeName('mark');
			
			$html .= '<div class="benchmark"><pre>MINT PROCESSING BENCHMARKS'."\r========================================================================\r".'Secs    Event'."\r------------------------------------------------------------------------\r";
			foreach ($marks as $mark) 
			{
				$start		= $mark->getChildNodesByNodeName('start');
				$stop		= $mark->getChildNodesByNodeName('stop');
				$runtime	= number_format(($stop[0]->time - $start[0]->time), 4);
				$html .= "{$runtime}  {$mark->event}\r";
			}
			
			$html .= "========================================================================\r".number_format(($this->microtime_float() - $benchStart), 4)."  getFormattedBenchmark()";
			$html .= '</pre></div>';
		}
				
		return $html;
	}
	
	/**************************************************************************
	 getFormattedQueries()
	 
	 **************************************************************************/
	function getFormattedQueries()
	{
		$html = '';
		
		if (!empty($this->queries))
		{
			$html .= '<p>'.count($this->queries).' queries.</p>';
			$data['list']['class'] = 'queries';
			$data['items'] = $this->queries;
			
			$html .= $this->generateUnorderedList($data);
		}
		
		return $html;
	}
	
	/**************************************************************************
	 getFormattedVersion()
	 
	 Returns the version number formatted for display
	 **************************************************************************/
	function getFormattedVersion() 
	{
		$len = (substr($this->version.'',-1) == '0')?1:2;
		return '<abbr title="Mint (v'.str_pad($this->version,3,'0',STR_PAD_LEFT).' on '.(($this->is64bit())?64:32).')">Mint '.number_format($this->version/100,$len).'</abbr>';
	}
		
	/**************************************************************************
	 getTableSize()
	 
	 Just like it sounds.
	 **************************************************************************/
	function getTableSize($table = 'visit') 
	{
		$prefix = str_replace('_', '\_', $this->db['tblPrefix']);
		$table 	= str_replace('_', '\_', $table);
		
		$size = 0;
		$query="SHOW TABLE STATUS LIKE '{$prefix}{$table}'";
		if ($result=$this->query($query)) 
		{
			while ($table = mysql_fetch_assoc($result)) 
			{ 
				$size += $table['Data_length'] + $table['Index_length']; 
			}
		}
		return $size;
	}
	
	/**************************************************************************
	 getFormattedTableSize()

	 **************************************************************************/
	function getFormattedTableSize($table = 'visit')
	{
		return number_format($this->getTableSize($table) / 1024 / 1024, 2).' MB';
	}
	
	/**************************************************************************
	 getIP()

	 **************************************************************************/
	function getIP()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && !empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		return $ip;
	}
	
	/**************************************************************************
	 getIPLong()

	 **************************************************************************/
	function getIPLong()
	{
		return ip2long($this->getIP());
	}
	
	/**************************************************************************
	 is64bit()
	 
	 Returns false on 32-bit architectures
	 **************************************************************************/
	function is64bit()
	{
		return (crc32('1') > 0);
	}
	
	/**************************************************************************
	 isInstalledLocally()
	 
	 Whether Mint has been installed on localhost or 127.0.0.1
	 **************************************************************************/
	function isInstalledLocally()
	{
		return (preg_match('/^(127\.0\.0\.1|localhost)(:\d+)?$/i', $this->cfg['installTrim']))?true:false;
	}
	
	/**************************************************************************
	 abbr()
	 
	 Trims a non-HTML string to a specified length. Will trim from a space (for 
	 words), slash (for urls), or question mark or ampersand for query strings 
	 if at all possible. Can be anchored to the end of a string as well.
	 **************************************************************************/
	 function abbr($var, $len = 44, $reverse = false) 
	 {
		$abbr	= '';
		// trim prefix for display purposes, borrowed from SI_Default::trimPrefixIndex()
		$var	= preg_replace("/^http(s)?:\/\/www\.([^.]+\.)/i", "http$1://$2", $var);
		$var	= preg_replace('/^http(s)?:\/\/('.str_replace('.', '\.', implode('|', $this->domains)).')?(:\d+)?/', '', $var);
		
		if (!empty($var))
		{
			if (strlen($var) <= $len)// || $this->cfg['preferences']['singleColumn'])
			{
				$abbr = str_replace('?', '<wbr />?', $var);
			}
			else if ($reverse)
			{
				if (preg_match("/(.(\s|\/|\?|&|_|-).{1,$len}$)/ms", $var, $match))
				{
					$abbr = "<abbr title=\"$var\">&#8230;".str_replace('?', '<wbr />?', $match[1])."</abbr>";
				}
				else
				{
					$abbr = "<abbr title=\"$var\">&#8230;".str_replace('?', '<wbr />?', substr($var, (strlen($var) - $len), $len))."</abbr>";
				}
			}
			else 
			{
				if (preg_match("/^((&[^;]+;|.){1,$len})(\s|\/|\?|&|_|-)./ms", $var, $match))
				{
					$abbr = "<abbr title=\"$var\">".str_replace('?', '<wbr />?', $match[1])."&#8230;</abbr>";
				}
				else
				{
					$abbr = "<abbr title=\"$var\">".str_replace('?', '<wbr />?', substr($var, 0, $len))."&#8230;</abbr>";
				}
			}
		}
		return $abbr;
	}
	
	/**************************************************************************
	 array_reindex()
	 
	 **************************************************************************/
	function array_reindex($array)
	{
		$tmpArray = array();
		foreach ($array as $value)
		{
			$tmpArray[] = $value;
		}
		return $tmpArray;
	}
	
	/**************************************************************************
	 array_last_index()
	 
	 **************************************************************************/
	function array_last_index($array)
	{
		$last = -1;
		foreach ($array as $i => $value)
		{
			$last = $i;
		}
		return $last;
	}
	
	/**************************************************************************
	 microtime_float()
	 
	 **************************************************************************/
	function microtime_float()
	{
		list
		(
			$s,
			$m
		) = explode(" ", microtime());
		return (float) $s + (float) $m;
	}
	
	/**************************************************************************
	 observe()
	 
	 Like print_r but returns a preformatted HTML string and omits sensitive 
	 information like passwords, etc
	 **************************************************************************/
	function observe($obj, $depth = 0)
	{
		$html = '';
		
		if (!$depth)
		{
			$html .= '<pre>';
		}
		
		$type = gettype($obj);
		
		$indent = str_repeat("\t",$depth);
		$html .= "<em>{$type}</em>";
		
		if ($type == 'array' || $type == 'object')
		{
			$html .= "\r{$indent}{\r";
			foreach($obj as $key=>$value)
			{
				
				$html .= "\t{$indent}".htmlspecialchars($key)." = ";
				$valueType = gettype($value);
				if ($valueType == 'array')
				{
					$html .= $this->observe($value, $depth+1);
				}
				else if ($valueType == 'object')
				{
					$html .= "<em>{$valueType}</em> {   }\r";
				}
				else
				{
					
					// Don't display the sensitive info
					if (preg_match('/(email|username|password|database|activationKey|API)/i',$key))
					{
						$html .= "********\r";
					}
					else
					{
						if (preg_match("/`{$this->db['tblPrefix']}_config`/", $value) && strlen($value) > 72)
						{
							$value = substr($value, 0, 72)."&#8230;";
						}
						$html .= str_replace("\r","\r\t\t{$indent}", htmlspecialchars($value))."\r";
					}
				}
			}
			$html .= "{$indent}}\r";
		}
		
		if (!$depth)
		{
			$html .= '</pre><hr />';
		}
		
		return $html;
	}
}

if (!function_exists('html_entity_decode'))
{
	function html_entity_decode($string, $quote_style = ENT_COMPAT) 
	{
		// replace numeric entities
		$string = preg_replace('/&#x([0-9a-f]+);/ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('/&#([0-9]+);/e', 'chr(\\1)', $string);
		
		// replace literal entities
		$translationTable = get_html_translation_table(HTML_ENTITIES, $quote_style);
		$translationTable = array_flip($translationTable);
		return strtr($string, $translationTable);
	}   
}