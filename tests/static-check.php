<?php

$root = dirname(__DIR__);
$module = file_get_contents($root . '/leancookieconsent/leancookieconsent.php');
$template = file_get_contents($root . '/leancookieconsent/views/templates/hook/header.tpl');
$config = simplexml_load_file($root . '/leancookieconsent/config.xml');

assert_contains($module, "registerHook('displayHeader')");
assert_contains($module, "private const RUNTIME_BASE_URL = 'https://api.leancookieconsent.com'");
assert_contains($module, 'LEAN_COOKIE_CONSENT_SITE_KEY');
assert_contains($module, 'rawurlencode($siteKey)');
assert_contains($module, 'preg_match(\'/^[A-Za-z0-9_-]+$/\', $siteKey)');
assert_contains($template, 'window.LeanCookieConsentPrestaShop');
assert_contains($template, '{$lean_runtime_url|escape:\'html\':\'UTF-8\'}');

if ((string) $config->name !== 'leancookieconsent') {
    fail('config.xml module name mismatch');
}

if ((string) $config->version !== '0.1.0') {
    fail('config.xml version mismatch');
}

if (strpos($module, 'Tools::getValue(\'api') !== false || strpos($module, 'Tools::getValue(\'url') !== false) {
    fail('Module must not expose configurable API URL fields');
}

echo "PrestaShop module static checks passed\n";

function assert_contains(string $haystack, string $needle): void
{
    if (strpos($haystack, $needle) === false) {
        fail("Missing expected content: {$needle}");
    }
}

function fail(string $message): void
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}
