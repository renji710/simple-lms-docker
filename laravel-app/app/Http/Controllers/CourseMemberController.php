<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CourseMember;
use Illuminate\Validation\ValidationException;

class CourseMemberController extends Controller
{
    public function index(Course $course)
    {
        $user = Auth::user();

        if ($course->teacher_id !== $user->id && $user->id !== 1) {
            return response()->json(['message' => 'Unauthorized: Only the teacher of this course or an administrator can view members.'], 403);
        }

        $members = $course->members()->with('user:id,username,fullname,email')->get();

        return response()->json([
            'message' => 'Course members retrieved successfully!',
            'course_id' => $course->id,
            'members' => $members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'user_id' => $member->user_id,
                    'username' => $member->user->username,
                    'fullname' => $member->user->fullname,
                    'email' => $member->user->email,
                    'roles' => $member->roles,
                    'enrolled_at' => $member->created_at,
                ];
            }),
        ]);
    }

    public function update(Request $request, CourseMember $member)
    {
        $user = Auth::user();

        $course = $member->course;
        if ($course->teacher_id !== $user->id && $user->id !== 1) {
            return response()->json(['message' => 'Unauthorized: Only the teacher of this course or an administrator can update member roles.'], 403);
        }

        try {
            $request->validate([
                'roles' => 'required|string|in:std,ast',
            ]);

            $member->roles = $request->roles;
            $member->save();

            return response()->json([
                'message' => 'Course member role updated successfully!',
                'member' => [
                    'id' => $member->id,
                    'user_id' => $member->user_id,
                    'username' => $member->user->username,
                    'roles' => $member->roles,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update member role', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(CourseMember $member)
    {
        $user = Auth::user();

        $course = $member->course;
        if ($course->teacher_id !== $user->id && $user->id !== 1) {
            return response()->json(['message' => 'Unauthorized: Only the teacher of this course or an administrator can remove members.'], 403);
        }

        $member->delete();

        return response()->json(['message' => 'Course member removed successfully!'], 200);
    }
}
