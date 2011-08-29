<?php
/******************************************************************************
 Pepper
 
 Developer		: Shaun Inman
 Plug-in Name	: Backup/Restore
 
 http://www.shauninman.com/
 
 ******************************************************************************/
if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file
$installPepper = "SI_BackUpRestore";

class SI_BackUpRestore extends Pepper
{
	var $version	= 202;
	var $info		= array
	(
		'pepperName'	=> 'Backup/Restore',
		'pepperUrl'		=> 'http://www.haveamint.com/',
		'pepperDesc'	=> 'As a pane-less Pepper, Backup/Restore does not record or display any data in Mint. It is a utility to backup and restore your Mint database tables. Painlessly.',
		'developerName'	=> 'Shaun Inman',
		'developerUrl'	=> 'http://www.shauninman.com/'
	);
	var $prefs		= array
	(
		'backupPrefix'	=> 'bak_'
	);
	
	/**************************************************************************
	 onPepperLoad() 
	 
	 **************************************************************************/
	function onPepperLoad()
	{
		if ($this->Mint->isLoggedIn() && isset($_GET['preferences']))
		{
			// check for pre120_ tables and offer to delete
			$prefix = str_replace('_', '\_', "pre120_{$this->Mint->db['tblPrefix']}");
			$query = "SHOW TABLES LIKE '{$prefix}%'";
			if ($result = $this->query($query))
			{
				if (mysql_fetch_assoc($result))
				{
					if (isset($_GET['drop120']))
					{
						if 
						(
							$this->query("DROP TABLE `pre120_{$this->Mint->db['tblPrefix']}_config`") &&
							$this->query("DROP TABLE `pre120_{$this->Mint->db['tblPrefix']}visit`")
						)
						{
							$error = 'The Backup/Restore Pepper has successfully deleted the old tables.';
						}
						else
						{
							$error = 'The Backup/Restore Pepper could not deleted the old tables.';
						}
					}
					else
					{
						$path	= $_SERVER['REQUEST_URI'].((strpos($_SERVER['REQUEST_URI'], '?') === false)?'?':'&amp;').'drop120';
						$error	= 'The Backup/Restore Pepper has detected tables left over from an earlier Mint installation.<br />';
						$error .= 'These tables can be safely deleted after a successful update from Mint 1.14.<br />';
						$error .= '<a href="'.$path.'"><img src="pepper/shauninman/backuprestore/images/btn-delete-mini.png" width="51" height="17" alt="Delete" /></a>';
					}
					$this->Mint->logError($error);
				}
			}
		}
	}
	
	/**************************************************************************
	 isCompatible()
	 **************************************************************************/
	function isCompatible()
	{
		if ($this->Mint->version < 200)
		{
			$compatible = array
			(
				'isCompatible'	=> false,
				'explanation'	=> '<p>This Pepper requires Mint 2, a paid upgrade, which is now available at <a href="http://www.haveamint.com/">haveamint.com</a>.</p>'
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
	 onDisplayPreferences()
	 **************************************************************************/
	function onDisplayPreferences() 
	{
		/* Database Settings **************************************************/
		$preferences['Database Settings'] = <<<HERE
<table>
	<tr>
		<th scope="row">Backup Table Prefix</th>
		<td><span><input type="text" id="backupPrefix" name="backupPrefix" value="{$this->prefs['backupPrefix']}" /></span></td>
	</tr>
	<tr>
		<td></td>
		<td>(Results in tables like: <code>{$this->prefs['backupPrefix']}0_{$this->Mint->db['tblPrefix']}_config</code>)</td>
	</tr>
</table>

HERE;

		/* New Backup *********************************************************/
		$preferences['Make a New Backup'] = <<<HERE
<script type="text/javascript" language="JavaScript">
// <![CDATA[
SI.BackupRestore = 
{
	makeBackup		: function()
	{
		var content = document.getElementById('backup-list');
		content.innerHTML = '<p class="request-feedback loading">Creating new backup.</p>';
		var backupConfigOnly = (document.getElementById('backupConfigOnly').checked)?true:false;
		var url = '{$this->Mint->cfg['installDir']}/?MintPath=Custom&backupAction=backup&backupConfigOnly=' + backupConfigOnly;
		SI.Request.post(url, content, SI.Fade.delayedDown, 'request-feedback');
	},
	restoreBackup	: function(backupPrefix)
	{
		SI.Mint.clickForm('{$this->Mint->cfg['installDir']}/', 'post', {'MintPath' : 'Custom', 'backupAction' : 'restore', 'backupPrefix' : backupPrefix});
	},
	deleteBackup	: function(backupPrefix, dateTime)
	{
		if (!window.confirm('Delete backup made on ' + dateTime + '? (Once deleted this data cannot be recovered.)')) { return; }
		var content = document.getElementById('backup-list');
		var url		= '{$this->Mint->cfg['installDir']}/?MintPath=Custom&backupAction=delete&backupPrefix=' + backupPrefix;
		SI.Request.post(url, content, SI.Fade.delayedDown, 'request-feedback');
	}
};
// ]]>
</script>
<table class="snug">
	<tr>
		<td><a href="#backup" onclick="SI.BackupRestore.makeBackup(); return false;" style="margin: 0 11px 0 0;"><img src="pepper/shauninman/backuprestore/images/btn-backup-mini-single.png" width="51" height="17" alt="Backup" /></a></td>
		<td><label><input type="checkbox" id="backupConfigOnly" name="backupConfigOnly" value="1" /> Configuration only</label></td>
	</tr>
</table>
HERE;

		/* Existing Backups ***************************************************/
		
		
		$preferences['Existing Backups'] = '<div id="backup-list">'.$this->getHTML_existingBackups().'</div>';
		
		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{
		$this->prefs['backupPrefix']	= $this->escapeSQL($_POST['backupPrefix']);
	}
	
	/**************************************************************************
	 onCustom()
	 **************************************************************************/
	function onCustom() 
	{
		/* Backup ------------------------------------------------------------*/
		if
		(
			isset($_POST['backupAction']) && 
			(
				$_POST['backupAction'] == 'backup' || 
				$_POST['backupAction'] == 'restore' || 
				$_POST['backupAction'] == 'delete'
			)
		)
		{
			$feedback = '';
			switch($_POST['backupAction'])
			{
				case 'backup':
					$this->makeBackup();
					$feedback = "Successfully created new backup.";
					break;
				case 'restore':
					if (isset($_POST['backupPrefix']))
					{
						$this->restoreBackup($_POST['backupPrefix']);
						$this->deleteBackup($_POST['backupPrefix']);
						header('Location:?preferences');
						exit();
					}
					break;
				case 'delete':
					if (isset($_POST['backupPrefix']))
					{
						$this->deleteBackup($_POST['backupPrefix']);
						$feedback = "Successfully deleted backup.";
					}
					break;
			}
			echo $this->getHTML_existingBackups($feedback);
		}
	}
	
	/**************************************************************************
	 makeBackup()
	 **************************************************************************/
	function makeBackup()
	{
		
		$backupData = $this->getBackupData();
		$newBackupNum = $backupData['last'] + 1;
		$prefix	= "{$this->prefs['backupPrefix']}{$newBackupNum}_";
		
		if ($_POST['backupConfigOnly'] == 'true') // Config only
		{
			// Create the _config_only table
			$tableName = "{$prefix}{$this->Mint->db['tblPrefix']}_config_only";
			$this->query("CREATE TABLE `$tableName` (`id` int(10) unsigned NOT NULL auto_increment, `cfg` text NOT NULL, PRIMARY KEY  (`id`)) ".$this->Mint->dbEngine()."=MyISAM;");
			$this->query("INSERT INTO `$tableName` VALUES (1, '".addslashes(serialize($this->Mint->cfg))."')");
		}
		else
		{
			foreach($this->Mint->cfg['manifest'] as $table => $unused)
			{
				$tableName = $this->Mint->db['tblPrefix'].$table;
				if ($result = $this->query("SHOW CREATE TABLE `$tableName`"))
				{
					if ($table = mysql_fetch_assoc($result))
					{
						$query = str_replace("CREATE TABLE `$tableName`", "CREATE TABLE `{$prefix}{$tableName}`", $table['Create Table']);
						if ($this->query($query))
						{
							$query = "INSERT INTO  `{$prefix}{$tableName}` SELECT * FROM  `$tableName` ;";
							$this->query($query);
						}
					}
				}
			}
		}
	}
	
	/**************************************************************************
	 restoreBackup()
	 **************************************************************************/
	function restoreBackup($backupPrefix)
	{
		$backupData = $this->getBackupData();
		
		if (isset($backupData['backups'][$backupPrefix]))
		{
			$backup = $backupData['backups'][$backupPrefix];
			if (isset($backup['configOnly']))
			{
				$query = "SELECT `cfg` FROM `{$backupPrefix}_config_only` LIMIT 0,1";
				if ($result = $this->query($query))
				{
					if ($load = mysql_fetch_assoc($result))
					{
						$this->Mint->cfg = $this->Mint->safeUnserialize($load['cfg']);
						$this->Mint->_save();
					}
				}
			}
			else
			{
				// Delete main Mint tables
				$tables		= array();
				foreach($this->Mint->cfg['manifest'] as $table => $unused)
				{
					$tables[] = $this->Mint->db['tblPrefix'].$table;
				}
				$query = "DROP TABLE `".implode("`, `", $tables)."`";
				
				if ($this->query($query))
				{
					// Rename backups
					foreach($backup['tables'] as $table)
					{
						$renamedTable = $this->Mint->db['tblPrefix'].str_replace($backupPrefix, '', $table);
						$query = "ALTER TABLE `$table` RENAME `$renamedTable`;";
						if (!$this->query($query))
						{
							echo mysql_error().' '.$query.'<br />';
						}
					}
				}
				else
				{
					echo mysql_error().' '.$query.'<br />';
				}
			}
		}
	}
	
	/**************************************************************************
	 deleteBackup()
	 **************************************************************************/
	function deleteBackup($backupPrefix)
	{
		$tables = array();
		$backupData = $this->getBackupData();
		
		if (isset($backupData['backups'][$backupPrefix]))
		{
			$backup = $backupData['backups'][$backupPrefix];
			if (isset($backup['configOnly']))
			{
				$tables[] = $backupPrefix.'_config_only';
			}
			else
			{
				$tables = $backup['tables'];
			}
			
			$query = "DROP TABLE `".implode("`, `", $tables)."`";
			$this->query($query);
		}
	}
	
	/**************************************************************************
	 getBackupData()
	 **************************************************************************/
	function getBackupData()
	{
		$backupData = array
		(
			'last'		=> 0,
			'size'		=> 0,
			'backups'	=> array()
		);
		
		$prefix = str_replace('_', '\_', $this->prefs['backupPrefix']);
		$query="SHOW TABLE STATUS LIKE '{$prefix}%'";
		if ($result = $this->query($query)) 
		{
			$stalePrefix = '';
			while ($table = mysql_fetch_assoc($result)) 
			{
				// Determine prefix and current backup number
				if (preg_match('/(^'.$prefix.'([0-9]+)_'.$this->Mint->db['tblPrefix'].').+/', $table['Name'], $match))
				{
					$freshPrefix = $match[1];
					if ($match[2] > $backupData['last'])
					{
						$backupData['last'] = $match[2];
					}
					
					// Determine creation time
					list
					(
						$year,
						$month,
						$day,
						$hour,
						$min,
						$sec
					) = preg_split('/[- :]/', $table['Create_time']);
					$dt = mktime($hour, $min, $sec, $month, $day, $year);
					
					// Reset size
					if ($stalePrefix != $freshPrefix)
					{
						$size = 0;
						$stalePrefix = $freshPrefix;
					}
					$size += $table['Data_length'] + $table['Index_length'];
					
					$backupData['backups'][$freshPrefix]['dt']			= $dt;
					$backupData['backups'][$freshPrefix]['size']		= $size;
					$backupData['backups'][$freshPrefix]['tables'][]	= $table['Name'];
					
					if (strpos($table['Name'], '_config_only') !== false)
					{
						$backupData['backups'][$freshPrefix]['configOnly'] = true;
					}
				}
			}
		}
		foreach($backupData['backups'] as $backup)
		{
			$backupData['size'] += $backup['size'];
		}
		return $backupData;
	}
	
	/**************************************************************************
	 getHTML_existingBackups()
	 **************************************************************************/
	function getHTML_existingBackups($feedback = '')
	{
		$html = '';
		$tableData['table'] = array('id'=>'','class'=>'');
		$backupData = $this->getBackupData();
		
		if (!empty($feedback))
		{
			$feedback = "<div id=\"request-feedback\" class=\"request-feedback\">$feedback</div>";
		}
		
		if (!empty($backupData['backups']))
		{
			$backups = array_reverse($backupData['backups']);
			foreach ($backups as $prefix => $data)
			{
				$tableData['tbody'][] = array
				(
					$this->Mint->offsetDate('M j \a\t g:ia', $data['dt']).'<br />'.((isset($data['configOnly']))?'<strong>Config only</strong> ':'').'('.number_format(($data['size'] / 1024 / 1024), 2).' MB)',
					'<a href="#restore" onclick="SI.BackupRestore.restoreBackup(\''.$prefix.'\'); return false;"><img src="pepper/shauninman/backuprestore/images/btn-restore-mini.png" width="51" height="17" alt="Restore" /></a>',
					'<a href="#delete" onclick="SI.BackupRestore.deleteBackup(\''.$prefix.'\', \''.$this->Mint->offsetDate('M j \a\t g:ia', $data['dt']).'\'); return false;"><img src="pepper/shauninman/backuprestore/images/btn-delete-mini.png" width="51" height="17" alt="Delete" /></a>'
				);
			}
			
			$backupHTML	= $this->Mint->generateTable($tableData);
			$sizeHTML	= number_format(($backupData['size'] / 1024 / 1024), 2);
			
			$html = <<<HERE
<p>Combined size of all backups: <strong>$sizeHTML MB</strong></p>
$feedback
$backupHTML
HERE;
		}
		else
		{
			$html = '<p>You have no backups.</p>';
		}
		
		return $html;
	}
}