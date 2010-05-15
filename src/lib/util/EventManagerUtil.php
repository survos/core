<?php
/**
 * Copyright 2009 Zikula Foundation.
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv2 (or at your option, any later version).
 * @package EventManager
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * EventManagerUtil
 */
class EventManagerUtil
{
    /**
     * Singleton instance of EventManager.
     *
     * @var object
     */
    public static $eventManagerInstance;

    /**
     * Singleton constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get EventManager instance.
     *
     * @return object EventManager.
     */
    static public function getEventManager()
    {
        if (!self::$eventManagerInstance) {
            self::$eventManagerInstance = new EventManager();
        }

        return self::$eventManagerInstance;
    }

    /**
     * Notify event.
     *
     * @param Event $event Event.
     */
    static public function notify(Event $event)
    {
        self::getEventManager()->notify($event);
    }

    /**
     * NotifyUntil event.
     *
     * @param Event $event Event.
     */
    static public function notifyUntil(Event $event)
    {
        self::getEventManager()->notify($event);
    }

    /**
     * Attach listener.
     *
     * @param string $name Name of event.
     * @param array|string $handler PHP Callable.
     */
    static public function attach($name, $handler)
    {
        self::getEventManager()->attach($name, $handler);
    }

    /**
     * Detach listener.
     *
     * @param string       $name    Name of listener.
     * @param array|string $handler PHP callable.
     */
    static public function detach($name, $handler)
    {
        self::getEventManager()->detach($name);
    }
}