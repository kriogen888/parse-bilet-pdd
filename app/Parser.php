<?php

namespace App;


class Parser
{
    /**
     * Version Application
     *
     * @var string
     */
    const VERSION = '0.1';

    /**
     * Get the version number of the application.
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }
}