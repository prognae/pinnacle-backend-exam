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
        //
        '/register',
        '/login',
        '/api/users/update/*',
        '/api/users/delete/*',
        '/api/posts/create',
        '/api/posts/update/*',
        '/api/posts/delete/*'
    ];
}
