<?php


namespace DynR53;

use Aws\Route53\Route53Client as R53Client;
use Aws\Credentials\Credentials;

class R53Updater
{
    public function updateDnsRecord($ipv4, $ipv6)
    {
        $r53 = new R53Client([
            'version' => 'latest',
            'region' => getenv('AWS_REGION'),
            "credentials" => $this->getCredentials()
        ]);

        $result = $r53->changeResourceRecordSets([
            'ChangeBatch' => [
                'Changes' => [
                    [
                        'Action' => 'UPSERT',
                        'ResourceRecordSet' => [
                            'Name' => getenv('AWS_RECORDSET_NAME'),
                            'ResourceRecords' => [
                                [
                                    'Value' => $ipv4,
                                ],
                            ],
                            'TTL' => 60,
                            'Type' => 'A',
                        ],
                    ],
                    [
                        'Action' => 'UPSERT',
                        'ResourceRecordSet' => [
                            'Name' => getenv('AWS_RECORDSET_NAME'),
                            'ResourceRecords' => [
                                [
                                    'Value' => $ipv6,
                                ],
                            ],
                            'TTL' => 60,
                            'Type' => 'AAAA',
                        ],
                    ],
                ],
                'Comment' => getenv('AWS_UPSERT_COMMENT'),
            ],
            'HostedZoneId' => getenv('AWS_HOSTED_ZONE_ID'),
        ]);

        echo "\n DNS Record updated";
    }

    private function getCredentials(): Credentials
    {
        return new Credentials(getenv('AWS_KEY'), getenv('AWS_SECRET'));
    }
}