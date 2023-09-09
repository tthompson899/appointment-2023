<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Appointment;
use App\Models\Type;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use RefreshDatabase;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(50)->create();

        $types = [
            'annual cleaning',
            'bonding',
            'bridges',
            'crowns',
            'dentures',
            'fillings',
            'hygiene',
            'implants',
            'inlays',
            'invisalign_vid',
            'orthodontics',
            'partial dentures',
            'perodontal',
            'TMJ',
            'root canal',
            'veneers',
            'whitening'
        ];

        foreach ($types as $type) {
            DB::table('types')->insert([
                'name' => $type,
                'created_at' => Carbon::now('America/Chicago'),
                'updated_at' => Carbon::now('America/Chicago'),
            ]);
        }

        for ($count=0; $count < 50; $count++) { 
            Appointment::factory()->state([
                'user_id' => User::all()->random()->id,
                'type_id' => Type::all()->random()->id,
            ])->create();
        }
    }
}
