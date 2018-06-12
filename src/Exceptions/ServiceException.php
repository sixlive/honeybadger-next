<?php

namespace Honeybadger\Exceptions;

use Exception;

class ServiceException extends Exception
{
    /**
     * @return \Honeybadger\Exceptions\ServiceException
     */
    public static function invalidApiKey() : ServiceException
    {
        return new static('The API key provided is invalid.');
    }

    /**
     * @return \Honeybadger\Exceptions\ServiceException
     */
    public static function invalidPayload() : ServiceException
    {
        return new static('The payload sent to Honeybadger was invalid.');
    }

    /**
     * @return \Honeybadger\Exceptions\ServiceException
     */
    public static function rateLimit() : ServiceException
    {
        return new static('You have hit your exception rate limit.');
    }

    /**
     * @return \Honeybadger\Exceptions\ServiceException
     */
    public static function serverError() : ServiceException
    {
        return new static('There was an error on our end.');
    }

    /**
     * @return \Honeybadger\Exceptions\ServiceException
     */
    public static function generic() : ServiceException
    {
        return new static('There was an error sending the payload to Honeybadger.');
    }
}
