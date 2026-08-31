<?php

namespace App\Enums;

enum MenuItemType: string
{
    case Page = 'page';
    case Post = 'post';
    case Category = 'category';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Page => 'Page',
            self::Post => 'Post',
            self::Category => 'Category',
            self::Custom => 'Custom URL',
        };
    }
}
