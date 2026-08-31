<?php

namespace App\Support\Install;

class InstallRequirements
{
    /**
     * @return list<array{name: string, passed: bool, message: string}>
     */
    public function checks(): array
    {
        return [
            $this->phpVersion(),
            $this->extension('pdo'),
            $this->extension('pdo_mysql'),
            $this->extension('mbstring'),
            $this->extension('openssl'),
            $this->extension('tokenizer'),
            $this->extension('xml'),
            $this->extension('ctype'),
            $this->extension('json'),
            $this->extension('fileinfo'),
            $this->writablePath('storage'),
            $this->writablePath('bootstrap/cache'),
            $this->envWritable(),
        ];
    }

    public function passes(): bool
    {
        foreach ($this->checks() as $check) {
            if (! $check['passed']) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{name: string, passed: bool, message: string}
     */
    private function phpVersion(): array
    {
        $required = '8.3.0';
        $current = PHP_VERSION;
        $passed = version_compare($current, $required, '>=');

        return [
            'name' => 'PHP version',
            'passed' => $passed,
            'message' => $passed
                ? "PHP {$current}"
                : "PHP {$required} or higher required (found {$current}).",
        ];
    }

    /**
     * @return array{name: string, passed: bool, message: string}
     */
    private function extension(string $extension): array
    {
        $passed = extension_loaded($extension);

        return [
            'name' => "PHP extension: {$extension}",
            'passed' => $passed,
            'message' => $passed
                ? 'Available'
                : "Required PHP extension [{$extension}] is missing.",
        ];
    }

    /**
     * @return array{name: string, passed: bool, message: string}
     */
    private function writablePath(string $relativePath): array
    {
        $path = base_path($relativePath);
        $passed = is_dir($path) && is_writable($path);

        return [
            'name' => "Writable: {$relativePath}",
            'passed' => $passed,
            'message' => $passed
                ? 'Writable'
                : "Directory [{$relativePath}] must exist and be writable.",
        ];
    }

    /**
     * @return array{name: string, passed: bool, message: string}
     */
    private function envWritable(): array
    {
        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');

        if (file_exists($envPath)) {
            $passed = is_writable($envPath);
            $message = $passed ? '.env is writable' : '.env exists but is not writable.';
        } elseif (file_exists($examplePath) && is_writable(dirname($examplePath))) {
            $passed = true;
            $message = '.env can be created from .env.example';
        } else {
            $passed = false;
            $message = 'Cannot create or write .env file.';
        }

        return [
            'name' => 'Environment file',
            'passed' => $passed,
            'message' => $message,
        ];
    }
}
