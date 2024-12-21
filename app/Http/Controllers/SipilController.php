<?php

namespace App\Http\Controllers;

use App\Models\Sipil;
use Illuminate\Http\Request;

class SipilController extends Controller
{
    function index(){
        $sipil = Sipil::all();
        return view('sipil.index', compact('sipil'));
    }

    function create(){
        return view('sipil.create');
    }

    function store(Request $request){
        $request->validate([
            'sr_no' => 'required|string',
            'jo_no' => 'required|string',
            'project_name' => 'required|string',
            'pic' => 'required|string',
            'start_date' => 'required|date',
            'location' => 'required|string',
        ]);

        Sipil::create($request->all());
        return redirect()->with('success','Success');
    }

    function edit($id){
        $sipil = Sipil::findOrFail($id);
        return view('sipil.edit', compact('sipil'));
    }

    function update(Request $request, $id){
        $sipil = Sipil::findOrFail($id);
        $request->validate([
            'sr_no' => 'required|string',
            'jo_no' => 'required|string',
            'project_name' => 'required|string',
            'pic' => 'required|string',
            'start_date' => 'required|date',
            'location' => 'required|string',
        ]);

        $sipil->update($request->all());
        return redirect()->with('success','Success');
    }

    function destroy($id){
        $sipil = Sipil::findOrFail($id);
        $sipil->delete();
        return redirect()->with('success','Success');
    }
}
