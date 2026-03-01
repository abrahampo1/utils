<?php

return [
    'host' => env('PLESK_HOST'),
    'api_key' => env('PLESK_API_KEY'),
    'verify_ssl' => env('PLESK_VERIFY_SSL', false),
    'cache_ttl' => env('PLESK_CACHE_TTL', 300),

    'rest_url' => env('PLESK_HOST') ? 'https://' . env('PLESK_HOST') . ':8443/api/v2' : null,
    'xmlrpc_url' => env('PLESK_HOST') ? 'https://' . env('PLESK_HOST') . ':8443/enterprise/control/agent.php' : null,

    'ssh_user' => env('PLESK_SSH_USER', 'root'),
    'ssh_key_path' => env('PLESK_SSH_KEY_PATH'),
    'ssh_password' => env('PLESK_SSH_PASSWORD'),
];
