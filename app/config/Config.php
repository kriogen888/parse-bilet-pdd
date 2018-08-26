<?php

namespace App\Config;


class Config
{
    /**
     * Version Application
     *
     * @var string
     */
    const VERSION = '0.2';
    /**
     * IMG Directory
     *
     * @var string
     */
    const IMG_DIR = __DIR__ . "/../../bilet/cd/img/";
    /**
     * TXT Directory
     *
     * @var string
     */
    const TEXT_DIR = __DIR__ . "/../../bilet/cd/txt/";
    /**
     * Charset txt file
     *
     * @var string
     */
    const IN_CHARSET = "windows-1251";
    /**
     * Charset DB
     *
     * @var string
     */
    const OUT_CHARSET = "utf-8";
}