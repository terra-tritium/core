<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id'=>1,
            'name' => 'Admin',
            'email' => 'admin@com.br',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert(['id'=>2,'name' => 'Kleiton','email' => 'kleiton@com.br','password' => bcrypt('123456'),]);

        DB::table('users')->insert([
            'id'=>3,
            'name' => 'Andry',
            'email' => 'andry@com.br',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'id'=>4,
            'name' => 'Laris',
            'email' => 'laris@com.br',
            'password' => bcrypt('123456'),
        ]);
        DB::table('users')->insert([
            'id'=>5,
            'name' => 'Lais',
            'email' => 'lais@com.br',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'id' => 6,
            'name' => 'Lucas',
            'email' => 'lucas@com.br',
            'password' => bcrypt('123456'),
        ]);
        
        DB::table('users')->insert([
            'id' => 7,
            'name' => 'Maria',
            'email' => 'maria@com.br',
            'password' => bcrypt('123456'),
        ]);
        
        DB::table('users')->insert([
            'id' => 8,
            'name' => 'Carlos',
            'email' => 'carlos@com.br',
            'password' => bcrypt('123456'),
        ]);
        
        DB::table('users')->insert([
            'id' => 9,
            'name' => 'Ana',
            'email' => 'ana@com.br',
            'password' => bcrypt('123456'),
        ]);
        
        DB::table('users')->insert([
            'id' => 10,
            'name' => 'Pedro',
            'email' => 'pedro@com.br',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'id' => 11,
            'name' => 'Mariana',
            'email' => 'mariana@com.br',
            'password' => bcrypt('123456'),
        ]);
        
        // DB::table('users')->insert([
        //     'id' => 12,
        //     'name' => 'João2',
        //     'email' => 'joao2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        
        // DB::table('users')->insert([
        //     'id' => 13,
        //     'name' => 'Clara2',
        //     'email' => 'clara2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);

        // DB::table('users')->insert([
        //     'id' => 14,
        //     'name' => 'Virginia2',
        //     'email' => 'virginia2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 15,
        //     'name' => 'wagner2',
        //     'email' => 'wagner2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 16,
        //     'name' => 'Cristyna2',
        //     'email' => 'cristyna2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);

        // DB::table('users')->insert([
        //     'id' => 17,
        //     'name' => 'Thiago1',
        //     'email' => 'thiago@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 18,
        //     'name' => 'marcos2',
        //     'email' => 'marcos2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 19,
        //     'name' => 'lola2',
        //     'email' => 'lola2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);

        // DB::table('users')->insert([
        //     'id' => 20,
        //     'name' => 'diplomata2',
        //     'email' => 'diplomata2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);

        // DB::table('users')->insert([
        //     'id' => 21,
        //     'name' => 'diplomata3',
        //     'email' => 'diplomata3@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 22,
        //     'name' => 'diplomata4',
        //     'email' => 'diplomata4@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 23,
        //     'name' => 'diplomata5',
        //     'email' => 'diplomata5@com.br',
        //     'password' => bcrypt('123456'),
        // ]);

        // DB::table('users')->insert([
        //     'id' => 24,
        //     'name' => 'general2',
        //     'email' => 'general2@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 25,
        //     'name' => 'general3',
        //     'email' => 'general3@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 26,
        //     'name' => 'general4',
        //     'email' => 'general4@com.br',
        //     'password' => bcrypt('123456'),
        // ]);
        // DB::table('users')->insert([
        //     'id' => 27,
        //     'name' => 'general5',
        //     'email' => 'general5@com.br',
        //     'password' => bcrypt('123456'),
        // ]);

        $faker = Faker::create();
        for ($i = 0; $i < 150; $i++) {
            $name = $faker->name;
            $email = $faker->unique()->safeEmail;
        
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('123456'),
            ]);
        }

    }
}
