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

namespace Phpfastcache\Bundle\Twig\CacheExtension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;

/**
 * Extension for caching template blocks with twig.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class Extension extends AbstractExtension
{
    private $cacheStrategy;

    /**
     * @param CacheStrategyInterface $cacheStrategy
     */
    public function __construct(CacheStrategyInterface $cacheStrategy)
    {
        $this->cacheStrategy = $cacheStrategy;
    }

    /**
     * @return CacheStrategyInterface
     */
    public function getCacheStrategy()
    {
        return $this->cacheStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        if (\version_compare(Environment::VERSION, '1.26.0', '>=')) {
            return \get_class($this);
        }
        return 'phpfastcache_cache';
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenParsers()
    {
        return [
          new TokenParser\Cache(),
        ];
    }
}
