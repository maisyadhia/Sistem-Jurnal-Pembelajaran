<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataMaster;

class DataMasterController extends Controller
{
    public function index()
    {
        $dataMaster = DataMaster::paginate(10);
        $totalDataMaster = DataMaster::count();
        
        return view('data-master.index', compact('dataMaster', 'totalDataMaster'));
    }

    public function create()
    {
        return view('data-master.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:data_masters',
            'initials' => 'required|string|max:3',
            'category' => 'required|string',
            'status' => 'required|string',
            'color' => 'required|string',
            'statusColor' => 'required|string',
        ]);

        DataMaster::create($request->all());

        return redirect()->route('data-master')
                         ->with('success', 'Data master berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $dataMaster = DataMaster::findOrFail($id);
        return view('data-master.edit', compact('dataMaster'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:data_masters,identifier,' . $id,
            'initials' => 'required|string|max:3',
            'category' => 'required|string',
            'status' => 'required|string',
            'color' => 'required|string',
            'statusColor' => 'required|string',
        ]);

        $dataMaster = DataMaster::findOrFail($id);
        $dataMaster->update($request->all());

        return redirect()->route('data-master')
                         ->with('success', 'Data master berhasil diupdate!');
    }

    public function destroy($id)
    {
        $dataMaster = DataMaster::findOrFail($id);
        $dataMaster->delete();

        return redirect()->route('data-master')
                         ->with('success', 'Data master berhasil dihapus!');
    }
}