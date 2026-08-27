<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function authorizeDownloads(Request $request): void
    {
        abort_unless($request->user()?->canViewMenuItem('downloads') ?? false, 403);
    }
}
