<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Queries\Content\PublicPostIndexQuery;
use App\Support\Seo\SeoResolver;
use Illuminate\Contracts\View\View;

class TagArchiveController extends Controller
{
    public function show(Tag $tag): View
    {
        return view('public.archives.tag', [
            'tag' => $tag,
            'posts' => app(PublicPostIndexQuery::class)->paginate(tagId: $tag->id),
            'seo' => app(SeoResolver::class)->resolveForListing($tag->name),
        ]);
    }
}
