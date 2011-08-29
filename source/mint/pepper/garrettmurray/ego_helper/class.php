<?php

/******************************************************************************
 Pepper

 Developer: Garrett Murray
 Plug-in Name: Ego Helper

 More info at: http://ego-app.com

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	Foobar is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

 ******************************************************************************/

	if (!defined('MINT'))
	{
		header('Location: /');
		exit();
	};

	$installPepper = 'GM_Ego_Helper';

	class GM_Ego_Helper extends Pepper
	{

		var $version = 102;

		var $info = array(
			'pepperName'	=> 'Ego Helper',
			'pepperUrl'		=> 'http://ego-app.com/pepper/',
			'pepperDesc'	=> 'Helps Ego work with Mint.',
			'developerName'	=> 'Garrett Murray',
			'developerUrl'	=> 'http://ego-app.com/'
		);

		var $panes = array();
		var $prefs = array();
		var $data = array();
		var $manifest = array();

		function update()
		{
			if ($this->Mint->version < 200)
			{
				$this->Mint->logError('This version of Ego Helper requires Mint v2.00.', 2);
			}
		}

		function isCompatible()
		{
			if ($this->Mint->version < 200)
			{
				return array('isCompatible' => FALSE, 'explanation' => '<p>This Pepper requires Mint v2.00.</p>');
			}
			else
			{
				return array('isCompatible' => TRUE);
			}
		}

		function print_debug($msg, $error = FALSE)
		{
			if (!is_array($msg))
			{
				$msg = array($msg);
			}
			foreach ($msg as $value)
			{
				if ($error)
				{
					print '<br /><code><b>Error:</b> '.$value.'</code><br />';
				}
				else
				{	
					print '<br /><code>'.$value.'</code><br />';
				}
			}
		}
		
		function show_stats($email, $password)
		{
			global $Mint;
			
			if (urldecode($email) == $Mint->cfg['email'] && urldecode($password) == $Mint->cfg['password'])
			{
				$visits = $Mint->data[0]['visits'];
			
				// total
				$total_unqs = $visits[0][0]['unique'];
				$total_hits = $visits[0][0]['total'];
			
				// this hour
				$hourly_keys = array_keys($visits[1]);
				$last_hour = $hourly_keys[(count($hourly_keys)-1)];
				$hour_unqs = $visits[1][$last_hour]['unique'];
				$hour_hits = $visits[1][$last_hour]['total'];
			
				// today
				$daily_keys = array_keys($visits[2]);
				$last_day = $daily_keys[(count($daily_keys)-1)];
				$day_unqs = $visits[2][$last_day]['unique'];
				$day_hits = $visits[2][$last_day]['total'];
			
				// this week
				$weekly_keys = array_keys($visits[3]);
				$last_week = $weekly_keys[(count($weekly_keys)-1)];
				$week_unqs = $visits[3][$last_week]['unique'];
				$week_hits = $visits[3][$last_week]['total'];
			
				// this month
				$monthly_keys = array_keys($visits[4]);
				$last_month = $monthly_keys[(count($monthly_keys)-1)];
				$month_unqs = $visits[4][$last_month]['unique'];
				$month_hits = $visits[4][$last_month]['total'];

                // get last five unique referrers
				$unique_refs = $this->get_unique_referrers();
				
				// build lazy XML
				// i know this isn't an awesome way to do it, but i want to make sure
				// it's compatible with all PHP versions, since i don't know exactly
				// what mint requires, so i can't rely on frameworks or libs or whatever
				
				header('Content-type: text/xml');
				
				echo '<?xml version="1.0" encoding="UTF-8" ?>';
				echo "\n<stats>";
				
				echo "\n\n";
				echo "<!-- Mint stats provided by the Ego Helper Pepper, used by Ego, an iPhone";
				echo " application available at http://ego-app.com -->";
				echo "\n";
				
				echo "\n\t<visits>";
				echo "\n\t\t<total unique=\"{$total_unqs}\" hits=\"{$total_hits}\"/>";
                echo "\n\t\t<hour unique=\"{$hour_unqs}\" hits=\"{$hour_hits}\"/>";
                echo "\n\t\t<day unique=\"{$day_unqs}\" hits=\"{$day_hits}\"/>";
                echo "\n\t\t<week unique=\"{$week_unqs}\" hits=\"{$week_hits}\"/>";
                echo "\n\t\t<month unique=\"{$month_unqs}\" hits=\"{$month_hits}\"/>";
				echo "\n\t</visits>";
				echo "\n\t<referrers>";
				foreach ($unique_refs as $ref):
					echo "\n\t\t<item>";
					echo "\n\t\t\t<date>".$ref['date']."</date>";
					echo "\n\t\t\t<title>".$this->clean_string($ref['title'])."</title>";
					echo "\n\t\t\t<link>".$ref['title_link']."</link>";
					echo "\n\t\t\t<resource>".$this->clean_string($ref['resource_title'])."</resource>";
					echo "\n\t\t\t<resource_link>".$ref['resource_link']."</resource_link>";
					echo "\n\t\t</item>";
				endforeach;
				echo "\n\t</referrers>";
				echo "\n</stats>";
			}
		}
		
		function clean_string($str)
		{
		    return str_replace('&', '&amp;', $str);
		}
		
		function get_unique_referrers() 
		{
			global $Mint;
			// Ignore certain domains
			// $ignoredDomains  = preg_split('/[\s,]+/', $Mint->prefs['ignoreReferringDomains']);
			//           $ignoreQuery    = '';
			//           if (!empty($ignoredDomains))
			//           {
			//               foreach ($ignoredDomains as $domain)
			//               {
			//                   if (empty($domain))
			//                   {
			//                       continue;
			//                   }
			//                   $ignoreQuery .= ' AND `domain_checksum` != '.crc32($domain);
			//               }
			//           }

			$query = "SELECT `referer`, `resource`, `resource_title`, `dt`
				FROM `{$Mint->db['tblPrefix']}visit` 
				WHERE `referer_is_local` = 0 AND `search_terms` = ''
				GROUP BY `referer_checksum` 
				ORDER BY `dt` DESC 
				LIMIT 0,5";
				
			$refs = array();

			if ($result = $Mint->query($query)) 
			{
				while ($r = mysql_fetch_array($result)) 
				{
					$ref['date'] = $Mint->formatDateTimeRelative($r['dt']);
					$ref['title'] = $r['referer'];
					$ref['title_link'] = $r['referer'];
					$ref['resource_title'] = (!empty($r['resource_title']))?stripslashes($r['resource_title']):$r['resource'];
					$ref['resource_link'] = $r['resource'];
					$refs[] = $ref;
				}
			}
			
			return $refs;
		}

	}
