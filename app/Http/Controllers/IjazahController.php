<?php

namespace App\Http\Controllers;

use App\Models\Ijazah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IjazahController extends Controller
{
    /**
     * GET ALL DATA - optional filter siswa_id
     */
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Ijazah::with('siswa')
                ->when($request->siswa_id, fn($q) => $q->where('siswa_id', $request->siswa_id))
                ->get()
        ]);
    }

    /**
     * PAGINATION LIST + SEARCH
     */
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = Ijazah::with('siswa')
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'LIKE', "%$search%")
                    ->orWhere('nisn', 'LIKE', "%$search%")
                    ->orWhereHas('siswa', function($q) use ($search) {
                        $q->where('nama', 'LIKE', "%$search%")
                          ->orWhere('jurusan', 'LIKE', "%$search%");
                    });
            })
            ->latest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    /**
     * STORE DATA BARU (VALIDASI TANPA FORMREQUEST)
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'nama'     => 'required|string|max:255',
            'nisn'     => 'required|string|max:20',
            'tanggal'  => 'required|date',
            'kelas'    => 'required|string|max:10',
            'tahun'    => 'required|numeric|min:2000|max:' . (date('Y') + 1),
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validatedData['foto'] = $request->file('foto')->store('ijazah_foto', 'public');
        }

        $ijazah = Ijazah::create($validatedData)->load('siswa');

        return response()->json([
            'success' => true,
            'data' => $ijazah
        ]);
    }

    /**
     * DETAIL DATA
     */
    public function show(Ijazah $ijazah)
    {
        return response()->json([
            'data' => $ijazah->load('siswa')
        ]);
    }

    /**
     * UPDATE DATA (VALIDASI TANPA FORMREQUEST)
     */
    public function update(Request $request, Ijazah $ijazah)
    {
        $validatedData = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'nama'     => 'required|string|max:255',
            'nisn'     => 'required|string|max:20',
            'tanggal'  => 'required|date',
            'kelas'    => 'required|string|max:10',
            'tahun'    => 'required|numeric|min:2000|max:' . (date('Y') + 1),
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($ijazah->foto) Storage::disk('public')->delete($ijazah->foto);

            $validatedData['foto'] = $request->file('foto')->store('ijazah_foto', 'public');
        }

        $ijazah->update($validatedData);

        return response()->json([
            'success' => true,
            'data' => $ijazah->load('siswa')
        ]);
    }

    /**
     * DELETE DATA
     */
    public function destroy(Ijazah $ijazah)
    {
        if ($ijazah->foto) Storage::disk('public')->delete($ijazah->foto);

        $ijazah->delete();

        return response()->json(['success' => true]);
    }
}
