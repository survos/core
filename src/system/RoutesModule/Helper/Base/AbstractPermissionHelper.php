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

namespace Zikula\RoutesModule\Helper\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\GroupsModule\Entity\GroupEntity;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;
use Zikula\UsersModule\Entity\UserEntity;

/**
 * Permission helper base class.
 */
abstract class AbstractPermissionHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionApiInterface
     */
    protected $permissionApi;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;
    
    public function __construct(
        RequestStack $requestStack,
        PermissionApiInterface $permissionApi,
        CurrentUserApiInterface $currentUserApi,
        UserRepositoryInterface $userRepository
    ) {
        $this->requestStack = $requestStack;
        $this->permissionApi = $permissionApi;
        $this->currentUserApi = $currentUserApi;
        $this->userRepository = $userRepository;
    }
    
    /**
     * Checks if the given entity instance may be read.
     */
    public function mayRead(EntityAccess $entity, int $userId = null): bool
    {
        return $this->hasEntityPermission($entity, ACCESS_READ, $userId);
    }
    
    /**
     * Checks if the given entity instance may be edited.
     */
    public function mayEdit(EntityAccess $entity, int $userId = null): bool
    {
        return $this->hasEntityPermission($entity, ACCESS_EDIT, $userId);
    }
    
    /**
     * Checks if the given entity instance may be deleted.
     */
    public function mayDelete(EntityAccess $entity, int $userId = null): bool
    {
        return $this->hasEntityPermission($entity, ACCESS_DELETE, $userId);
    }
    
    /**
     * Checks if a certain permission level is granted for the given entity instance.
     */
    public function hasEntityPermission(EntityAccess $entity, int $permissionLevel, int $userId = null): bool
    {
        $objectType = $entity->get_objectType();
        $instance = $entity->getKey() . '::';
    
        return $this->permissionApi->hasPermission(
            'ZikulaRoutesModule:' . ucfirst($objectType) . ':',
            $instance,
            $permissionLevel,
            $userId
        );
    }
    
    /**
     * Filters a given collection of entities based on different permission checks.
     *
     */
    public function filterCollection($entities, int $permissionLevel, int $userId = null): array
    {
        $filteredEntities = [];
        foreach ($entities as $entity) {
            if (!$this->hasEntityPermission($entity, $permissionLevel, $userId)) {
                continue;
            }
            $filteredEntities[] = $entity;
        }
    
        return $filteredEntities;
    }
    
    /**
     * Checks if a certain permission level is granted for the given object type.
     */
    public function hasComponentPermission(string $objectType, int $permissionLevel, int $userId = null): bool
    {
        return $this->permissionApi->hasPermission(
            'ZikulaRoutesModule:' . ucfirst($objectType) . ':',
            '::',
            $permissionLevel,
            $userId
        );
    }
    
    /**
     * Checks if the quick navigation form for the given object type may be used or not.
     */
    public function mayUseQuickNav(string $objectType, int $userId = null): bool
    {
        return $this->hasComponentPermission($objectType, ACCESS_READ, $userId);
    }
    
    /**
     * Checks if a certain permission level is granted for the application in general.
     */
    public function hasPermission(int $permissionLevel, int $userId = null): bool
    {
        return $this->permissionApi->hasPermission(
            'ZikulaRoutesModule::',
            '::',
            $permissionLevel,
            $userId
        );
    }
    
    /**
     * Returns the list of user group ids of the current user.
     *
     * @return int[] List of group ids
     */
    public function getUserGroupIds(): array
    {
        $isLoggedIn = $this->currentUserApi->isLoggedIn();
        if (!$isLoggedIn) {
            return [];
        }
    
        $groupIds = [];
        $groups = $this->currentUserApi->get('groups');
        /** @var GroupEntity $group */
        foreach ($groups as $group) {
            $groupIds[] = $group->getGid();
        }
    
        return $groupIds;
    }
    
    /**
     * Returns the the current user's id.
     */
    public function getUserId(): int
    {
        return (int)$this->currentUserApi->get('uid');
    }
    
    /**
     * Returns the the current user's entity.
     */
    public function getUser(): UserEntity
    {
        return $this->userRepository->find($this->getUserId());
    }
}
