<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;
use App\Models\Place;

class PlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(["name" => "Bike Station", "slug" => "station"]);
        Category::create(["name" => "Bike Workshop", "slug" => "workshop"]);
        Category::create(["name" => "Bike Store", "slug" => "store"]);
        Category::create(["name" => "Restroom", "slug" => "restroom"]);

        $stationCategoryId = Category::where('slug', 'station')->value('id');
        $workshopCategoryId = Category::where('slug', 'workshop')->value('id');
        $storeCategoryId = Category::where('slug', 'store')->value('id');
        $restroomCategoryId = Category::where('slug', 'restroom')->value('id');

        Place::create([
            "name" => "Madero",
            "description" => "Frente Catedra, fuera de Dary Queen",
            "capacity" => 5,
            "latitude" => 21.881591781750803,
            "longitude" => -102.29581174258954,
            "category_id" => $stationCategoryId,
        ]);

        Place::create([
            "name" => "El Yersi",
            "description" => "Agencia de bicis a mitad de la calle Union",
            "schedule" => "10:00 - 21:00",
            "latitude" => 21.86811807210745,
            "longitude" => -102.24274646043305,
            "category_id" => $workshopCategoryId,
        ]);

        Place::create([
            "name" => "Abel Bicicletas y Partes",
            "description" => "Atras del mercado juarez (mercado de la birria)",
            "schedule" => "09:00 - 19:00",
            "latitude" => 21.883401224747434,
            "longitude" => -102.29917991907547,
            "category_id" => $storeCategoryId,
        ]);

        Place::create([
            "name" => "El Parian (planta alta)",
            "description" => "Baño Público en el tercer piso del centro comercial",
            "schedule" => "10:00 - 22:00",
            "cost" => 8.0,
            "latitude" => 21.882963481669155,
            "longitude" => -102.295815118261,
            "category_id" => $restroomCategoryId,
        ]);
    }
}