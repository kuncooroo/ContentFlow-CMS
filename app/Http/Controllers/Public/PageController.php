<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Seo\SeoResolver;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        if (! $page->isPubliclyVisible()) {
            throw new NotFoundHttpException();
        }

        return view('public.pages.show', [
            'page' => $page,
            'seo' => app(SeoResolver::class)->resolveForPage($page),
        ]);
    }
}
