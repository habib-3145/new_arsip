<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KartuKeluargaController extends Controller
{
    /**
     * Display all (for dropdown etc)
     */
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => KartuKeluarga::when($request->search, function (Builder $query, string $search) {
                $query->where('no_kk', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('alamat', 'like', "%$search%");
            })->get()
        ]);
    }

    /**
     * Paginated list
     */
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = KartuKeluarga::when($request->search, function (Builder $query, string $search) {
            $query->where('no_kk', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('alamat', 'like', "%$search%")
                ->orWhere('desa_kelurahan', 'like', "%$search%")
                ->orWhere('kecamatan', 'like', "%$search%")
                ->orWhere('kabupaten_kota', 'like', "%$search%")
                ->orWhere('provinsi', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required',
            'no_kk'            => 'required|unique:kartu_keluargas,no_kk',
            'alamat'           => 'required',
            'rt'               => 'required',
            'rw'               => 'required',
            'kode_pos'         => 'nullable',
            'desa_kelurahan'   => 'required',
            'kecamatan'        => 'required',
            'kabupaten_kota'   => 'required',
            'provinsi'         => 'required',
        ]);

        $data = KartuKeluarga::create($validated);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Show detail
     */
    public function show(KartuKeluarga $kartuKeluarga)
    {
        return response()->json([
            'success' => true,
            'data' => $kartuKeluarga
        ]);
    }

    /**
     * Update
     */
    public function update(Request $request, KartuKeluarga $kartuKeluarga)
    {
        $validated = $request->validate([
            'name'             => 'required',
            'no_kk'            => 'required|unique:kartu_keluargas,no_kk,' . $kartuKeluarga->id,
            'alamat'           => 'required',
            'rt'               => 'required',
            'rw'               => 'required',
            'kode_pos'         => 'nullable',
            'desa_kelurahan'   => 'required',
            'kecamatan'        => 'required',
            'kabupaten_kota'   => 'required',
            'provinsi'         => 'required',
        ]);

        $kartuKeluarga->update($validated);

        return response()->json([
            'success' => true,
            'data' => $kartuKeluarga
        ]);
    }

    /**
     * Delete
     */
    public function destroy(KartuKeluarga $kartuKeluarga)
    {
        $kartuKeluarga->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
