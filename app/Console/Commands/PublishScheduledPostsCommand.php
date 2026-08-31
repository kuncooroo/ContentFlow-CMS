<?php

namespace App\Console\Commands;

use App\Actions\Posts\PublishScheduledPosts;
use Illuminate\Console\Command;

class PublishScheduledPostsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'content:publish-scheduled-posts';

    /**
     * @var string
     */
    protected $description = 'Publish scheduled posts whose publish time has been reached';

    public function handle(PublishScheduledPosts $publishScheduledPosts): int
    {
        $result = $publishScheduledPosts->handle();

        if ($result['published'] > 0) {
            $this->info("Published {$result['published']} scheduled post(s).");
        }

        if ($result['failed'] > 0) {
            foreach ($result['failures'] as $failure) {
                $this->warn("Post #{$failure['post_id']}: {$failure['message']}");
            }
        }

        if ($result['published'] === 0 && $result['failed'] === 0) {
            $this->line('No scheduled posts were due for publishing.');
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
