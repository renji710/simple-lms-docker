<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Storage;


class CourseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/data/course-data.csv');
        $data = array_map('str_getcsv', file($csvFile));
        $header = array_shift($data);

        foreach ($data as $row) {
            $courseDataFromCsv = array_combine($header, $row);

            $teacher = User::find($courseDataFromCsv['teacher']);
            if (!$teacher) {
                continue;
            }

            $fillableCourseData = [
                'name' => $courseDataFromCsv['name'],
                'url' => $courseDataFromCsv['url'],
                'site' => $courseDataFromCsv['site'],
                'description' => $courseDataFromCsv['description'],
                'price' => (int)$courseDataFromCsv['price'],
                'image' => null,
                'teacher_id' => $teacher->id,
            ];

            Course::firstOrCreate(
                [
                    'name' => $courseDataFromCsv['name'],
                ],
                $fillableCourseData
            );
        }
    }
}
