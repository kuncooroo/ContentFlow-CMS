<?php

namespace Tests\Feature\UiFeedback;

use App\Livewire\Admin\Settings\General;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Ui\Flash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class AdminFlashFeedbackTest extends TestCase
{
    use DatabaseTransactions;

    public function test_successful_save_shows_flash_message(): void
    {
        $admin = User::factory()->administrator()->create();
        SiteSetting::bootstrap();

        Livewire::actingAs($admin)
            ->test(General::class)
            ->set('site_name', 'Flash Test Site')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSessionHas(Flash::SUCCESS, 'Site settings saved.')
            ->assertSee('Site settings saved.');
    }

    public function test_validation_errors_are_distinct_from_success_flash(): void
    {
        $admin = User::factory()->administrator()->create();
        SiteSetting::bootstrap();

        Livewire::actingAs($admin)
            ->test(General::class)
            ->set('site_name', '')
            ->call('save')
            ->assertHasErrors(['site_name'])
            ->assertSessionMissing(Flash::SUCCESS);
    }
}
