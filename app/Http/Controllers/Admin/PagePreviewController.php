<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class PagePreviewController extends Controller
{
    public function __invoke(Page $page): View
    {
        $this->authorize('preview', $page);

        return view('admin.pages.preview', [
            'page' => $page->load(['author', 'ogMedia']),
        ]);
    }
}
