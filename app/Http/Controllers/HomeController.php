<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Car;
use App\Models\CarType;
use App\Models\CarFeatures;
use App\Models\CarImage;
use App\Models\Maker;
use App\Models\Model as CarModel;
use App\Models\FuelType;
use Illuminate\Support\Facades\View;
class HomeController extends Controller
{
    public function index()
    {
        // >> Select all Cars
        // $cars = Car::get();

        // >> Select published Cars
        //$cars = Car::where('published_at', '!=', null)->get();

        // >> Select first Car
        // $car = Car::first();

        // >> Select Car by ID
        // $car = Car::find(2);

        // >> Order Cars by created time - top 2
        // $cars = Car::orderBy('created_at', 'desc')->limit(2)->get();
        // dump($cars);

        // >> Create a new Car
        // $car = new Car();
        // $car->maker_id = 1;
        // $car->model_id = 1;
        // $car->year = 2019;
        // $car->price = 40000;
        // $car->vin = 'ABC123';
        // $car->mileage = 45000;
        // $car->car_type_id = 1;
        // $car->fuel_type_id = 1;
        // $car->user_id = 1;
        // $car->city_id = 1;
        // $car->address = '123 Main St';
        // $car->phone = '123-456-7890';
        // $car->description = 'This is a great car';
        // $car->published_at = now();
        // $car->save();

        // >> Create a new Car - Mass Assignment
        // $carData = [
        //     'maker_id' => 1,
        //     'model_id' => 1,
        //     'year' => 2019,
        //     'price' => 40000,
        //     'vin' => 'ABC123',
        //     'mileage' => 45000,
        //     'car_type_id' => 1,
        //     'fuel_type_id' => 1,
        //     'user_id' => 1,
        //     'city_id' => 1,
        //     'address' => '123 Main St',
        //     'phone' => '123-456-7890',
        //     'description' => 'This is a great car',
        //     'published_at' => now()
        // ];

        // >> Approach 1
        // Car::create($carData);

        // >> Approach 2
        // $car2 = new Car();
        // $car2->fill($carData)->save();

        // >> Approach 3
        // $car3 = new Car($carData)->save();

        // >> Update Car
        // $car = Car::find(1);
        // $car->price = 1500;
        // $car->save();

        // >> Update Or Create Car
        // $carData = [
        //     'maker_id' => 1,
        //     'model_id' => 1,
        //     'year' => 2019,
        //     'price' => 40000,
        //     'vin' => 'ABC1234',
        //     'mileage' => 45000,
        //     'car_type_id' => 1,
        //     'fuel_type_id' => 1,
        //     'user_id' => 1,
        //     'city_id' => 1,
        //     'address' => '123 Main St',
        //     'phone' => '123-456-7890',
        //     'description' => 'This is a great car',
        //     'published_at' => now()
        // ];

        // $car = Car::updateOrCreate(
        //     ['vin' => 'ABC1234', 'price' => 40000],
        //     $carData
        // );

        // >> Update Car - Mass Assignment
        // Car::where('published_at', null)
        //     ->where('user_id', 1)
        //     ->update(['published_at' => now()]);

        // >> Delete Car
        // $car = Car::find(2)->delete();

        // Car::destroy([4,3]);

        // Car::where('published_at', null)
        //     ->where('user_id', 1)
        //     ->delete();

        // >> Delete All Cars - NOT soft delete
        // Car::truncate();

        //$cars = Car::where('price', '>', 20000)->get();

        //$maker = Maker::where('name', 'Suzuki')->get();

        // FuelType::insert([
        //     ['name' => 'Hybrid'],
        // ]);

        // Car::where('id', 5)
        //     ->update(['price' => 15000]);

        // Car::where('year', '<', 2020)
        //     ->delete();

        // dump($maker);

        // $car = Car::find(1);

        // // $car->features->abs = 0;
        // // $car->features->save();

        // // $car->features->update(['heated_seats' => 1]);

        // $car->primaryImage->delete();

        // dd($car->features, $car->primaryImage);

        // $car = Car::find(2);
        // $carFeatures = new CarFeatures([
        //     'abs' => 1,
        //     'air_conditioning' => 1,
        //     'power_windows' => 1,
        //     'power_door_locks' => 1,
        //     'cruise_control' => 1,
        //     'bluetooth_connectivity' => 1,
        //     'remote_start' => 1,
        //     'gps_navigation' => 1,
        //     'heated_seats' => 1,
        //     'climate_control' => 1,
        //     'rear_parking_sensors' => 1,
        //     'leather_seats' => 1,
        // ]);
        // $car->features()->save($carFeatures);

        // $car = Car::find(1);

        // Create new image
        // $image = new CarImage(['image_path' => 'path3', 'position' => 3]);
        // $car->images()->save($image);

        // $car->images()->create(['image_path' => 'path4', 'position' => 4]);

        // $car->images()->saveMany([
        //     new CarImage(['image_path' => 'path5', 'position' => 5]),
        //     new CarImage(['image_path' => 'path6', 'position' => 6]),
        // ]);

        // $car->images()->createMany([
        //     ['image_path' => 'path7', 'position' => 7],
        //     ['image_path' => 'path8', 'position' => 8],
        // ]);

        // $carType = CarType::where("name", 'Hatchback')->first();
        // $cars = Car::whereBelongsTo($carType)->get();
        // dd($cars);

        // $carType = CarType::where("name", 'Hatchback')->first();
        // $cars = $carType->cars;

        // $car->car_type_id = $carType->id;
        // $car->save();

        // $car->carType()->associate($carType);
        // $car->save();

        // $user = User::find(1);

        // $user->favouriteCars()->attach([3, 4]);

        // $user->favouriteCars()->sync([3, 4]);

        // $user->favouriteCars()->detach([3]);

        // dd($user->favouriteCars);

        // >> Make 10 records but don't save them to database
        // $makers = Maker::factory()->count(10)->make();
        // >> Make 10 records and save them to database
        // $makers = Maker::factory()->count(10)->create();
        // dd($makers);

        // User::factory()->state([
        //     'name' => 'John Doe',
        // ])->create();

        // User::factory()->afterMaking(function (User $user) {
        //     dump($user);
        // })->create();

        // >> Make 5 records with the same name
        // Maker::factory()->count(5)->hasModels(5, ['name' => 'TestName'])->create();

        // Maker::factory()->count(1)->hasModels(1, function(array $attributes, Maker $maker) {
        //     return [];
        // })->create();

        // >> Make 1 Maker with 3 Models
        // Maker::factory()->count(1)->has(CarModel::factory()->count(3), 'models')->create();

        // CarModel::factory()->count(5)->forMaker(['name' => 'Lexus'])->create();
        // CarModel::factory()->count(5)->for(Maker::factory()->state(['name'=>'Mitsubishi']), 'maker')->create();

        // $maker = Maker::factory()->create();
        // CarModel::factory()->count(5)->for($maker)->create();

        // User::factory()->has(Car::factory()
        //     ->count(5), 'favouriteCars')
        //     // ->hasAttached(count(5), ['col1'=>'val1'], 'favouriteCars')
        //     ->create();

        $listings = Car::where('published_at', '<', now())
            ->orderBy('published_at', 'desc')
            ->limit(30)
            ->get();

        return View::make('home.index', ['listings' => $listings]);
    }
}
