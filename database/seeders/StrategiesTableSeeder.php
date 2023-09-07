<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrategiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $strategies = [
            [
                'code' => 'bounce',
                'title' => 'Отбой от уровня',
                'description' => 'Стратегия основывается на том что ценовой уровень не будет пробит и цена развернется в обратную сторону.'
            ],
            [
                'code' => 'breakout',
                'title' => 'Пробой уровня',
                'description' => 'Стратегия основывается на том что цена пробет уровень и пойдет дальше в ту же сторону.'
            ],
            [
                'code' => 'fake_breakout',
                'title' => 'Ложный пробой',
                'description' => 'Если после пробоя не возникает импульс.'
            ]
        ];

        DB::table('strategies')->insert($strategies);
    }
}
