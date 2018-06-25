<?php

namespace Honeybadger\Tests;

use Honeybadger\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request as FoundationRequest;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class RequestTest extends TestCase
{
    /** @test */
    public function it_correctly_builds_a_url()
    {
        $request = FoundationRequest::create(
            'http://honeybadger.dev/test',
            'GET'
        );

        $request->overrideGlobals();

        $this->assertEquals(
            'http://honeybadger.dev/test',
            (new Request($request))->url()
        );
    }

    /** @test */
    public function it_correctly_populates_method_and_query()
    {
        $request = FoundationRequest::create(
            'http://honeybadger.dev/test?query1=foo&query2=bar',
            'POST'
        );

        $request->overrideGlobals();

        $this->assertArraySubset([
            'method' => 'POST',
            'query' => [
                'query1' => 'foo',
                'query2' => 'bar',
            ],
        ], (new Request($request))->params());
    }

    /** @test */
    public function it_correctly_populates_data_from_form_request()
    {
        $request = FoundationRequest::create(
            '/test',
            'POST',
            [
                'foo' => 'bar',
                'baz' => 'bax',
            ]
        );

        $request->overrideGlobals();

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 'bax'],
            (new Request($request))->params()['data']
        );
    }

    /** @test */
    public function it_correctly_populates_data_from_json_request()
    {
        $request = FoundationRequest::create(
            'http://honeybadger.dev/test',
            'POST',
            [],
            [],
            [],
            [],
            json_encode(['foo' => 'bar', 'baz' => 'bax'])
        );

        $request->headers->set('Content-Type', 'application/json');

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 'bax'],
            (new Request($request))->params()['data']
        );
    }

    /** @test */
    public function it_can_filter_custom_keys()
    {
        $foundationRequest = FoundationRequest::create(
            '/test',
            'POST',
            [
                'foo' => 'bar',
                'baz' => 'bax',
            ]
        );

        $this->assertEquals(
            ['foo' => '[FILTERED]', 'baz' => 'bax'],
            (new Request($foundationRequest))->filterKeys(['foo'])->params()['data']
        );
    }

    /** @test */
    public function it_filters_default_values()
    {
        $foundationRequest = FoundationRequest::create(
            '/test',
            'POST',
            [
                'password' => 'foo',
                'password_confirmation' => 'foo',
            ]
        );

        $this->assertEquals(
            ['password' => '[FILTERED]', 'password_confirmation' => '[FILTERED]'],
            (new Request($foundationRequest))->params()['data']
        );
    }

    /** @test */
    public function it_filters_query_params()
    {
        $request = FoundationRequest::create(
            'http://honeybadger.dev/test?query1=foo&query2=bar',
            'GET'
        );

        $request->overrideGlobals();

        $this->assertEquals([
            'method' => 'GET',
            'query' => [
                'query1' => 'foo',
                'query2' => '[FILTERED]',
            ],
            'data' => [],
        ], (new Request($request))->filterKeys(['query2'])->params());
    }

    /** @test */
    public function it_filters_session_data()
    {
        $session = new Session(new MockArraySessionStorage);
        $session->start();
        $session->set('uid', 1234);

        $request = FoundationRequest::create(
            'http://honeybadger.dev/test',
            'GET'
        );

        $request->setSession($session);

        $sessionData = (new Request($request))
            ->filterKeys(['uid'])
            ->session();

        $this->assertEquals(['uid' => '[FILTERED]'], $sessionData);

        $session->invalidate();
    }
}
