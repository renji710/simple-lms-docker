<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseMember;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class CourseMembersDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/data/member-data.csv');
        $data = array_map('str_getcsv', file($csvFile));
        $header = array_shift($data);

        foreach ($data as $row) {
            $memberData = array_combine($header, $row);

            $user = User::find($memberData['user_id']);
            $course = Course::find($memberData['course_id']);

            if (!$user || !$course) {
                continue; 
            }

            CourseMember::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'roles' => $memberData['roles'],
                ]
            );
        }
    }
}
