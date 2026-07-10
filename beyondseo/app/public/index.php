<?php
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Symfony\Kernels\DDDKernel;
use RankingCoach\Inc\Core\Helpers\WordpressHelpers;
use RankingCoach\Inc\Core\Initializers\Hooks;
use RankingCoach\Inc\Core\HooksManager;
return (function () {

    // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash

    // assign request URI
    $requestUri = isset($_SERVER['REQUEST_URI']) ? filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL) : '';
    if (str_starts_with($requestUri, '/wp-json/rankingcoach')) {
        // alter/change the request URI so Symfony can handle it properly
        $_SERVER['REQUEST_URI'] = str_replace('/wp-json/rankingcoach', '', $requestUri);
    }
    // re-assign request URI
    $requestUri = isset($_SERVER['REQUEST_URI']) ? filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL) : '';

    // we need to set https otherwise symfony will not notice that it is running https and when enforcing https
    $_SERVER['HTTPS'] = 'off';

    $routes = [
        '/api/' => 'api',
    ];
    $isSymfonyRoot = false;
    foreach ($routes as $route => $kernelPrefix) {
        if (str_starts_with($requestUri, $route)) {
            $isSymfonyRoot = true;
            break;
        }
    }

    // phpcs:enable WordPress.Security.ValidatedSanitizedInput.MissingUnslash

    if (!$isSymfonyRoot) {
        echo 'Not a Symfony root';
        exit;
    }

    $debug_param = WordpressHelpers::sanitize_input('GET', 'debug');
    if (!empty($debug_param)) {
        $debug = filter_var($debug_param, FILTER_VALIDATE_BOOLEAN);
        setcookie('beyondseo_symfony_debug', json_encode($debug), time() + 3600 * 24, '/');
        $_COOKIE['beyondseo_symfony_debug'] = $debug;
    }
    // Get the current working directory
    $currentDirectory = getcwd();

    $appPrefix = 'app';
    if (!defined('BEYONDSEO_APP_PREFIX')) {
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
        define('BEYONDSEO_APP_PREFIX', $appPrefix);
    }

    $debug = false;
    $cookie_debug = WordpressHelpers::sanitize_input('COOKIE', 'beyondseo_symfony_debug');
    if (!empty($cookie_debug)) {
        $debug = (bool)$cookie_debug;
    }

    $projectDir = realpath(__DIR__ . '/../');
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
    define('BEYONDSEO_APP_ROOT_DIR', $projectDir);

    require_once realpath(__DIR__ . '/../../') . '/inc/Core/Plugin/functions.php';
    require_once realpath(__DIR__ . '/../../') . '/inc/Core/Plugin/safe-polyfills.php';

    $loader = beyondseo_load_wrapped_autoloader(realpath(__DIR__ . '/../') . '/vendor/autoload.php');
    try {
        (new Hooks(new HooksManager()))->initialize();
    } catch (ReflectionException|Exception $e) {
        // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
        echo esc_html($e->getMessage());
        die();
    }

    $matchedKernelPrefix = 'api';
    return function (array $context) use ($projectDir, $debug, $matchedKernelPrefix) {
        try {
            $kernel = new DDDKernel($context['APP_ENV'], (bool)$context['APP_DEBUG'] && $debug);
            $kernel->setProjectDir($projectDir);
            $kernel->setKernelPrefix($matchedKernelPrefix);
            return $kernel;
        } catch (Exception $e) {
            // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
            echo esc_html($e->getMessage());
            die();
        }
    };
})();
