<?php

namespace Honeybadger\Tests;

use PHPUnit\Framework\TestCase;
use Honeybadger\Tests\Fixtures\FiltersDataFixture;

class FiltersDataTest extends TestCase
{
    /** @test */
    public function it_will_filter_data()
    {
        $this->assertEquals(
            '[FILTERED]',
            (new FiltersDataFixture(['foo' => 'bar']))
                ->filterKeys(['foo'])
                ->data()['foo']
        );
    }

    /** @test */
    public function it_will_filter_data_recursivly()
    {
        $filteredData = (new FiltersDataFixture(['foo' => ['bar' => 'baz']]))
            ->filterKeys(['bar'])
            ->data();

        $this->assertEquals(['foo' => ['bar' => '[FILTERED]']], $filteredData);
    }
}
