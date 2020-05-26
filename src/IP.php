<?php


namespace DynR53;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;


class IP
{
    private const EMPTY_V6 = '::';
    private const EMPTY_V4 = '0.0.0.0';

    private static string $externalv4 = self::EMPTY_V4;
    private static string $externalv6 = self::EMPTY_V6;

    /**
     * @return string
     */
    public static function getExternalv4(): string
    {
        if (self::$externalv4 === self::EMPTY_V4) {
            self::setIps();
        };
        return self::$externalv4;
    }

    /**
     * @return string
     */
    public static function getExternalv6(): string
    {
        if (self::$externalv6 === self::EMPTY_V6) {
            self::setIps();
        };
        return self::$externalv6;
    }

    /**
     * Performs concurrent requests to get external ips and sets them as class attributes
     */
    private static function setIps()
    {
        $client = new Client();
        $promises = [
            'ipv4' => $client->getAsync('https://api.ipify.org/?format=json'),
            'ipv6' => $client->getAsync('https://api6.ipify.org/?format=json')
        ];
        $responses = null;
        try {
            $responses = Promise\unwrap($promises);
        } catch (\Throwable $e) {
            die("Error while fetching external IP addresses.");
        }
        $responses = Promise\settle($promises)->wait();

        self::$externalv4 = json_decode($responses['ipv4']['value']->getBody())->ip;
        self::$externalv6 = json_decode($responses['ipv6']['value']->getBody())->ip;

    }

}