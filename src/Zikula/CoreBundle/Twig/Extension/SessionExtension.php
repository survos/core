<?php

declare(strict_types=1);

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\Bundle\CoreBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Zikula\Bundle\CoreBundle\Twig\Runtime\SessionRuntime;

class SessionExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('showflashes', [SessionRuntime::class, 'showFlashes'], ['is_safe' => ['html']])
        ];
    }
}
