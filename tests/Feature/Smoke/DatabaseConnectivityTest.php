<?php

namespace Tests\Feature\Smoke;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseConnectivityTest extends TestCase
{
    public function test_core_infrastructure_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertTrue(Schema::hasTable('jobs'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
    }
}
