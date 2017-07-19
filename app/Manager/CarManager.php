<?php

namespace App\Manager;


use App\Entity\Car;
use App\Manager\Contract\CarManager as CarManagerContract;
use App\Request\Contract\SaveCarRequest;
use Illuminate\Support\Collection;

class CarManager implements CarManagerContract
{

    /**
     * Find all Cars
     *
     * @return Collection
     */
    public function findAll(): Collection
    {
        return Car::all();
    }

    /**
     * Find Car by ID
     *
     * @param int $id
     * @return Car|null
     */
    public function findById(int $id)
    {
        return Car::find($id) ?? null;
    }

    /**
     * Find Cars that belongs only to active users
     *
     * @return Collection
     */
    public function findCarsFromActiveUsers(): Collection
    {
        return Car::whereHas('user', function ($query) {
            return $query->where('is_active', 1);
        })->get();
    }

    /**
     * Create or Update Car
     *
     * @param SaveCarRequest $request
     * @return Car
     */
    public function saveCar(SaveCarRequest $request): Car
    {
        return Car::updateOrCreate(
            ['id' => $request->getCar()->id],
            [
                'model' => $request->getModel(),
                'color' => $request->getColor(),
                'registration_number' => $request->getRegistrationNumber(),
                'year' => $request->getYear(),
                'price' => $request->getPrice(),
                'user_id' => $request->getUser()->id,
            ]
        );
    }

    /**
     * Delete Car by ID
     *
     * @param int $carId
     * @return void
     */
    public function deleteCar(int $carId)
    {
        Car::destroy($carId);
    }
}