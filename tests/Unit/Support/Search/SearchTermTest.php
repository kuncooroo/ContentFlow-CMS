<?php

namespace Tests\Unit\Support\Search;

use App\Support\Search\SearchTerm;
use Tests\PureUnitTestCase;

class SearchTermTest extends PureUnitTestCase
{
    public function test_it_escapes_like_wildcards(): void
    {
        $this->assertSame('%100\\% complete%', SearchTerm::likePattern('100% complete'));
        $this->assertSame('%file\\_name%', SearchTerm::likePattern('file_name'));
    }

    public function test_it_limits_search_length(): void
    {
        $term = str_repeat('a', 120);

        $this->assertSame(100, strlen(SearchTerm::normalize($term)));
    }

    public function test_blank_search_returns_null_pattern(): void
    {
        $this->assertNull(SearchTerm::likePattern(''));
        $this->assertNull(SearchTerm::likePattern('   '));
    }
}
