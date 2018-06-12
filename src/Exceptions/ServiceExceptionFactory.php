<?php

namespace Honeybadger\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class ServiceExceptionFactory
{

    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    protected $response;

    /**
     * @param  \GuzzleHttp\Psr7\Response  $response
     */
    public function __construct(GuzzleResponse $response)
    {
        $this->response = $response;
    }

    public function make() : Exception
    {
        return $this->exception();
    }

    /**
     * @return void
     *
     * @throws \Honeybadger\Exceptions\ServiceException
     */
    private function exception() : void
    {
        if ($this->response->getStatusCode() === Response::HTTP_FORBIDDEN) {
            throw ServiceException::invalidApiKey();
        }

        if ($this->response->getStatusCode() === Response::HTTP_UNPROCESSABLE_ENTITY) {
            throw ServiceException::invalidPayload();
        }

        if ($this->response->getStatusCode() === Response::HTTP_TOO_MANY_REQUESTS) {
            throw ServiceException::rateLimit();
        }

        if ($this->response->getStatusCode() === Response::HTTP_INTERNAL_SERVER_ERROR) {
            throw ServiceException::serverError();
        }

        throw ServiceException::generic();
    }
}
