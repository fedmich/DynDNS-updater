<?php
/* DynDNS updater 0.1 */

/* config */
$user_name = '';
$password = '';
$host = 'host.dyndns-free.com';

/* config */

if (isset($_GET['update'])) {
	echo 'updating dyndns <BR />';

	function curl_page($url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		}

		$curlResult = curl_exec($curl);
		$curl_info = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
		$httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);
		if ($httpStatus == 404) {
			return '';
		}
		return $curlResult;
	}

	$url = "http://checkip.dyndns.org/";
	$get_ip = curl_page($url);
	if ($get_ip) {
		preg_match_all("@Current IP Address: (\d.{0,3}\.\d{0,2}\.\d.{0,2}\.\d.{0,2})@isu", $get_ip, $ms);
	}
	$myip = empty($ms[1][0]) ? '' : $ms[1][0];
	if ($myip) {
		?>
		<b>
			<?= $myip; ?>
		</b>
		<br />
		<br />

		<?php
		$url = "http://$user_name:$password@members.dyndns.org/nic/update?hostname=$host"
				. "&wildcard=NOCHG&mx=NOCHG&backmx=NOCHG&myip=$myip";

		$response = curl_page($url);
		?>
		response:
		<br />
		<?php
		echo $response;
		?>
		<br />
		<?php
		if (
				stristr($response, 'nochg')
				or stristr($response, "good $myip")
		) {
			?>
			<br />
			<b>success!</b>
			<?php
		}
	}

	exit();
}
?>
<p>
	Click the link below to start updating dyndns IP address...
</p>
<a href="./?update">Update now</a>