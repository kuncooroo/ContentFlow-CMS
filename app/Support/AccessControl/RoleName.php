<?php

namespace App\Support\AccessControl;

class RoleName
{
    public const SuperAdmin = 'Super Admin';

    public const Administrator = 'Administrator';

    public const Editor = 'Editor';

    public const Author = 'Author';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::SuperAdmin,
            self::Administrator,
            self::Editor,
            self::Author,
        ];
    }
}
