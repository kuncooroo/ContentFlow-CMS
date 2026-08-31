<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Queries\Content\PublicPostIndexQuery;
use App\Support\Comments\CommentsGate;
use App\Support\Seo\SeoResolver;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{
    public function index(): View
    {
        return view('public.posts.index', [
            'posts' => app(PublicPostIndexQuery::class)->paginate(),
            'seo' => app(SeoResolver::class)->resolveForListing('Blog'),
        ]);
    }

    public function show(Post $post): View
    {
        if (! $post->isPubliclyVisible()) {
            throw new NotFoundHttpException();
        }

        $post->load(['author', 'approvedComments' => fn ($query) => $query->latest()]);

        return view('public.posts.show', [
            'post' => $post,
            'commentsEnabled' => CommentsGate::enabled(),
            'seo' => app(SeoResolver::class)->resolveForPost($post),
        ]);
    }
}
