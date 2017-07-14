<?php

namespace Tests\Unit;


use App\Manager\UserManager;
use App\Entity\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Mock\SaveUserRequest;
use Tests\TestCase;

class UserManagerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * @var \App\Manager\Contract\UserManager
     */
    private $manager;

    protected function setUp()
    {
        parent::setUp();
        $this->manager = $this->app->make(UserManager::class);
    }

    public function testCreate()
    {
        $data = [
            'first_name' => 'Firstname',
            'last_name' => 'Lastname'
        ];

        $request = new SaveUserRequest($data);
        $user  = $this->manager->saveUser($request);

        $this->assertInstanceOf(User::class, $user);
        $this->assertArraySubset($data, $user->toArray());

        $data = [
            'first_name' => 'Firstname',
            'last_name' => 'Lastname1'
        ];

        $userId = $user->id;

        $request = new SaveUserRequest($data, $user);
        $user  = $this->manager->saveUser($request);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->id);
        $this->assertArraySubset($data, $user->toArray());
    }

    public function testCollection()
    {
        $request = new SaveUserRequest([
            'first_name' => 'Firstname1',
            'last_name' => 'Lastname1'
        ]);
        $user1 = $this->manager->saveUser($request);

        $request = new SaveUserRequest([
            'first_name' => 'Firstname2',
            'last_name' => 'Lastname2',
            'is_active' => false
        ]);
        $user2 = $this->manager->saveUser($request);

        $userList = $this->manager->findAll()->toArray();

        $this->assertContains($user1->toArray(), $userList);
        $this->assertContains($user2->toArray(), $userList);
    }

    public function testFindById()
    {
        $request = new SaveUserRequest([
            'first_name' => 'Firstname1',
            'last_name' => 'Lastname1'
        ]);
        $user = $this->manager->saveUser($request);

        $userResult = $this->manager->findById($user->id);
        $this->assertNotNull($userResult);
        $this->assertEquals($user->toArray(), $userResult->toArray());

        $userId = $user->id;
        $this->manager->deleteUser($user->id);

        $userResult = $this->manager->findById($userId);
        $this->assertNull($userResult);
    }

    public function testActiveUsers()
    {
        $request = new SaveUserRequest([
            'first_name' => 'Firstname1',
            'last_name' => 'Lastname1'
        ]);
        $user1 = $this->manager->saveUser($request);

        $request = new SaveUserRequest([
            'first_name' => 'Firstname2',
            'last_name' => 'Lastname2',
            'is_active' => false
        ]);
        $user2 = $this->manager->saveUser($request);

        $userList = $this->manager->findActive()->toArray();

        $this->assertContains($user1->toArray(), $userList);
        $this->assertNotContains($user2->toArray(), $userList);
    }
}