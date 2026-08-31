<?php

namespace App\Support\Install;

use Illuminate\Support\Str;

class InstallEnvironmentWriter
{
    /**
     * @param  array<string, string|null>  $values
     */
    public function write(array $values): void
    {
        $path = $this->envPath();

        if (! file_exists($path)) {
            $example = base_path('.env.example');

            if (! file_exists($example)) {
                throw new \RuntimeException('Missing .env and .env.example files.');
            }

            copy($example, $path);
        }

        $content = (string) file_get_contents($path);

        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }

            $line = $key.'='.$this->escape((string) $value);

            if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $content) === 1) {
                $content = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $content) ?? $content;
            } else {
                $content = rtrim($content).PHP_EOL.$line.PHP_EOL;
            }
        }

        file_put_contents($path, $content);
    }

    public function ensureApplicationKey(): string
    {
        $key = (string) config('app.key', '');

        if ($key !== '' && $key !== 'base64:') {
            return $key;
        }

        $generated = 'base64:'.base64_encode(random_bytes(32));
        $this->write(['APP_KEY' => $generated]);

        return $generated;
    }

    private function envPath(): string
    {
        return base_path('.env');
    }

    private function escape(string $value): string
    {
        if (preg_match('/[\r\n]/', $value) === 1) {
            throw new \InvalidArgumentException('Configuration values cannot contain line breaks.');
        }

        if ($value === '') {
            return '""';
        }

        if (Str::contains($value, [' ', '#', '"', "'", '\\'])) {
            return '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }
}
