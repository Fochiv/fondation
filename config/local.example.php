<?php
/**
 * Copy this file to config/local.php on a Hostinger account.
 * Keep local.php private and never commit it or publish its values.
 */
return [
    'DB_HOST' => '',
    'DB_PORT' => '3306',
    'DB_NAME' => '',
    'DB_USER' => '',
    'DB_PASSWORD' => '',
    // Generate separate random values of at least 32 characters for production.
    'SESSION_SECRET' => '',
    'ADMIN_SETUP_KEY' => '',
    // Optional first-admin credentials; remove both after initial account creation.
    'ADMIN_EMAIL' => '',
    'ADMIN_INITIAL_PASSWORD' => '',
];
