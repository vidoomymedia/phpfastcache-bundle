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

namespace Phpfastcache\Bundle\Twig\CacheExtension\Node;

use Twig\Compiler;
use Twig\Environment;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

/**
 * Cache twig node.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class CacheNode extends Node
{
    private static $cacheCount = 1;

    /**
     * @param AbstractExpression $annotation
     * @param AbstractExpression $keyInfo
     * @param Node $body
     * @param integer $lineno
     * @param string $tag
     */
    public function __construct(AbstractExpression $annotation, AbstractExpression $keyInfo, Node $body, $lineno, $tag = null)
    {
        parent::__construct([
          'key_info' => $keyInfo,
          'body' => $body,
          'annotation' => $annotation
        ], [], $lineno, $tag);
    }

    /**
     * {@inheritDoc}
     */
    public function compile(Compiler $compiler)
    {
        $i = self::$cacheCount++;

        if (\version_compare(Environment::VERSION, '1.26.0', '>=')) {
            $extension = \Phpfastcache\Bundle\Twig\CacheExtension\Extension::class;
        } else {
            $extension = 'phpfastcache_cache';
        }

        $compiler
            ->addDebugInfo($this)
            ->write("\$phpfastcacheCacheStrategy".$i." = \$this->env->getExtension('{$extension}')->getCacheStrategy();\n")
            ->write("\$phpfastcacheKey".$i." = \$phpfastcacheCacheStrategy".$i."->generateKey(")
                ->subcompile($this->getNode('annotation'))
                ->raw(", ")
                ->subcompile($this->getNode('key_info'))
            ->write(");\n")
            ->write("\$phpfastcacheCacheBody".$i." = \$phpfastcacheCacheStrategy".$i."->fetchBlock(\$phpfastcacheKey".$i.", \$this->getSourceContext());\n")
            ->write("if (\$phpfastcacheCacheBody".$i." === false) {\n")
            ->indent()
                ->write("\\ob_start();\n")
                ->write("\$compileMc = \\microtime(true);\n")
                    ->indent()
                        ->subcompile($this->getNode('body'))
                    ->outdent()
                ->write("\n")
                // ->write("sleep(2);\n") // For debug purpose
                ->write("\$phpfastcacheCacheBody".$i." = \\ob_get_clean();\n")
                ->write("\$phpfastcacheCacheStrategy".$i."->saveBlock(\$phpfastcacheKey".$i.", \$phpfastcacheCacheBody".$i.", \\microtime(true) - \$compileMc, \$this->getSourceContext());\n")
            ->outdent()
            ->write("}\n")
            ->write("echo \$phpfastcacheCacheBody".$i.";\n")
        ;
    }
}
