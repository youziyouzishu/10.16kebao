<?php

namespace support;

use Monolog\Processor\ProcessorInterface;

class LogProcessor implements ProcessorInterface
{

    public function __invoke(array $record): array
    {
        $key = 'request_id';
        if (is_null(Context::get($key))) {
            Context::set($key, md5(uniqid() . mt_rand(0, 1000000)));
        }
        $record['extra'][$key] = Context::get($key);
        return $record;
    }
}