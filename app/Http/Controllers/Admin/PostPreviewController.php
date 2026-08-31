<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class PostPreviewController extends Controller
{
    public function __invoke(Post $post): View
    {
        $this->authorize('preview', $post);

        return view('admin.posts.preview', [
            'post' => $post->load(['author', 'categories', 'tags', 'featuredMedia']),
        ]);
    }
}
