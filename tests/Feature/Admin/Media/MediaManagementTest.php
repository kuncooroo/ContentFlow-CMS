<?php

namespace Tests\Feature\Admin\Media;

use App\Livewire\Admin\Media\Edit;
use App\Livewire\Admin\Media\Index;
use App\Models\Media;
use App\Models\MediaReference;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        config(['media.disk' => 'public']);
    }

    public function test_author_can_upload_valid_image(): void
    {
        $author = User::factory()->author()->create();
        $file = UploadedFile::fake()->image('hero.jpg', 800, 600);

        Livewire::actingAs($author)
            ->test(Index::class)
            ->set('upload', $file)
            ->call('storeUpload')
            ->assertHasNoErrors();

        $media = Media::query()->first();

        $this->assertNotNull($media);
        $this->assertSame('hero.jpg', $media->original_name);
        $this->assertSame('image/jpeg', $media->mime_type);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_invalid_mime_upload_is_rejected(): void
    {
        $author = User::factory()->author()->create();
        $file = UploadedFile::fake()->create('script.php', 100, 'application/x-php');

        Livewire::actingAs($author)
            ->test(Index::class)
            ->set('upload', $file)
            ->call('storeUpload')
            ->assertHasErrors(['upload']);

        $this->assertDatabaseCount('media', 0);
    }

    public function test_oversized_upload_is_rejected(): void
    {
        $author = User::factory()->author()->create();
        config(['media.max_upload_kilobytes' => 1]);
        $file = UploadedFile::fake()->create('large.jpg', 2048, 'image/jpeg');

        Livewire::actingAs($author)
            ->test(Index::class)
            ->set('upload', $file)
            ->call('storeUpload')
            ->assertHasErrors(['upload']);
    }

    public function test_editor_can_update_alt_text(): void
    {
        $editor = User::factory()->editor()->create();
        $media = Media::factory()->withStoredFile()->create([
            'alt_text' => null,
        ]);

        Livewire::actingAs($editor)
            ->test(Edit::class, ['media' => $media])
            ->set('alt_text', 'Team photo at the office')
            ->call('save')
            ->assertRedirect(route('admin.media.index'));

        $this->assertSame('Team photo at the office', $media->fresh()->alt_text);
    }

    public function test_editor_can_delete_unreferenced_media(): void
    {
        $editor = User::factory()->editor()->create();
        $media = Media::factory()->withStoredFile()->create();

        Livewire::actingAs($editor)
            ->test(Index::class)
            ->call('delete', $media->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_referenced_media_cannot_be_deleted(): void
    {
        $editor = User::factory()->editor()->create();
        $media = Media::factory()->withStoredFile()->create();

        MediaReference::query()->create([
            'media_id' => $media->id,
            'owner_type' => 'post',
            'owner_id' => 1,
            'usage' => 'content_embed',
            'created_at' => now(),
        ]);

        Livewire::actingAs($editor)
            ->test(Index::class)
            ->call('delete', $media->id)
            ->assertHasErrors(['media']);

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_author_cannot_delete_media(): void
    {
        $author = User::factory()->author()->create();
        $media = Media::factory()->withStoredFile()->create();

        Livewire::actingAs($author)
            ->test(Index::class)
            ->call('delete', $media->id)
            ->assertForbidden();
    }
}
