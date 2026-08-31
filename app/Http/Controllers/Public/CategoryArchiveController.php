<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Queries\Content\PublicPostIndexQuery;
use App\Support\Seo\SeoResolver;
use Illuminate\Contracts\View\View;

class CategoryArchiveController extends Controller
{
    public function show(Category $category): View
    {
        return view('public.archives.category', [
            'category' => $category,
            'posts' => app(PublicPostIndexQuery::class)->paginate(categoryId: $category->id),
            'seo' => app(SeoResolver::class)->resolveForListing($category->name),
        ]);
    }
}
