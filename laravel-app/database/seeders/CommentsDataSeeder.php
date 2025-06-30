<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\CourseContent;
use App\Models\CourseMember;
use Illuminate\Support\Facades\Storage;

class CommentsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile = database_path('seeders/data/comments.json');
        $data = json_decode(file_get_contents($jsonFile), true);

        foreach ($data as $commentData) {

            $content = CourseContent::find($commentData['content_id']);
            if (!$content) {
                continue;
            }

            $courseMember = CourseMember::where('user_id', $commentData['user_id'])
                                        ->where('course_id', $content->course_id)
                                        ->first();

            if (!$courseMember) {
                continue;
            }

            Comment::firstOrCreate(
                [
                    'content_id' => $content->id,
                    'member_id' => $courseMember->id,
                    'comment' => $commentData['comment'],
                ],
                [
                    // 'is_moderated' => false,
                ]
            );
        }
    }
}
