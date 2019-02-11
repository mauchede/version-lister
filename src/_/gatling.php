<?php
<<<CONFIG
packages:
    - "fabpot/goutte: ^3.2"
    - "symfony/filesystem: ^3.2"
CONFIG;

use Goutte\Client;
use Symfony\Component\Filesystem\Filesystem;

// Configure HTTP Client

$client = new Client();

// Retrieve versions

try {
    $crawler = $client->request(
        'GET',
        'https://repo1.maven.org/maven2/io/gatling/highcharts/gatling-charts-highcharts-bundle/',
        ['connect_timeout' => 1, 'delay' => 1000, 'read_timeout' => 1, 'timeout' => 1, 'verify' => false]
    );
} catch (\Exception $exception) {
    exit('Impossible to retrieve versions.');
}

$versions = \array_filter(
    \array_map(
        function ($link) {
            if (1 === \preg_match('`^([0-9.]+)/$`', $link->textContent, $matches)) {
                return $matches[1];
            }

            return '';
        },
        \iterator_to_array($crawler->filterXPath('//a'))
    ),
    function ($version) {
        return 1 === \preg_match('`^([0-9](\.[0-9])+)$`', $version);
    }
);
\usort($versions, 'version_compare');

// Generate files

$latestVersion = \end($versions);

$fs = new Filesystem();
$fs->dumpFile(
  'latest',
<<<EOF
GATLING_RELEASE="https://repo1.maven.org/maven2/io/gatling/highcharts/gatling-charts-highcharts-bundle/{$latestVersion}/gatling-charts-highcharts-bundle-{$latestVersion}-bundle.zip"
GATLING_VERSION="{$latestVersion}"

EOF
);
