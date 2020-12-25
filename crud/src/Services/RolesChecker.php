<?php

namespace App\Services;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;

class RolesChecker
{
    private const ADMIN_ROLE = 'admin';

    /** @var RoleRepository */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
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
        $adminRole = $this->roleRepository->getOneByName(self::ADMIN_ROLE);

        if (!$adminRole) {
            $adminRole = $this->roleRepository->createWithName(self::ADMIN_ROLE);
        }

        return $adminRole;
    }
}
