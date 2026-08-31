<?php

namespace App\Support\Audit;

use App\Models\Comment;
use App\Models\Media;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class SubjectType
{
    public static function forModel(Model $model): string
    {
        return match ($model::class) {
            User::class => 'user',
            Role::class => 'role',
            Media::class => 'media',
            Post::class => 'post',
            Page::class => 'page',
            Comment::class => 'comment',
            Menu::class => 'menu',
            SiteSetting::class => 'site_setting',
            default => throw new InvalidArgumentException('Unsupported audit subject: '.$model::class),
        };
    }
}
