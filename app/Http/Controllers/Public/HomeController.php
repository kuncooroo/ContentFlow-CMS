<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Queries\Content\PublicPostIndexQuery;
use App\Support\Seo\SeoResolver;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'posts' => app(PublicPostIndexQuery::class)->latest(),
            'seo' => app(SeoResolver::class)->resolveForSite(),
        ]);
    }
}
