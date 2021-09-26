<?php

namespace LMS\Dashboard\Repositories;

use Illuminate\Support\Facades\Auth;
use LMS\Courses\Models\Course;

class DashboardRepository
{
    public function getHeroCardInformation(): array
    {
        $hasAnyCourse = Auth::user()->enrollments()->count();
        $hasAnyLessonWatched = Auth::user()->watched()->count();

        if ($hasAnyCourse && $hasAnyLessonWatched) {
            return [
                'type' => 'lastSeen',
                'course' => $this->getLastAccessedCourse()
            ];
        }

        return [
            'type' => 'mostRated',
            'course' => $this->getMostRatedCourse()
        ];
    }

    private function getLastAccessedCourse()
    {
        return Auth::user()
            ->watched()
            ->latest()
            ->first()
            ->module
            ->course;
    }

    private function getMostRatedCourse()
    {
        $count = 0;
        foreach (Course::where('paid', false)->get() as $course) {
            $studentsCount = $course->students()->count();
            if ($studentsCount > $count) {
                $result = $course;
                $count = $studentsCount;
            }
        }
        return $result ?? Course::first();
    }
}