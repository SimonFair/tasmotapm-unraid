<?php
$tasmotapm_cfg = parse_ini_file( "/boot/config/plugins/tasmotapm/tasmotapm.cfg" );
$tasmotapm_device_ip1	= isset($tasmotapm_cfg['DEVICE_IP']) ? $tasmotapm_cfg['DEVICE_IP'] : "";
$tasmotapm_device_ip2	= isset($tasmotapm_cfg['DEVICE_IP2']) ? $tasmotapm_cfg['DEVICE_IP2'] : "";
$tasmotapm_device_ip3	= isset($tasmotapm_cfg['DEVICE_IP3']) ? $tasmotapm_cfg['DEVICE_IP3'] : "";
$tasmotapm_device_name1	= isset($tasmotapm_cfg['DEVICE_NAME1']) ? $tasmotapm_cfg['DEVICE_NAME1'] : "";
$tasmotapm_device_name2	= isset($tasmotapm_cfg['DEVICE_NAME2']) ? $tasmotapm_cfg['DEVICE_NAME2'] : "";
$tasmotapm_device_name3	= isset($tasmotapm_cfg['DEVICE_NAME3']) ? $tasmotapm_cfg['DEVICE_NAME3'] : "";
$tasmotapm_use_pass	= isset($tasmotapm_cfg['DEVICE_USE_PASS']) ? $tasmotapm_cfg['DEVICE_USE_PASS'] : "false";
$tasmotapm_device_user	= isset($tasmotapm_cfg['DEVICE_USER']) ? $tasmotapm_cfg['DEVICE_USER'] : "";
$tasmotapm_device_pass	= isset($tasmotapm_cfg['DEVICE_PASS']) ? $tasmotapm_cfg['DEVICE_PASS'] : "";
$tasmotapm_costs_price	= isset($tasmotapm_cfg['COSTS_PRICE']) ? $tasmotapm_cfg['COSTS_PRICE'] : "0.0";
$tasmotapm_costs_unit	= isset($tasmotapm_cfg['COSTS_UNIT']) ? $tasmotapm_cfg['COSTS_UNIT'] : "USD";


if ($tasmotapm_device_ip1 == "") {
	die("Tasmota Device IP missing!");
}

$count = 1;

do {

if (${"tasmotapm_device_ip".$count} != "") {	
$Url = "http://" . ${"tasmotapm_device_ip".$count} . "/cm?";

if ($tasmotapm_use_pass == 1) {
	if ($tasmotapm_device_user == "") {
		die("Tasmota username missing!");
	}
	if ($tasmotapm_device_pass == "") {
		die("Tasmota password missing!");
	}

	$Url = $Url . "user=" . $tasmotapm_device_user . "&password=" . $tasmotapm_device_pass . "&";
}

$Url = $Url . "cmnd=Status%208";

$datajson = @file_get_contents($Url);
#$datajson = @file_get_contents("/tmp/File".${"tasmotapm_device_ip".$count});
$data = json_decode($datajson, true); 

$json[$count] = array(
		'Total' => $data['StatusSNS']['ENERGY']['Total'] ?? null,
		'Today' => $data['StatusSNS']['ENERGY']['Today'] ?? null,
		'Yesterday' => $data['StatusSNS']['ENERGY']['Yesterday'] ?? null,
		'Voltage' => $data['StatusSNS']['ENERGY']['Voltage'] ?? null,
		'Current' => $data['StatusSNS']['ENERGY']['Current'] ?? null,
		'ApparentPower' => $data['StatusSNS']['ENERGY']['ApparentPower'] ?? null,
		'ReactivePower' => $data['StatusSNS']['ENERGY']['ReactivePower'] ?? null,
		'Factor' => $data['StatusSNS']['ENERGY']['Factor'] ?? null,
		'Power' => $data['StatusSNS']['ENERGY']['Power'] ?? null,
		'Costs_Price' => $tasmotapm_costs_price,
		'Costs_Unit' => $tasmotapm_costs_unit,
		'Name' => ${"tasmotapm_device_name".$count},
		'IP' => ${"tasmotapm_device_ip".$count},
	);

}

$count++;

} while ($count < 4);


header('Content-Type: application/json');
echo json_encode($json);
?>