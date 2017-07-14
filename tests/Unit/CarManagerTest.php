<?php

namespace Tests\Unit;


use App\Entity\Car;
use App\Manager\CarManager;
use App\Manager\UserManager;
use App\Entity\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Mock\SaveCarRequest;
use Tests\Mock\SaveUserRequest;
use Tests\TestCase;

class CarManagerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * @var \App\Manager\Contract\CarManager
     */
    private $manager;

    /**
     * @var \App\Manager\Contract\UserManager
     */
    private $userManager;

    protected function setUp()
    {
        parent::setUp();
        $this->manager = $this->app->make(CarManager::class);
        $this->userManager = $this->app->make(UserManager::class);
    }

    public function testCreate()
    {
        $user = $this->createUser();

        $data = [
            'registration_number' => 'MB123456',
            'model' => 'Toyota',
            'color' => 'FFF'
        ];

        $request = new SaveCarRequest($data, $user);
        $car = $this->manager->saveCar($request);

        $this->assertInstanceOf(Car::class, $car);
        $this->assertArraySubset($data, $car->toArray());
        $this->assertEquals($user->toArray(), $car->user->toArray());
        $this->assertNotNull($car->id);

        $carId = $car->id;

        $data = [
            'registration_number' => 'MB123456',
            'model' => 'Toyota',
            'color' => 'black'
        ];

        $request = new SaveCarRequest($data, $user, $car);
        $car = $this->manager->saveCar($request);

        $this->assertInstanceOf(Car::class, $car);
        $this->assertArraySubset($data, $car->toArray());
        $this->assertEquals($user->toArray(), $car->user->toArray());
        $this->assertEquals($carId, $car->id);
    }

    public function testCollection()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser(false);

        $request = new SaveCarRequest([
            'registration_number' => 'MB123456',
            'model' => 'Toyota',
            'color' => 'FFF'
        ], $user1);
        $car1 = $this->manager->saveCar($request);

        $request = new SaveCarRequest([
            'registration_number' => 'AB123456',
            'model' => 'Honda',
            'color' => '000'
        ], $user2);

        $car2 = $this->manager->saveCar($request);

        $carList = $this->manager->findAll()->toArray();

        $this->assertContains($car1->toArray(), $carList);
        $this->assertContains($car2->toArray(), $carList);
    }

    public function testFindById()
    {
        $user = $this->createUser();
        $request = new SaveCarRequest([
            'registration_number' => 'MB123456',
            'model' => 'Toyota',
            'color' => 'FFF'
        ], $user);
        $car = $this->manager->saveCar($request);

        $carResult = $this->manager->findById($car->id);
        $this->assertNotNull($carResult);
        $this->assertEquals($car->toArray(), $carResult->toArray());

        $carId = $carResult->id;

        $this->manager->deleteCar($car->id);
        $carResult = $this->manager->findById($carId);
        $this->assertNull($carResult);
    }

    public function testFindCarsByActiveUser()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser(false);

        $request = new SaveCarRequest([
            'registration_number' => 'MB123456',
            'model' => 'Toyota',
            'color' => 'FFF'
        ], $user1);
        $car1 = $this->manager->saveCar($request);

        $request = new SaveCarRequest([
            'registration_number' => 'AB123456',
            'model' => 'Honda',
            'color' => '000'
        ], $user2);

        $car2 = $this->manager->saveCar($request);

        $carList = $this->manager->findCarsFromActiveUsers()->toArray();

        $this->assertContains($car1->toArray(), $carList);
        $this->assertNotContains($car2->toArray(), $carList);
    }

    public function testDeleteCarsWithUser()
    {
        $user = $this->createUser();
        $carIds = [];

        $request = new SaveCarRequest([
            'registration_number' => 'MB123456',
            'model' => 'Toyota',
            'color' => 'FFF'
        ], $user);
        $car1 = $this->manager->saveCar($request);
        $cardIds[] = $car1->id;

        $request = new SaveCarRequest([
            'registration_number' => 'AB123456',
            'model' => 'Honda',
            'color' => '000'
        ], $user);

        $car2 = $this->manager->saveCar($request);
        $cardIds[] = $car2->id;

        $this->userManager->deleteUser($user->id);

        $carList = $this->manager->findAll();

        foreach ($carList as $carItem) {
            $this->assertNotContains($carItem->id, $cardIds);
        }
    }

    protected function createUser(bool $isActive = true): User
    {
        $request = new SaveUserRequest([
            'first_name' => 'Firstname',
            'last_name' => 'Lastname',
            'is_active' => $isActive
        ]);
        return $this->userManager->saveUser($request);
    }
}