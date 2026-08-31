<?php

use App\Http\Controllers\Admin\PagePreviewController;
use App\Http\Controllers\Admin\PostPreviewController;
use App\Livewire\Admin\Audit\Index as AuditIndex;
use App\Livewire\Admin\Pages\Create as PagesCreate;
use App\Livewire\Admin\Pages\Edit as PagesEdit;
use App\Livewire\Admin\Pages\Index as PagesIndex;
use App\Livewire\Admin\Posts\Create as PostsCreate;
use App\Livewire\Admin\Posts\Edit as PostsEdit;
use App\Livewire\Admin\Posts\Index as PostsIndex;
use App\Livewire\Admin\Comments\Index as CommentsIndex;
use App\Livewire\Admin\Dashboard\Overview as DashboardOverview;
use App\Livewire\Admin\Media\Edit as MediaEdit;
use App\Livewire\Admin\Menus\Edit as MenusEdit;
use App\Livewire\Admin\Menus\Index as MenusIndex;
use App\Livewire\Admin\Media\Index as MediaIndex;
use App\Livewire\Admin\Taxonomy\Tags\Create as TagsCreate;
use App\Livewire\Admin\Taxonomy\Tags\Edit as TagsEdit;
use App\Livewire\Admin\Taxonomy\Tags\Index as TagsIndex;
use App\Livewire\Admin\Taxonomy\Categories\Create as CategoriesCreate;
use App\Livewire\Admin\Taxonomy\Categories\Edit as CategoriesEdit;
use App\Livewire\Admin\Taxonomy\Categories\Index as CategoriesIndex;
use App\Livewire\Admin\Roles\Edit as RolesEdit;
use App\Livewire\Admin\Roles\Index as RolesIndex;
use App\Livewire\Admin\Settings\General as SettingsGeneral;
use App\Livewire\Admin\Users\Create as UsersCreate;
use App\Livewire\Admin\Users\Edit as UsersEdit;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Protected by session authentication. Full auth UI lands in TASK-002.
|
*/

Route::middleware(['auth', EnsureUserIsActive::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::livewire('/dashboard', DashboardOverview::class)->name('dashboard');

        Route::livewire('/users', UsersIndex::class)->name('users.index');
        Route::livewire('/users/create', UsersCreate::class)->name('users.create');
        Route::livewire('/users/{user}/edit', UsersEdit::class)->name('users.edit');

        Route::livewire('/roles', RolesIndex::class)->name('roles.index');
        Route::livewire('/roles/{role}/edit', RolesEdit::class)->name('roles.edit');

        Route::livewire('/categories', CategoriesIndex::class)->name('categories.index');
        Route::livewire('/categories/create', CategoriesCreate::class)->name('categories.create');
        Route::livewire('/categories/{category}/edit', CategoriesEdit::class)->name('categories.edit');

        Route::livewire('/tags', TagsIndex::class)->name('tags.index');
        Route::livewire('/tags/create', TagsCreate::class)->name('tags.create');
        Route::livewire('/tags/{tag}/edit', TagsEdit::class)->name('tags.edit');

        Route::livewire('/media', MediaIndex::class)->name('media.index');
        Route::livewire('/media/{media}/edit', MediaEdit::class)->name('media.edit');

        Route::livewire('/posts', PostsIndex::class)->name('posts.index');
        Route::livewire('/posts/create', PostsCreate::class)->name('posts.create');
        Route::get('/posts/{post}/preview', PostPreviewController::class)->name('posts.preview');
        Route::livewire('/posts/{post}/edit', PostsEdit::class)->name('posts.edit');

        Route::livewire('/pages', PagesIndex::class)->name('pages.index');
        Route::livewire('/pages/create', PagesCreate::class)->name('pages.create');
        Route::get('/pages/{page}/preview', PagePreviewController::class)->name('pages.preview');
        Route::livewire('/pages/{page}/edit', PagesEdit::class)->name('pages.edit');

        Route::livewire('/comments', CommentsIndex::class)->name('comments.index');

        Route::livewire('/menus', MenusIndex::class)->name('menus.index');
        Route::livewire('/menus/{menu}/edit', MenusEdit::class)->name('menus.edit');

        Route::livewire('/settings', SettingsGeneral::class)->name('settings.general');

        Route::livewire('/audit', AuditIndex::class)->name('audit.index');
    });
