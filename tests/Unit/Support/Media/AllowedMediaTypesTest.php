<?php

namespace Tests\Unit\Support\Media;

use App\Support\Media\AllowedMediaTypes;
use Tests\PureUnitTestCase;

class AllowedMediaTypesTest extends PureUnitTestCase
{
    public function test_blocked_extensions_include_executable_and_script_types(): void
    {
        $this->assertTrue(AllowedMediaTypes::isBlockedExtension('php'));
        $this->assertTrue(AllowedMediaTypes::isBlockedExtension('svg'));
        $this->assertTrue(AllowedMediaTypes::isBlockedExtension('exe'));
    }

    public function test_double_extension_filename_is_blocked(): void
    {
        $this->assertTrue(AllowedMediaTypes::isBlockedFilename('image.php.jpg'));
        $this->assertFalse(AllowedMediaTypes::isBlockedFilename('photo.jpg'));
    }

    public function test_allowed_image_extensions_remain_permitted(): void
    {
        $this->assertFalse(AllowedMediaTypes::isBlockedExtension('jpg'));
        $this->assertFalse(AllowedMediaTypes::isBlockedExtension('webp'));
        $this->assertTrue(AllowedMediaTypes::isBlockedExtension('svg'));
    }
}
