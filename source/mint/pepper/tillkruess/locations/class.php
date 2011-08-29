<?php

/******************************************************************************
 Pepper

 Developer: Till Krüss
 Plug-in Name: Locations

 More info at: http://pepper.pralinenschachtel.de/

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.

 Country lookup engine (c) L. Petersen, Weird Silence
 More info at: http://weirdsilence.net/software/ip2c

 ******************************************************************************/

	if (!defined('MINT')) {
		header('Location: /');
		exit();
	};

	$installPepper = 'TK_Locations';

	class TK_Locations extends Pepper {

		var $version = 251;

		var $info = array(
			'pepperName' => 'Locations',
			'pepperUrl' => 'http://pepper.pralinenschachtel.de/',
			'pepperDesc' => 'This Pepper tracks the geographical locations, based on IP addresses.',
			'developerName' => 'Till Kr&uuml;ss',
			'developerUrl' => 'http://pralinenschachtel.de/'
		);

		var $panes = array(
			'Locations' => array(
				'Most Common',
				'Most Recent'
			)
		);

		var $data = array(
			'locations' => array(
				'total' => array(),
				'unique' => array()
			)
		);

		var $prefs = array(
			'threshold' => 1,
			'sortby' => 0,
			'apache' => 0
		);

		var $manifest = array(
			'visit' => array(
				'country_code' => "varchar(2) NOT NULL default 'XX'"
			)
		);

		function update() {

			if ($this->Mint->version < 215) {

				$this->Mint->logError('This version of Locations requires Mint 2.15 or higher.', 2);

			} else {

				if ($this->getInstalledVersion() < 223) {

					foreach ($this->data['locations'] as $type => $contries) {
						if ($type != 'total' && $type != 'unique') {
							unset($this->data['locations'][$type]);
						} else {
							foreach ($this->data['locations'][$type] as $country => $hits) {
								if (empty($country) || $hits == '-') {
									unset($this->data['locations'][$type][$country]);
								}
							}
						}
					}

					$this->query('UPDATE '.$this->Mint->db['tblPrefix']."visit SET country_code = 'XX' WHERE country_code = '--' OR country_code = ''");

				}

				if ($this->getInstalledVersion() == 223) {
					unset($this->data['locations']['total'][''], $this->data['locations']['unique']['']);
					$this->query('UPDATE '.$this->Mint->db['tblPrefix']."visit SET country_code = 'XX' WHERE country_code = ''");
				}

				if ($this->getInstalledVersion() < 230) {
					unset($this->data['locations']['total']['XX'], $this->data['locations']['unique']['XX']);
				}

				if ($this->getInstalledVersion() < 236) {
					$this->onInstall();
				}
				
				if ($this->getInstalledVersion() < 245) {
					$this->prefs['apache'] = 0;
				}

			}

		}

		function isCompatible() {

			if ($this->Mint->version < 215) {
				return array('isCompatible' => FALSE, 'explanation' => '<p>This Pepper requires Mint 2.15 or higher.</p>');
			} elseif (!$this->get_countrycode(TRUE)) {
				return array('isCompatible' => FALSE, 'explanation' => '<p>Location\'s database is either corrupted, or not readable by Mint.</p>');
			} else {
				return array('isCompatible' => TRUE);
			}

		}

		function onInstall() {

			if (isset($this->Mint->cfg['pepperLookUp']['SI_SecretCrush'])) {

				$SecretCrush =& $this->Mint->getPepperByClassName('SI_SecretCrush');
				$SecretCrush->register($this->pepperId);

			}

		}

		function onUninstall() {

			if (isset($this->Mint->cfg['pepperLookUp']['SI_SecretCrush'])) {

				$SecretCrush =& $this->Mint->getPepperByClassName('SI_SecretCrush');
				$SecretCrush->unregister($this->pepperId);

			}
		}

		function onRecord() {

			$code = FALSE;

			if ($this->prefs['apache']) {
				$code = apache_note('GEOIP_COUNTRY_CODE');
			} else {
				$code = $this->get_countrycode();
			}

			if ($code) {

				$this->data['locations']['total'][$code] = isset($this->data['locations']['total'][$code]) ? $this->data['locations']['total'][$code] + 1 : 1;

				if ($this->Mint->acceptsCookies && !isset($_COOKIE['MintUniqueLocation'])) {

					$this->Mint->bakeCookie('MintUniqueLocation', 1, time() + 315360000);

					$this->data['locations']['unique'][$code] = isset($this->data['locations']['unique'][$code]) ? $this->data['locations']['unique'][$code] + 1 : 1;

				}

				return array('country_code' => $code);

			}

		}

		function onSecretCrushMeta($session) {

			if ($result = $this->query('SELECT country_code FROM '.$this->Mint->db['tblPrefix'].'visit WHERE session_checksum = '.$session.' ORDER BY dt DESC LIMIT 1')) {

				$row = mysql_fetch_assoc($result);
				return array('Location' => $this->get_countryname($row['country_code']));

			}

		}

		function onDisplay($pane, $tab, $column = '', $sort = '') {

			switch ($pane) {
				case 'Locations': 
					switch ($tab) {
						case 'Most Common':
							return $this->build_mostcommon();
						break;
						case 'Most Recent':
							return $this->build_mostrecent();
						break;
					}
				break;
			}

		}

		function onDisplayPreferences() {

			$sortby0 = $this->prefs['sortby'] == 0 ? ' selected="selected"' : '';
			$sortby1 = $this->prefs['sortby'] == 1 ? ' selected="selected"' : '';
			$threshold = $this->prefs['threshold'];
			$apache = $this->prefs['apache'] ? ' checked="checked"' : '';

			$preferences['Display'] = <<<HTML
<table class="snug">
	<tr>
		<th>Order countries by</th>
		<td><span><select name="locations_sortby"><option value="0"{$sortby0}>Total hits</option><option value="1"{$sortby1}>Unique visits</option></select></span></td>
	</tr>
</table>
<table class="snug">
	<tr>
		<td>Fade countries smaller than</td>
		<td><span class="inline"><input type="text" name="locations_threshold" value="{$threshold}" class="cinch" /></span></td>
		<td>percent</td>
	</tr>
</table>
HTML;

			$preferences['Performance'] = <<<HTML
<table>
	<tr>
		<td>
			<label>
				<input type="checkbox" name="locationsplus_apache" value="1"{$apache} />
				Use MaxMind's mod_geoip.
			</label>
		</td>
	</tr>
</table>
HTML;

			return $preferences;

		}

		function onSavePreferences() {

			if (isset($_POST['locations_sortby']) && is_numeric($_POST['locations_sortby'])) {
				$this->prefs['sortby'] = $_POST['locations_sortby'];
			}

			if (isset($_POST['locations_threshold']) && is_numeric($_POST['locations_threshold'])) {
				$this->prefs['threshold'] = $_POST['locations_threshold'];
			}

			$this->prefs['apache'] = isset($_POST['locationsplus_apache']) ? 1 : 0;

		}

		function build_mostcommon() {

			$sortby = $this->prefs['sortby'] ? 'unique' : 'total';
			$irrelevant = $this->prefs['sortby'] ? 'total' : 'unique';
			$locations = $this->data['locations'];

			arsort($locations[$sortby]);

			$countries = array(); $total = 0; $i = 0;

			foreach ($locations[$sortby] as $code => $hits) {
				$countries[$this->get_countryname($code)][$sortby] = $hits;
				$total += $hits;
			}

			foreach ($locations[$irrelevant] as $code => $hits) {
				$countries[$this->get_countryname($code)][$irrelevant] = $hits;
			}

			$table_data['thead'] = array(array('value' => '% of Total', 'class' => 'sort'), array('value' => 'Country', 'class' => 'focus'), array('value' => '<abbr title="Total Page Views">Total</abbr>', 'class' => 'sort'), array('value' => '<abbr title="Unique Visitors">Unique</abbr>', 'class' => 'sort'));

			foreach ($countries as $name => $hits) {

				if ($i == $this->Mint->cfg['preferences']['rows']) {
					break;
				}

				$percent = $hits[$sortby] / $total * 100; $i++;

				$row = array($this->Mint->formatPercents($percent), $name, $hits['total'], (isset($hits['unique']) ? $hits['unique'] : '-'));

				if (round($percent, 5) < $this->prefs['threshold'] || $name == 'Unknown') {
					$row['class'] = 'insig';
				}

				$table_data['tbody'][] = $row;

			}

			return $this->Mint->generateTable($table_data);

		}

		function build_mostrecent() {

			$table_data['thead'] = array(array('value' => 'Country', 'class' => 'focus'), array('value' => 'When', 'class' => 'sort'));

			$secret_crush = isset($this->Mint->cfg['pepperLookUp']['SI_SecretCrush']);

			if ($secret_crush) {
				array_unshift($table_data['thead'], array('value' => '&nbsp;', 'class' => 'search'));
				$SecretCrush =& $this->Mint->getPepperByClassName('SI_SecretCrush');
			}

			if ($result = $this->query('SELECT dt '.($secret_crush ? ', ip_long' : NULL).', country_code FROM '.$this->Mint->db['tblPrefix'].'visit ORDER BY dt DESC LIMIT 0, '.$this->Mint->cfg['preferences']['rows'])) {

				while ($row = mysql_fetch_assoc($result)) {

					$class = $row['country_code'] == 'XX' ? 'insig' : '';

					if ($secret_crush) {
						$table_data['tbody'][] = array($SecretCrush->generateSearchIcon(long2ip($row['ip_long']), TRUE), $this->get_countryname($row['country_code']), $this->Mint->formatDateTimeRelative($row['dt']), 'class' => $class);
					} else {
						$table_data['tbody'][] = array($this->get_countryname($row['country_code']), $this->Mint->formatDateTimeRelative($row['dt']), 'class' => $class);
					}

				}

			}

			return $this->Mint->generateTable($table_data);

		}

		function get_countryname($code) {

			if (!isset($this->countries)) {
				require_once dirname(__FILE__).'/countries.php';
				$this->countries = $TK_CountryTable;
			}

			return $this->countries[$code];

		}

		function get_countrycode($check = FALSE) {

			$fp = fopen(dirname(__FILE__).'/database.dat', 'rb');

			if (fread($fp, 4) != 'TKdb') {
				return FALSE;
			}

			if ($check) {
				return TRUE;
			}

			$ip = FALSE;

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}

			if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
			}

			if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

				$ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);

				if ($ip) {
					array_unshift($ips, $ip);
					$ip = FALSE;
				}

				for ($i = 0; $i < count($ips); $i++) {
					if (!preg_match('/^(?:10|172\.(?:1[6-9]|2\d|3[01])|192\.168)\./', $ips[$i])) {
						if (version_compare(phpversion(), '5.0.0', '>=')) {
							if (ip2long($ips[$i]) != FALSE) {
								$ip = $ips[$i];
								break;
							}
						} else {
							if (ip2long($ips[$i]) != -1) {
								$ip = $ips[$i];
								break;
							}
						}
					}
				}

			}

			$ip = $ip ? $ip : $_SERVER['REMOTE_ADDR'];

			fseek($fp, $this->read_long($fp));
			$records = sprintf('%u', $this->read_long($fp));
			$min = sprintf('%u', $this->read_long($fp));
			$max =  sprintf('%u', $this->read_long($fp));
			$recsize = $this->read_byte($fp);
			$countries = $this->read_short($fp);

			$temp = fread($fp, $countries * 2);

			for ($i = 0; $i < $countries; $i++) {
				$countryname[] = substr($temp, $i * 2, 2);
			}

			$minip = $this->read_byte($fp);
			$maxip = $this->read_byte($fp);

			for ($i = 0; $i < 256; $i++) {
				$topidx[$i] = -1;
			}

			for ($i = $minip; $i <= $maxip; $i++) {
				$topidx[$i] = $this->read_long($fp);
			}

			$list = split('\.', $ip);
			$aclass = $list[0];
			$ip = sprintf('%u', ip2long($ip));

			if ($ip < $min || $ip > $max || $topidx[$aclass] < 0) {
				$index = -1;
			}

			if ($aclass == $maxip) {
				$top = $records;
				$bottom = abs($topidx[$aclass]) - 1;
			} else {
				$bottom = abs($topidx[$aclass]) - 1;
				$i = 1;
				while ($topidx[$aclass + $i] < 0) {
					$i++;
				}
				$top = $topidx[$aclass + $i];
			}

			if ($aclass == $minip) {
				$bottom = 0;
			}

			$oldtop = -1;
			$oldbot = -1;
			$nextrecord = floor(($top + $bottom) / 2);

			if ($ip == $min) {
				fseek($fp, 16);
				$index = $this->read_short($fp);
			} elseif ($ip == $max) {
				fseek($fp, $records * $recsize - $recsize + 16);
				$index = $this->read_short($fp);
			}

			$cnt = 0;
			while (!FALSE) {

				$cnt++;
				fseek($fp, $nextrecord * $recsize + 8);
				$start = sprintf( '%u', $this->read_long($fp));

				if ($ip < $start) {
					$top = $nextrecord;
				} else {
					$end = sprintf('%u', $this->read_long($fp));
					if ($ip > $end) {
						$bottom = $nextrecord;
					} else {
						$index = $this->read_short($fp);
						break;
					}
				}

				$nextrecord = floor(($top + $bottom) / 2);
				if ($top == $oldtop && $bottom == $oldbot) {
					$index = -1;
					break;
				}

				$oldtop = $top;
				$oldbot = $bottom;

			}

			fclose($fp);

			if ($index >= 0 && $index < $countries) {
				return $countryname[$index];
			}

		}

		function read_long($fp) {

			$string = fread($fp, 4);
			return ord($string[3]) << 24 | ord($string[2]) << 16 | ord($string[1]) << 8 | ord($string[0]);

		}

		function read_short($fp) {

			$string = fread($fp, 2);
			return ord($string[1]) << 8 | ord($string[0]);

		}

		function read_byte($fp) {

			$string = fread($fp, 1);
			return ord($string[0]);

		}

	}
