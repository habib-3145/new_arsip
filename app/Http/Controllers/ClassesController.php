<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Classes::all()
        ]);
    }

    /**
     * Display a paginated list of the resource.
     */
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Classes::when($request->search, function (Builder $query, string $search) {
            $query->where('kelas', 'like', "%$search%")
                  ->orWhere('wali_kelas', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kelas' => 'required|string|max:100',
            'wali_kelas' => 'required|string|max:100',
        ]);

        $class = Classes::create($validatedData);

        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Classes $class)
    {
        return response()->json([
            'data' => $class
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classes $class)
    {
        $validatedData = $request->validate([
            'kelas' => 'sometimes|string|max:100',
            'wali_kelas' => 'sometimes|string|max:100',
        ]);

        $class->update($validatedData);

        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $class = Classes::findOrFail($id);
        $class->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil dihapus',
        ]);
    }

}
