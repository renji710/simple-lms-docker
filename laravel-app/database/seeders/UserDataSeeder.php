<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/data/user-data.csv');
        $data = array_map('str_getcsv', file($csvFile));
        $header = array_shift($data);

        foreach ($data as $row) {
            $userData = array_combine($header, $row);

            User::firstOrCreate(
                [
                    'email' => $userData['email'],
                ],
                [
                    'username' => $userData['username'],
                    'fullname' => $userData['firstname'] . ' ' . $userData['lastname'],
                    'password' => Hash::make($userData['password']),
                    // 'email_verified_at' => now(),
                ]
            );
        }
    }
}
