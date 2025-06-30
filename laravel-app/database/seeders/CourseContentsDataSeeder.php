<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseContent;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;


class CourseContentsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile = database_path('seeders/data/contents.json');
        $data = json_decode(file_get_contents($jsonFile), true);

        foreach ($data as $contentData) {
            $course = Course::find($contentData['course_id']);
            if (!$course) {
                continue; 
            }

            CourseContent::firstOrCreate(
                [
                    'name' => $contentData['name'],
                    'course_id' => $course->id,
                ],
                [
                    'video_url' => $contentData['video_url'],
                    'description' => $contentData['description'],
                    // 'file_attachment' => null,
                    // 'parent_id' => null,
                ]
            );
        }
    }
}
