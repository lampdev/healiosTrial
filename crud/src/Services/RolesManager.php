<?php

namespace App\Services;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;

class RolesManager
{
    private const ADMIN_ROLE = 'admin';
    private const USER_ROLE = 'user';
    private const AVAILABLE_ROLES = [
        self::ADMIN_ROLE,
        self::USER_ROLE
    ];

    /** @var RoleRepository */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @return Role
     */
    public function getDefaultRole(): Role
    {
        return $this->getRoleByName(self::USER_ROLE);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isAdmin(User $user): bool
    {
        $adminRole = $this->getAdminRole();

        return $adminRole->getId() === $user->getRole()->getId();
    }

    /**
     * @return Role
     */
    private function getAdminRole(): Role
    {
        return $this->getRoleByName(self::ADMIN_ROLE);
    }

    /**
     * @param string $name
     * @return Role
     */
    private function getRoleByName(string $name): Role
    {
        if (!in_array($name, self::AVAILABLE_ROLES)) {
            $name = self::USER_ROLE;
        }

        $role = $this->roleRepository->findOneBy(['name' => $name]);

        if (!$role) {
            $role = $this->roleRepository->createWithName($name);
        }

        return $role;
    }
}
