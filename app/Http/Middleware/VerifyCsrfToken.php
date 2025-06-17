<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
       'api/decrypt/cred',
       'api/encrypt/cred',
       'api/login',
       'api/user/create',
       'api/user/get-user-details',
       'api/user/update-user-info',
       'api/send-credentials-mail',
       'api/check-agreement'
    ];
}
