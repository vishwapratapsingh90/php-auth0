<?php

declare(strict_types=1);

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;

require __DIR__ . '/autoload.php';

function environment(string $name, string $default = ''): string
{
    $value = getenv($name);

    return false === $value ? $default : trim($value);
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$baseUrl = rtrim(environment('AUTH0_BASE_URL', 'http://localhost:3000'), '/');
$appEnvironment = environment('APP_ENV', 'production');
$debug = filter_var(environment('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
$domain = environment('AUTH0_DOMAIN');
$clientId = environment('AUTH0_CLIENT_ID');
$clientSecret = environment('AUTH0_CLIENT_SECRET');
$cookieSecret = environment('AUTH0_COOKIE_SECRET');
$adConnection = environment('AUTH0_AD_CONNECTION');
$nonAdConnection = environment('AUTH0_NON_AD_CONNECTION');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$action = is_string($_GET['action'] ?? null) ? $_GET['action'] : '';
$provider = is_string($_GET['provider'] ?? null) ? $_GET['provider'] : '';

error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');

if ('' === $domain || '' === $clientId || '' === $clientSecret || '' === $cookieSecret) {
    getView('authentication', 'index', [
        'view' => 'setup',
    ]);
}

$configuration = new SdkConfiguration(
    domain: $domain,
    clientId: $clientId,
    clientSecret: $clientSecret,
    redirectUri: $baseUrl . '/callback',
    cookieSecret: $cookieSecret,
);
$auth0 = new Auth0($configuration);

try {
    if ('/callback' === $path && null !== $auth0->getExchangeParameters()) {
        $auth0->exchange();
        header('Location: ' . $baseUrl . '/');
        exit;
    }

    if ('login' === $action) {
        $connections = [
            'ad' => $adConnection,
            'non_ad' => $nonAdConnection,
        ];

        if (!array_key_exists($provider, $connections) || '' === $connections[$provider]) {
            getView('authentication', 'index', [
                'view' => 'error',
                'title' => 'Auth0 configuration',
                'errorMessage' => 'Set the connection environment variable for this login option.',
            ]);
        }

        header('Location: ' . $auth0->login(null, ['connection' => $connections[$provider]]));
        exit;
    }

    if ('logout' === $action) {
        header('Location: ' . $auth0->logout($baseUrl . '/'));
        exit;
    }

    $session = $auth0->getCredentials();
} catch (Throwable $exception) {
    error_log(sprintf('[%s] %s', $appEnvironment, $exception->__toString()));
    http_response_code(500);
    getView('authentication', 'index', [
        'view' => 'error',
        'title' => 'Auth0 error',
        'errorMessage' => $debug
            ? $exception->getMessage()
            : 'Authentication could not be completed. Check the application logs.',
    ]);
}

if (null === $session || $session->accessTokenExpired) {
    getView('authentication', 'index', [
        'view' => 'login',
        'baseUrl' => $baseUrl,
        'adConnection' => $adConnection,
        'nonAdConnection' => $nonAdConnection,
    ]);
}

$user = is_array($session->user) ? $session->user : [];

getView('authentication', 'index', [
    'view' => 'profile',
    'baseUrl' => $baseUrl,
    'user' => $user,
]);
