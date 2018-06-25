<?php

namespace Honeybadger;

use Throwable;
use GuzzleHttp\Client;
use Honeybadger\Concerns\Newable;
use Honeybadger\Support\Repository;
use Honeybadger\Handlers\ErrorHandler;
use Honeybadger\Handlers\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request as FoundationRequest;

class Honeybadger
{
    use Newable;

    /**
     * SDK Version.
     */
    const VERSION = '0.5.0';

    /**
     * Honeybadger API URL.
     */
    const API_URL = 'https://api.honeybadger.io/v1/';

    /**
     * @var \GuzzleHttp\Client;
     */
    protected $client;

    /**
     * @var \Honeybadger\Config
     */
    protected $config;

    /**
     * @var \Honeybadger\Support\Repository
     */
    protected $context;

    /**
     * @param  array  $config
     * @param  \GuzzleHttp\Client  $client
     */
    public function __construct(array $config = [], Client $client = null)
    {
        $this->config = new Config($config);

        $this->client = new HoneybadgerClient($this->config, $client);
        $this->context = new Repository;

        $this->setHandlers();
    }

    /**
     * @param  \Throwable  $throwable
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return void
     *
     * @throws \Honeybadger\Exceptions\ServiceException
     */
    public function notify(Throwable $throwable, FoundationRequest $request = null) : void
    {
        if ($this->excludedException($throwable)) {
            return;
        }

        $notification = (new ExceptionNotification($this->config, $this->context))
            ->make($throwable, $request);

        $this->client->notification($notification);
    }

    /**
     * @param  array  $payload
     * @return void
     *
     * @throws \Honeybadger\Exceptions\ServiceException
     */
    public function customNotification(array $payload) : void
    {
        $notification = (new CustomNotification($this->config, $this->context))
            ->make($payload);

        $this->client->notification($notification);
    }

    /**
     * @param  string  $key
     * @return void
     *
     * @throws \Honeybadger\Exceptions\ServiceException
     */
    public function checkin(string $key) : void
    {
        $this->client->checkin($key);
    }

    /**
     * @param  int|string  $key
     * @param  int|string  $value
     * @return void
     */
    public function context($key, $value) : void
    {
        $this->context->set($key, $value);
    }

    /**
     * @return void
     */
    private function setHandlers() : void
    {
        if ($this->config['handlers']['exception']) {
            (new ExceptionHandler($this))->register();
        }

        if ($this->config['handlers']['error']) {
            (new ErrorHandler($this))->register();
        }
    }

    /**
     * @param  \Throwable  $throwable
     * @return bool
     */
    private function excludedException(Throwable $throwable) : bool
    {
        return in_array(
            get_class($throwable),
            $this->config['excluded_exceptions']
        );
    }
}
