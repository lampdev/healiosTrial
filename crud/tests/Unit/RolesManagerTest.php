<?php

namespace App\Tests\Unit;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Services\RolesManager;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class RolesManagerTest extends TestCase
{
    /** @var Role|null */
    private $role;

    /** @var Role|null */
    private $foundRole;

    /** @var Role|null */
    private $createdRole;

    /** @var Role */
    private $adminRole;

    /** @var Role */
    private $userRole;

    /** @var RolesManager */
    private $rolesManager;

    /** @var User */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->rolesManager = new RolesManager($this->mockRoleRepository());
        $this->user = new User();
        $this->adminRole = new Role();
        $this->adminRole->setName('admin');
        $this->userRole = new Role();
        $this->userRole->setName('user');
        $this->role = $this->adminRole;
        $this->foundRole = $this->adminRole;
        $this->createdRole = $this->adminRole;
    }

    public function testIsAdmin(): void
    {
        $this->foundRole = $this->adminRole;
        $this->user->setRole($this->adminRole);
        $result = $this->rolesManager->isAdmin($this->user);
        $this->assertTrue($result);
    }

    public function testIsAdminIfUser(): void
    {
        $this->foundRole = $this->adminRole;
        $this->user->setRole($this->userRole);
        $result = $this->rolesManager->isAdmin($this->user);
        $this->assertFalse($result);
    }

    public function testFindOrDefault()
    {
        $this->role = $this->adminRole;
        $role = $this->rolesManager->findOrDefault(1);
        $this->assertEquals($this->adminRole->getName(), $role->getName());
    }

    public function testFindOrDefaultIfNotExists()
    {
        $this->role = null;
        $this->foundRole = $this->userRole;
        $role = $this->rolesManager->findOrDefault(1);
        $this->assertEquals($this->userRole->getName(), $role->getName());
    }

    public function testFindOrDefaultIfNoRoles()
    {
        $this->role = null;
        $this->foundRole = null;
        $this->createdRole = $this->userRole;
        $role = $this->rolesManager->findOrDefault(1);
        $this->assertEquals($this->userRole->getName(), $role->getName());
    }

    /**
     * @return RoleRepository|MockInterface
     */
    private function mockRoleRepository()
    {
        /** @var RoleRepository|MockInterface $roleRepository */
        $roleRepository = Mockery::mock(RoleRepository::class);

        $roleRepository->shouldReceive('find')->andReturnUsing(function () {
            return $this->role;
        });
        $roleRepository->shouldReceive('findOneBy')->andReturnUsing(function () {
            return $this->foundRole;
        });
        $roleRepository->shouldReceive('createWithName')->andReturnUsing(function () {
            return $this->createdRole;
        });

        return $roleRepository;
    }
}
