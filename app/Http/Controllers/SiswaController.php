<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class SiswaController extends Controller
{
    /**
     * Menampilkan semua data siswa.
     */
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Siswa::when($request->search, function (Builder $query, string $search) {
            $query->where('nama', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('kelas', 'like', "%$search%")
                ->orWhere('jurusan', 'like', "%$search%"); // <── search jurusan
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Siswa::all(),
        ]);
    }

    /**
     * Menampilkan data berdasarkan ID.
     */
    public function show(Siswa $siswa)
    {
        return response()->json([
            'siswa' => $siswa
        ]);
    }

    /**
     * Menyimpan data siswa baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'     => 'required|string|unique:siswas,nis',
            'nama'    => 'required|string|max:255',
            'kelas'   => 'required|string|max:100',
            'jurusan' => 'required|string|max:100',     // <── validasi jurusan
            'email'   => 'required|email|unique:siswas,email',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $siswa = Siswa::create($validated);

        return response()->json([
            'message' => 'Data siswa berhasil ditambahkan',
            'data'    => $siswa
        ], 201);
    }

    /**
     * Update data siswa.
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nis'     => 'required|string|unique:siswas,nis,' . $siswa->id,
            'nama'    => 'required|string|max:255',
            'kelas'   => 'required|string|max:100',
            'jurusan' => 'required|string|max:100',  // <── field baru
            'email'   => 'required|email|unique:siswas,email,' . $siswa->id,
            'user_id' => 'nullable|exists:users,id'
        ]);

        $siswa->update($validated);

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $siswa
        ]);
    }

    /**
     * Menghapus data siswa.
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json([
            'message' => 'Data siswa berhasil dihapus'
        ]);
    }
}
