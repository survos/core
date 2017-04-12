<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - http://zikula.org/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\Bundle\HookBundle\Listener;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\Event\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\CapabilityApiInterface;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;

/**
 * Class HooksListener
 */
class HooksListener implements EventSubscriberInterface
{
    /**
     * @var PermissionApiInterface
     */
    private $permissionsApi;

    /**
     * @var CapabilityApiInterface
     */
    private $capabilityApi;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ExtensionServicesListener constructor.
     * @param PermissionApiInterface $permissionApi
     * @param CapabilityApiInterface $capabilityApi
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        PermissionApiInterface $permissionApi,
        CapabilityApiInterface $capabilityApi,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->permissionsApi = $permissionApi;
        $this->capabilityApi = $capabilityApi;
        $this->router = $router;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            'zikula.link_collector' => 'linkCollectorResponder',
        ];
    }

    /**
     * Respond to zikula.link_collector events.
     *
     * Create a BC Layer for the zikula.link_collector event to gather Hook-related links.
     *
     * @param GenericEvent $event
     */
    public function linkCollectorResponder(GenericEvent $event)
    {
        $event->setArgument('modname', $event->getSubject());
        $event->setArgument('modfunc', [1 => 'getLinks']);
        $event->setArgument('api', true);
        if (!$this->permissionsApi->hasPermission($event['modname'] . '::Hooks', '::', ACCESS_ADMIN)) {
            return;
        }
        // return if module is not subscriber or provider capable
        if (!$this->capabilityApi->isCapable($event['modname'], CapabilityApiInterface::HOOK_SUBSCRIBER)
            && !$this->capabilityApi->isCapable($event['modname'], CapabilityApiInterface::HOOK_PROVIDER)) {
            return;
        }
        $event->data[] = [
            'url' => $this->router->generate('zikula_hook_hook_edit', ['moduleName' => $event['modname']]),
            'text' => $this->translator->__('Hooks'),
            'icon' => 'paperclip'
        ];
    }
}
