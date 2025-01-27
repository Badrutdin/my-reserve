<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Organization;
use App\Entity\Position;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadOrganizations($manager);
        $this->loadPositions($manager);
        $this->loadRoles($manager);
        $this->loadEmployees($manager);
        $this->loadUsers($manager);

    }

    private function loadOrganizations(ObjectManager $manager): void
    {
        for ($i = 0; $i < 100; $i++) {
            $organization = new Organization();
            $organization->setName($this->faker->company);
            $organization->setSubdomain($this->faker->word);
            $manager->persist($organization);
        }

        $manager->flush();
    }

    private function loadPositions(ObjectManager $manager)
    {
        $organizations = $manager->getRepository(Organization::class)->findAll();

        foreach ($organizations as $organization) {
            for ($i = 0; $i < 2; $i++) {
                $position = new Position();
                $position->setName($this->faker->word);
                $position->setOrganization($organization);
                $manager->persist($position);
            }
        }

        $manager->flush();
    }

    private function loadRoles(ObjectManager $manager)
    {
        $organizations = $manager->getRepository(Organization::class)->findAll();

        foreach ($organizations as $organization) {
            for ($i = 0; $i < 1; $i++) {
                $role = new Role();
                $role->setName($this->faker->word);
                $role->setType('admin');
                $role->setIsEditable(false);
                $role->setIsDeletable(false);
                $role->setPermissions(['wfefwef', 'fwefwef']);
                $role->setOrganization($organization);
                $manager->persist($role);
                $role = new Role();
                $role->setName($this->faker->word);
                $role->setType('default');
                $role->setIsEditable(true);
                $role->setIsDeletable(false);
                $role->setPermissions([]);
                $role->setOrganization($organization);
                $manager->persist($role);
            }
        }

        $manager->flush();
    }

    private function loadEmployees(ObjectManager $manager)
    {
        $organizations = $manager->getRepository(Organization::class)->findAll();
        $positions = $manager->getRepository(Position::class)->findAll();

        foreach ($organizations as $organization) {
            foreach ($positions as $position) {
                $employee = new Employee();
                $employee->setName($this->faker->name);
                $employee->setOrganization($organization);
//                $employee->setPosition($position);
                $manager->persist($employee);
            }
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        $organizations = $manager->getRepository(Organization::class)->findAll();

        foreach ($organizations as $organization) {
            $positions = $manager->getRepository(Position::class)->findByOrganization($organization);
            $roles = $manager->getRepository(Role::class)->findByOrganization($organization);

            foreach ($positions as $position) {
                foreach ($roles as $role) {
                    $user = new User();
                    $user->setName($this->faker->name);
                    $user->setEmail($this->faker->email);
                    $user->setPassword('1545');
                    $user->addOrganization($organization);
                    /*TODO
*/
//                    $user->setPosition($position);
//                    $user->setRole($role);
                    $manager->persist($user);
                }
            }
        }

        $manager->flush();
    }
}
