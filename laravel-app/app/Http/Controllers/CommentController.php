<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\CourseContent;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // public function store(Request $request, CourseContent $content)
    // {
    //     $user = Auth::user();

    //     $isMember = $content->course->members()->where('user_id', $user->id)->exists();

    //     if (!$isMember) {
    //         return response()->json(['message' => 'Unauthorized: You must be a member of this course to comment.'], 403);
    //     }

    //     try {
    //         $request->validate([
    //             'comment' => 'required|string|max:1000',
    //         ]);

    //         $courseMember = $content->course->members()->where('user_id', $user->id)->first();

    //         $comment = Comment::create([
    //             'content_id' => $content->id,
    //             'member_id' => $courseMember->id,
    //             'comment' => $request->comment,
    //             'is_moderated' => false,
    //         ]);

    //         return response()->json([
    //             'message' => 'Comment added successfully! Waiting for moderation.',
    //             'comment' => $comment,
    //         ], 201);

    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Failed to add comment', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function moderate(Request $request, CourseContent $content, Comment $comment)
    {
        $user = Auth::user();

        if ($comment->content_id !== $content->id) {
            return response()->json(['message' => 'Comment does not belong to this content.'], 404);
        }

        $course = $content->course;

        if (!$course || $course->teacher_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: You are not the teacher of this course.'], 403);
        }

        try {
            $request->validate([
                'action' => 'required|string|in:approve,reject',
            ]);

            if ($request->action === 'approve') {
                $comment->is_moderated = true;
            } else {
                $comment->is_moderated = false;
            }
            $comment->save();

            return response()->json([
                'message' => 'Comment moderated successfully!',
                'comment' => $comment,
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to moderate comment', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Comment $comment)
    {
        $user = Auth::user();

        $commentingMember = $comment->member;
        $course = $comment->content->course;

        $isCommentAuthor = ($commentingMember->user_id === $user->id);
        $isCourseTeacher = ($course->teacher_id === $user->id);
        $isAdmin = ($user->id === 1);

        if (!$isCommentAuthor && !$isCourseTeacher && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized: You are not authorized to edit this comment.'], 403);
        }

        try {
            $request->validate([
                'comment' => 'required|string|max:1000',
            ]);

            $comment->comment = $request->comment;
            $comment->save();

            return response()->json([
                'message' => 'Comment updated successfully!',
                'comment' => $comment,
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update comment', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Comment $comment)
    {
        $user = Auth::user();

        $commentingMember = $comment->member;
        $course = $comment->content->course;

        $isCommentAuthor = ($commentingMember->user_id === $user->id);
        $isCourseTeacher = ($course->teacher_id === $user->id);
        $isAdmin = ($user->id === 1);

        if (!$isCommentAuthor && !$isCourseTeacher && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized: You are not authorized to delete this comment.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully!'], 200);
    }
}
