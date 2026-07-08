<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnreportedClass;
use App\Models\DataMaster;

class HumasMonitoringController extends Controller
{
    /**
     * Show monitoring dashboard
     */
    public function index()
    {
        // Get unreported classes
        $unreportedClasses = UnreportedClass::where('date', today())
                                           ->where('reported', false)
                                           ->get();

        // Get data master
        $dataMaster = DataMaster::take(3)->get();
        $totalDataMaster = DataMaster::count();

        return view('humas.monitoring', compact('unreportedClasses', 'dataMaster', 'totalDataMaster'));
    }
}