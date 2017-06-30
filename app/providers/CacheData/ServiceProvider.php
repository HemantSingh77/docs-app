<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon                                                                |
  +------------------------------------------------------------------------+
  | Copyright (c) 20111-2017 Phalcon Team (https://phalconphp.com)         |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

namespace Docs\Providers\CacheData;

use Phalcon\Cache\Frontend\Data;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use function Docs\Functions\app_path;
use function Docs\Functions\container;

/**
 * Docs\Providers\CacheData\ServiceProvider
 *
 * @package Docs\Providers\CacheData
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di)
    {
        $di->setShared(
            'cacheData',
            function () {
                $lifetime = container('config')->get('cache')->get('lifetime', 3600);
                $driver   = container('config')->get('cache')->get('driver', 'file');

                $frontEnd = new Data(['lifetime' => $lifetime]);
                $backEnd  = ['cacheDir' => app_path('storage/cache/data/')];
                $adapter  = sprintf('\Phalcon\Cache\Backend\%s', ucfirst($driver));

                return new $adapter($frontEnd, $backEnd);
            }
        );
    }
}