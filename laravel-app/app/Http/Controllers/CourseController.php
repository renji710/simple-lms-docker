<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CourseMember;
use App\Models\Course;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{

    public function batchEnrollStudents(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($course->teacher_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: You are not the teacher of this course.'], 403);
        }

        try {
            $request->validate([
                'student_identifiers' => 'required|array',
                'student_identifiers.*' => 'required|string',
                'roles' => 'nullable|string|in:std,ast',
            ]);

            $identifiers = $request->input('student_identifiers');
            $roleToAssign = $request->input('roles', 'std');

            $enrolledCount = 0;
            $failedToEnroll = [];

            DB::beginTransaction();

            foreach ($identifiers as $identifier) {
                $studentUser = User::where('email', $identifier)
                                   ->orWhere('username', $identifier)
                                   ->first();

                if (!$studentUser) {
                    $failedToEnroll[] = ['identifier' => $identifier, 'reason' => 'User not found.'];
                    continue; 
                }

                $isAlreadyMember = CourseMember::where('course_id', $course->id)
                                                ->where('user_id', $studentUser->id)
                                                ->exists();

                if ($isAlreadyMember) {
                    $failedToEnroll[] = ['identifier' => $identifier, 'reason' => 'Already enrolled.'];
                    continue;
                }

                try {
                    CourseMember::create([
                        'course_id' => $course->id,
                        'user_id' => $studentUser->id,
                        'roles' => $roleToAssign,
                    ]);
                    $enrolledCount++;
                } catch (\Exception $e) {
                    $failedToEnroll[] = ['identifier' => $identifier, 'reason' => 'Database error: ' . $e->getMessage()];
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Batch enrollment completed.',
                'enrolled_count' => $enrolledCount,
                'failed_to_enroll' => $failedToEnroll,
            ]);

        } catch (ValidationException $e) {
            DB::rollBack(); 
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Batch enrollment failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $courses = Course::with('teacher')->get();

        return response()->json([
            'message' => 'Courses retrieved successfully!',
            'courses' => $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'description' => $course->description,
                    'price' => $course->price,
                    'image' => $course->image,
                    'teacher' => [
                        'id' => $course->teacher->id,
                        'username' => $course->teacher->username,
                        'fullname' => $course->teacher->fullname,
                    ],
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();


        try {
            $request->validate([
                'name' => ['required', 'string', 'max:100', Rule::unique('courses')->where(function ($query) use ($user) {
                    return $query->where('teacher_id', $user->id);
                })],
                'description' => 'nullable|string',
                'price' => 'required|integer|min:0',
                'image' => 'nullable|string|max:200',
            ]);

            $course = Course::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $request->image,
                'teacher_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Course created successfully!',
                'course' => $course,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create course', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Course $course)
    {
        $course->load('teacher');

        return response()->json([
            'message' => 'Course retrieved successfully!',
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'price' => $course->price,
                'image' => $course->image,
                'teacher' => [
                    'id' => $course->teacher->id,
                    'username' => $course->teacher->username,
                    'fullname' => $course->teacher->fullname,
                ],
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at,
            ],
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($course->teacher_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: You are not the teacher of this course.'], 403);
        }

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:100', Rule::unique('courses')->ignore($course->id)->where(function ($query) use ($user) {
                    return $query->where('teacher_id', $user->id);
                })],
                'description' => 'nullable|string',
                'price' => 'required|integer|min:0',
                'image' => 'nullable|string|max:200',
            ]);

            $course->name = $request->name;
            $course->description = $request->description;
            $course->price = $request->price;
            $course->image = $request->image;
            $course->save();

            return response()->json([
                'message' => 'Course updated successfully!',
                'course' => $course,
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update course', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Course $course)
    {
        $user = Auth::user();

        if ($course->teacher_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: You are not the teacher of this course.'], 403);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully!'], 200);
    }
}
