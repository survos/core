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

namespace Zikula\ExtensionsModule\Twig\Runtime;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\CapabilityApiInterface;

class DefaultPathRuntime implements RuntimeExtensionInterface
{
    /**
     * @var CapabilityApiInterface
     */
    private $capabilityApi;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        CapabilityApiInterface $capabilityApi,
        RouterInterface $router
    ) {
        $this->capabilityApi = $capabilityApi;
        $this->router = $router;
    }

    public function getDefaultPath(string $extensionName, $type = CapabilityApiInterface::USER): string
    {
        $capability = $this->capabilityApi->isCapable($extensionName, $type);
        if (!$capability) {
            return '';
        }
        if (isset($capability['route'])) {
            return $this->router->generate($capability['route']);
        }

        return '';
    }
}
