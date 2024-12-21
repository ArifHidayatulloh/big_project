<?php

namespace App\Http\Controllers;

use App\Models\Sipil;
use App\Models\SipilProgress;
use Illuminate\Http\Request;

class SipilProgressController extends Controller
{
    function index(Sipil $sipil){
        $progress = $sipil->progress;
        return view('progress.index', compact('progress'));
    }

    function create(Sipil $sipil){
        return view('progress.create', compact('sipil'));
    }

    function store(Request $request, Sipil $sipil){
        $request->validate([
            'report_date' => 'required|date',
            'progress' => 'required|string',
            'obstacle' => 'nullable|string',
            'solution_date' => 'nullable|date',
            'solution' => 'nullable|string',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $sipil->progress()->create($request->all());
        return redirect()->route('sipil.progress.index', $sipil)->with('success', 'Progress has been added');
    }

    function edit(Sipil $sipil, SipilProgress $progress){
        return view('progress.edit', compact('progress','sipil'));
    }

    function update(Request $request, Sipil $sipil, SipilProgress $progress){
        $request->validate([
            'report_date' => 'required|date',
            'progress' => 'required|string',
            'obstacle' => 'nullable|string',
           'solution_date' => 'nullable|date',
           'solution' => 'nullable|string',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $progress->update($request->all());
        return redirect()->route('sipil.progress.index', $sipil)->with('success', 'Progress has been updated');
    }

    function destroy(Sipil $sipil, SipilProgress $progress){
        $progress->delete();
        return redirect()->route('sipil.progress.index', $sipil)->with('success', 'Progress has been deleted');
    }
}
