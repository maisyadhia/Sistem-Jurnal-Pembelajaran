<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Student;
use App\Models\Note;

class DashboardController extends Controller
{
    /**
     * Show timeline dashboard
     */
    public function timeline()
    {
        // Get student data from session
        $studentId = session('student_id', 1);
        $student = Student::with('activities', 'notes')->find($studentId);
        
        if (!$student) {
            $student = (object) [
                'name' => 'Aditya Pratama',
                'class' => 'Kelas XI - IPA 2'
            ];
        }

        // Get activities
        $activities = Activity::where('student_id', $studentId)
                              ->orderBy('date_time', 'desc')
                              ->take(10)
                              ->get();

        // Get latest note
        $note = Note::where('student_id', $studentId)
                    ->latest()
                    ->first();

        return view('dashboard.timeline', compact('student', 'activities', 'note'));
    }
}