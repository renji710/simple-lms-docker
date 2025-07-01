<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseContent;
use App\Models\CourseMember;
use App\Models\Comment;
use App\Models\Course;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class CourseContentController extends Controller
{
    public function addComment(Request $request, CourseContent $content)
    {
        $user = Auth::user();

        $isMember = CourseMember::where('course_id', $content->course_id)
                                ->where('user_id', $user->id)
                                ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'Unauthorized: You must be a member of this course to comment.'], 403);
        }

        try {
            $request->validate([
                'comment' => 'required|string|max:1000',
            ]);

            $courseMember = CourseMember::where('course_id', $content->course_id)
                                        ->where('user_id', $user->id)
                                        ->first();

            $comment = Comment::create([
                'content_id' => $content->id,
                'member_id' => $courseMember->id,
                'comment' => $request->comment,
                'is_moderated' => false,
            ]);

            return response()->json([
                'message' => 'Comment added successfully! Waiting for moderation.',
                'comment' => $comment,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add comment', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(CourseContent $content)
    {
        $user = Auth::user();

        $isTeacher = ($content->course->teacher_id === $user->id);
        $isMember = $content->course->members()->where('user_id', $user->id)->exists();

        if (!$isTeacher && !$isMember) {
            return response()->json(['message' => 'Unauthorized: You are not a member or teacher of this course.'], 403);
        }

        $commentsQuery = $content->comments();

        if (!$isTeacher) {
            $commentsQuery->where('is_moderated', true);
        }

        $comments = $commentsQuery->with('member.user')->get();

        return response()->json([
            'id' => $content->id,
            'name' => $content->name,
            'description' => $content->description,
            'video_url' => $content->video_url,
            'file_attachment' => $content->file_attachment,
            'course' => [
                'id' => $content->course->id,
                'name' => $content->course->name,
            ],
            'comments' => $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'is_moderated' => $comment->is_moderated,
                    'commenter' => [
                        'id' => $comment->member->user->id,
                        'username' => $comment->member->user->username,
                        'fullname' => $comment->member->user->fullname,
                    ],
                    'created_at' => $comment->created_at,
                ];
            }),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($course->teacher_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: You are not the teacher of this course.'], 403);
        }

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:200', Rule::unique('course_contents')->where(function ($query) use ($course) {
                    return $query->where('course_id', $course->id);
                })],
                'description' => 'nullable|string',
                'video_url' => 'nullable|url|max:200',
                'file_attachment' => 'nullable|string',
                'parent_id' => 'nullable|exists:course_contents,id',
            ]);

            $content = $course->contents()->create([
                'name' => $request->name,
                'description' => $request->description,
                'video_url' => $request->video_url,
                'file_attachment' => $request->file_attachment,
                'parent_id' => $request->parent_id,
            ]);

            return response()->json([
                'message' => 'Course content created successfully!',
                'content' => $content,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create course content', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Course $course, CourseContent $content)
    {
        $user = Auth::user();

        if ($content->course_id !== $course->id || $course->teacher_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: You are not authorized to update this content.'], 403);
        }

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:200', Rule::unique('course_contents')->ignore($content->id)->where(function ($query) use ($course) {
                    return $query->where('course_id', $course->id);
                })],
                'description' => 'nullable|string',
                'video_url' => 'nullable|url|max:200',
                'file_attachment' => 'nullable|string',
                'parent_id' => 'nullable|exists:course_contents,id',
            ]);

            $content->name = $request->name;
            $content->description = $request->description;
            $content->video_url = $request->video_url;
            $content->file_attachment = $request->file_attachment;
            $content->parent_id = $request->parent_id;
            $content->save();

            return response()->json([
                'message' => 'Course content updated successfully!',
                'content' => $content,
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update course content', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Course $course, CourseContent $content)
    {
        $user = Auth::user();

        if ($content->course_id !== $course->id || $course->teacher_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: You are not authorized to delete this content.'], 403);
        }

        $content->delete();

        return response()->json(['message' => 'Course content deleted successfully!'], 200);
    }
}
