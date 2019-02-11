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
        'http://cdimage.debian.org/debian-cd/project/build/',
        ['connect_timeout' => 1, 'delay' => 1000, 'read_timeout' => 1, 'timeout' => 1, 'verify' => false]
    );
} catch (\Exception $exception) {
    exit('Impossible to retrieve versions.');
}

$versions = \array_filter(
    \array_map(
        function ($version) {
            return \substr(\trim($version->textContent), 0, -1);
        },
        \iterator_to_array($crawler->filterXPath('//a'))
    ),
    function ($version) {
        return 1 === \preg_match('`^[0-9.]+$`', $version);
    }
);
\usort($versions, 'version_compare');

// Generate files

$latestVersion = \end($versions);

$fs = new Filesystem();
$fs->dumpFile(
  'latest',
<<<EOF
DEBIAN_RELEASE="http://cdimage.debian.org/debian-cd/{$latestVersion}/amd64/iso-cd/debian-{$latestVersion}-amd64-netinst.iso"
DEBIAN_VERSION="{$latestVersion}"

EOF
);
