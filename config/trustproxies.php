<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Set trusted proxy IP addresses.
    |
    | Both IPv4 and IPv6 addresses are
    | supported, as well as CIDR notation.
    |
    | The "*" character is syntactic sugar
    | within TrustedProxy to trust any proxy;
    | a requirement when you cannot know the address
    | of your proxy (e.g. if using Rackspace balancers).
    |
    */

    'proxies' => '*',

    /*
    |--------------------------------------------------------------------------
    | Trusted Headers
    |--------------------------------------------------------------------------
    |
    | The headers that should be trusted when using a proxy.
    |
    | Available options:
    |   - HEADER_X_FORWARDED_ALL
    |   - HEADER_X_FORWARDED_AWS_ELB
    |   - HEADER_X_FORWARDED_FOR
    |   - HEADER_X_FORWARDED_HOST
    |   - HEADER_X_FORWARDED_PORT
    |   - HEADER_X_FORWARDED_PROTO
    |   - HEADER_FORWARDED
    |
    | Default: HEADER_X_FORWARDED_ALL
    */

    'headers' => \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_ALL,

];