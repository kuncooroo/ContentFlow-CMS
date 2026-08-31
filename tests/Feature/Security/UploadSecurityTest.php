<?php

namespace Tests\Feature\Security;

use App\Livewire\Admin\Media\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UploadSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        config(['media.disk' => 'public']);
    }

    public function test_svg_upload_is_rejected(): void
    {
        $author = User::factory()->author()->create();
        $file = UploadedFile::fake()->create('icon.svg', 100, 'image/svg+xml');

        Livewire::actingAs($author)
            ->test(Index::class)
            ->set('upload', $file)
            ->call('storeUpload')
            ->assertHasErrors(['upload']);

        $this->assertDatabaseCount('media', 0);
    }

    public function test_double_extension_php_disguised_as_jpeg_is_rejected(): void
    {
        $author = User::factory()->author()->create();
        $file = UploadedFile::fake()->create('shell.php.jpg', 100, 'image/jpeg');

        Livewire::actingAs($author)
            ->test(Index::class)
            ->set('upload', $file)
            ->call('storeUpload')
            ->assertHasErrors(['upload']);

        $this->assertDatabaseCount('media', 0);
    }

    public function test_executable_extension_is_rejected(): void
    {
        $author = User::factory()->author()->create();
        $file = UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream');

        Livewire::actingAs($author)
            ->test(Index::class)
            ->set('upload', $file)
            ->call('storeUpload')
            ->assertHasErrors(['upload']);

        $this->assertDatabaseCount('media', 0);
    }
}
