<?php

namespace App\Http\Controllers\Public;

use App\Actions\Comments\SubmitComment;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\Ui\Flash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        if (! $post->isPubliclyVisible()) {
            throw new NotFoundHttpException();
        }

        try {
            app(SubmitComment::class)->handle(
                post: $post,
                authorName: (string) $request->input('author_name'),
                authorEmail: (string) $request->input('author_email'),
                content: (string) $request->input('content'),
                honeypot: $request->input('website'),
            );
        } catch (ValidationException $exception) {
            return back()
                ->withInput($request->only('author_name', 'author_email', 'content'))
                ->withErrors($exception->errors());
        }

        return back()->with(Flash::SUCCESS, 'Thank you. Your comment is awaiting moderation.');
    }
}
