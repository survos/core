<?php

/**
 * Routes.
 *
 * @copyright Zikula contributors (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula contributors <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\RoutesModule\Base;

use Zikula\RoutesModule\Listener\EntityLifecycleListener;
use Zikula\RoutesModule\Menu\MenuBuilder;

/**
 * Events definition base class.
 */
abstract class AbstractRoutesEvents
{
    /**
     * The zikularoutesmodule.itemactionsmenu_pre_configure event is thrown before the item actions
     * menu is built in the menu builder.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\ConfigureItemActionsMenuEvent instance.
     *
     * @see MenuBuilder::createItemActionsMenu()
     * @var string
     */
    public const MENU_ITEMACTIONS_PRE_CONFIGURE = 'zikularoutesmodule.itemactionsmenu_pre_configure';
    
    /**
     * The zikularoutesmodule.itemactionsmenu_post_configure event is thrown after the item actions
     * menu has been built in the menu builder.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\ConfigureItemActionsMenuEvent instance.
     *
     * @see MenuBuilder::createItemActionsMenu()
     * @var string
     */
    public const MENU_ITEMACTIONS_POST_CONFIGURE = 'zikularoutesmodule.itemactionsmenu_post_configure';
    
    /**
     * The zikularoutesmodule.route_post_load event is thrown when routes
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\FilterRouteEvent instance.
     *
     * @see EntityLifecycleListener::postLoad()
     * @var string
     */
    public const ROUTE_POST_LOAD = 'zikularoutesmodule.route_post_load';
    
    /**
     * The zikularoutesmodule.route_pre_persist event is thrown before a new route
     * is created in the system.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\FilterRouteEvent instance.
     *
     * @see EntityLifecycleListener::prePersist()
     * @var string
     */
    public const ROUTE_PRE_PERSIST = 'zikularoutesmodule.route_pre_persist';
    
    /**
     * The zikularoutesmodule.route_post_persist event is thrown after a new route
     * has been created in the system.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\FilterRouteEvent instance.
     *
     * @see EntityLifecycleListener::postPersist()
     * @var string
     */
    public const ROUTE_POST_PERSIST = 'zikularoutesmodule.route_post_persist';
    
    /**
     * The zikularoutesmodule.route_pre_remove event is thrown before an existing route
     * is removed from the system.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\FilterRouteEvent instance.
     *
     * @see EntityLifecycleListener::preRemove()
     * @var string
     */
    public const ROUTE_PRE_REMOVE = 'zikularoutesmodule.route_pre_remove';
    
    /**
     * The zikularoutesmodule.route_post_remove event is thrown after an existing route
     * has been removed from the system.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\FilterRouteEvent instance.
     *
     * @see EntityLifecycleListener::postRemove()
     * @var string
     */
    public const ROUTE_POST_REMOVE = 'zikularoutesmodule.route_post_remove';
    
    /**
     * The zikularoutesmodule.route_pre_update event is thrown before an existing route
     * is updated in the system.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\FilterRouteEvent instance.
     *
     * @see EntityLifecycleListener::preUpdate()
     * @var string
     */
    public const ROUTE_PRE_UPDATE = 'zikularoutesmodule.route_pre_update';
    
    /**
     * The zikularoutesmodule.route_post_update event is thrown after an existing new route
     * has been updated in the system.
     *
     * The event listener receives an
     * Zikula\RoutesModule\Event\FilterRouteEvent instance.
     *
     * @see EntityLifecycleListener::postUpdate()
     * @var string
     */
    public const ROUTE_POST_UPDATE = 'zikularoutesmodule.route_post_update';
}
