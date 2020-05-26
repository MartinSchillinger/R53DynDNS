<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use DynR53\IP;
use DynR53\R53Updater;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$startTime = microtime(true);

// Get current external IP Addresses and compare them to the last ones
$currentIpv4 = IP::getExternalv4();
$currentIpv6 = IP::getExternalv6();

$oldIps = json_decode(file_get_contents("ip.json"), true);
$oldIpv4 = $oldIps["ipv4"];
$oldIpv6 = $oldIps["ipv6"];

if ($oldIpv4 === $currentIpv4 && $oldIpv6 === $currentIpv6) {
    die("Data didn't change.");
}

$result = [
    "ipv4" => $currentIpv4,
    "ipv6" => $currentIpv6
];

$updater = new R53Updater();

$updater->updateDnsRecord($result["ipv4"], $result["ipv6"]);


file_put_contents("ip.json", json_encode($result));
echo "\nWrote new IP addresses to file";


$time = microtime(true) - $startTime;

echo "\n Update took: $time seconds";
//echo $currentIpv4 . '\n' . $external_v6;
