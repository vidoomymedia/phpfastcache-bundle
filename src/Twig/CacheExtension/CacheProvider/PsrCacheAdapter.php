<?php

/**
 *
 * This file is part of phpFastCache.
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the docs/CREDITS.txt file.
 *
 * @author Georges.L (Geolim4)  <contact@geolim4.com>
 * @author PastisD https://github.com/PastisD
 * @author Alexander (asm89) <iam.asm89@gmail.com>
 * @author Khoa Bui (khoaofgod)  <khoaofgod@gmail.com> http://www.phpfastcache.com
 *
 */
declare(strict_types=1);

namespace Phpfastcache\Bundle\Twig\CacheExtension\CacheProvider;

use Phpfastcache\Bundle\Service\Phpfastcache;
use Phpfastcache\Bundle\Twig\CacheExtension\CacheProviderInterface;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Adapter class to make extension interoperable with every PSR-6 adapter.
 *
 * @see http://php-cache.readthedocs.io/
 *
 * @author Rvanlaak <rvanlaak@gmail.com>
 */
class PsrCacheAdapter implements CacheProviderInterface
{
    /**
     * @var Phpfastcache
     */
    private $cache;

    /**
     * @param Phpfastcache $cache
     */
    public function __construct(Phpfastcache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $key
     * @return mixed|false
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     */
    public function fetch($key)
    {
        $item = $this->cache->getTwigCacheInstance()->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }
        return false;
    }

    /**
     * @param string $key
     * @param string $value
     * @param int|\DateInterval $lifetime
     * @return bool
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     */
    public function save($key, $value, $lifetime = 0)
    {
        $item = $this->cache->getTwigCacheInstance()->getItem($key);
        $item->set($value);
        $item->expiresAfter($lifetime);

        return $this->cache->save($item);
    }
}
