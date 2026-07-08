<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurnal;

class GuruJurnalController extends Controller
{
    /**
     * Show jurnal form
     */
    public function index()
    {
        $jurnal = Jurnal::where('teacher_id', session('teacher_id', 1))
                        ->whereDate('created_at', today())
                        ->first();

        $lastUpdated = $jurnal ? $jurnal->updated_at->diffForHumans() : '2 menit yang lalu';

        return view('guru.jurnal', compact('jurnal', 'lastUpdated'));
    }

    /**
     * Store jurnal
     */
    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|min:10',
            'next_target' => 'required|string|min:10',
        ]);

        Jurnal::updateOrCreate(
            [
                'teacher_id' => session('teacher_id', 1),
                'date' => today(),
            ],
            [
                'class' => 'Kelas 10-A',
                'subject' => 'Matematika',
                'time' => '08:00 - 09:30',
                'topic' => $request->topic,
                'next_target' => $request->next_target,
                'rpp_completed' => $request->has('rpp_completed'),
                'absent_students' => $request->has('absent_students'),
            ]
        );

        return redirect()->route('jurnal')->with('success', 'Jurnal berhasil disimpan!');
    }
}