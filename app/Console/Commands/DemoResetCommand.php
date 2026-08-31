<?php

namespace App\Console\Commands;

use App\Support\Demo\DemoReset;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class DemoResetCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'demo:reset {--force : Run without confirmation}';

    /**
     * @var string
     */
    protected $description = 'Reset demo content and users to the seeded baseline (requires DEMO_MODE=true)';

    public function handle(DemoReset $demoReset): int
    {
        if (! $this->option('force') && ! $this->confirm('This will delete all users and content, then re-seed demo data. Continue?')) {
            $this->line('Demo reset cancelled.');

            return self::SUCCESS;
        }

        try {
            $demoReset->reset();
        } catch (ValidationException $exception) {
            $this->error($exception->validator->errors()->first());

            return self::FAILURE;
        }

        $this->info('Demo environment reset successfully.');

        return self::SUCCESS;
    }
}
