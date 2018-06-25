<?php

namespace Honeybadger\Tests;

use Honeybadger\Config;
use Honeybadger\Honeybadger;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_requires_the_api_key()
    {
        $this->expectException(InvalidArgumentException::class);

        new Config;
    }

    /** @test */
    public function it_merges_configuration_values()
    {
        $config = (new Config(['api_key' => '1234']))->all();

        $this->assertArraySubset([
            'api_key' => '1234',
            'notifier' => [
                'name' => 'Honeybadger PHP',
                'url' => 'https://github.com/honeybadger-io/honeybadger-php',
                'version' => Honeybadger::VERSION,
            ],
            'environment' => [
                'filter' => [],
                'include' => [],
            ],
            'request' => [
                'filter' => [],
            ],
            'version' => '',
            'hostname' => gethostname(),
            'project_root' => '',
            'environment_name' => 'production',
            'handlers' => [
                'exception' => true,
                'error' => true,
            ],
            'client' => [
                'timeout' => 0,
                'proxy' => [],
            ],
        ], $config);
    }
}
