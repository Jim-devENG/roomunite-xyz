<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/admin/ajax-icalender-import/*',
        'users/veriff-complete',
        'users/veriff-process',
        'admin/authenticate', // Temporarily excluded for local development - CSRF token issue
        //
    ];
}
