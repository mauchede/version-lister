<?php
<<<CONFIG
packages:
    - "guzzlehttp/guzzle: ^6.2"
    - "symfony/filesystem: ^3.2"
CONFIG;

use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;

// Configure HTTP Client

$headers = [];
if (false !== \getenv('GITHUB_TOKEN')) {
    $headers['Authorization'] = \sprintf('token %s', \getenv('GITHUB_TOKEN'));
}
$client = new Client(
    [
        'headers' => $headers,
    ]
);

// Retrieve versions

try {
    $response = $client->get('https://api.github.com/repos/docker/compose/git/refs/tags');
} catch (\Exception $exception) {
    exit('Impossible to retrieve versions.');
}

$versions = \array_filter(
    \array_map(
        function ($version) {
            return \preg_replace('`refs/tags/(release-|v)?`', '', $version['ref']);
        },
        \json_decode($response->getBody()->getContents(), true)
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
COMPOSE_RELEASE="https://github.com/docker/compose/releases/download/${latestVersion}/docker-compose-linux-x86_64"
COMPOSE_SOURCE="https://github.com/docker/compose/archive/${latestVersion}.tar.gz"
COMPOSE_VERSION="${latestVersion}"

EOF
);
