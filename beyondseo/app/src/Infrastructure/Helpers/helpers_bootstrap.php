<?php
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// src/Infrastructure/Helpers/helpers_bootstrap.php
foreach (glob(__DIR__ . '/*.php') as $beyondseo_helperFile) {
    if ($beyondseo_helperFile === __DIR__ . '/helpers_bootstrap.php') {
        continue; // Skip the bootstrap file itself
    }
    require_once $beyondseo_helperFile;
}