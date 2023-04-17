<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessagesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('messages')->insert([
            'senderId' => 1,
            'recipientId' => 2,
            'content' => 'teste de envio de msg',
            'status' => true,
            'read'=> false
        ]);
        DB::table('messages')->insert([
            [
                'senderId' => 1,
                'recipientId' => 3,
                'content' => 'mais um envio de msg',
                'status' => true,
                'read'=> false
            ]
        ]);
        DB::table('messages')->insert([
            [
                'senderId' => 1,
                'recipientId' => 2,
                'content' => 'Leia a msg por favor',
                'status' => true,
                'read'=> false
            ]
        ]);
    }
}
