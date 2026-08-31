<?php

namespace App\Support\Ui;

class Flash
{
    public const SUCCESS = 'success';

    public const ERROR = 'error';

    public static function success(string $message): void
    {
        session()->flash(self::SUCCESS, $message);
    }

    public static function error(string $message): void
    {
        session()->flash(self::ERROR, $message);
    }
}
