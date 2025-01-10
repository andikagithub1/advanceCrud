<?php

namespace App\Http\Controllers;


use App\Models\Karyawan;
use App\Models\Departemen;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $karyawan = Karyawan::all();
        return view ('karyawan.index', ['karyawan'=>$karyawan]);
    }

    /**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\View\View
 */
public function create(): \Illuminate\View\View
{
    // Mendapatkan data departemen
    $departemen = Departemen::all();

    // Mengembalikan view dengan data departemen
    return view('karyawan.create', ['departemen' => $departemen]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'nip'=> 'required',
            'nama_karyawan'=> 'required',
            'gaji_karyawan'=> 'required',
            'alamat'=> 'required',
            'jenis_kelamin'=> 'required',
            'foto'=> 'required|mimes:jpeg,jpg,png,gif',

        ],[
            'nip.required' => 'NIP Wajib Diisi!',
            'nama_karyawan.required' => 'Nama Karyawan Wajib Diisi!',
            'gaji_karyawan.required' => 'Gaji Karyawan Wajib Diisi!',
            'alamat.required' => 'Alamat Karyawan Wajib Diisi!',
            'jenis_kelamin.required' => 'Data Jenis Kelamin Wajib Diisi!',
            'foto.required' => 'Foto Wajib Diisi!',
            'foto.mimes' => 'Foto Boleh Berekstensi jpeg, jpg, png, dan gif',

        ]);
        $foto_file = $request->file('foto');
        $foto_ekstensi = $foto_file->extension();
        $foto_nama = date('ymdhis'). "." .$foto_ekstensi;
        $foto_file->move(public_path('foto'), $foto_nama);

        $data = [
            'nip'=> $request->input('nip'),
            'nama_karyawan'=> $request->input('nama_karyawan'),
            'gaji_karyawan'=> $request->input('gaji_karyawan'),
            'alamat'=> $request->input('alamat'),
            'jenis_kelamin'=> $request->input('jenis_kelamin'),
            'foto'=> $foto_nama,
            'departemen_id' => $request->input('departemen_id'),
        ];

        Karyawan::create($data);
        return redirect('karyawan')->with('success', 'Data Karyawan Berhasil Ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Karyawan $karyawan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Karyawan::where('nip', $id)->first();
        $departemen = Departemen::all();

        return view('karyawan.edit',
        [
        'departemen' => $departemen,
        'data'=> $data]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nip'=> 'required',
            'nama_karyawan'=> 'required',
            'departemen_id' => 'required',
            'gaji_karyawan'=> 'required',
            'alamat'=> 'required',
            'jenis_kelamin'=> 'required',// pastikan departemen_id valid
        ],[
            'nip.required' => 'NIP Wajib Diisi!',
            'nama_karyawan.required' => 'Nama Karyawan Wajib Diisi!',
            'departemen_id' => 'Departemen Wajib Diisi',
            'gaji_karyawan.required' => 'Gaji Karyawan Wajib Diisi!',
            'alamat.required' => 'Alamat Karyawan Wajib Diisi!',
            'jenis_kelamin.required' => 'Data Jenis Kelamin Wajib Diisi!',
        ]);

        // Ambil data karyawan berdasarkan nip
        $data = Karyawan::where('nip', $id)->first();

        // Perbarui data karyawan dengan data baru
        $data->update([
            'nip' => $request->nip,
            'nama_karyawan' => $request->nama_karyawan,
            'departemen_id' => $request->departemen_id,
            'gaji_karyawan' => $request->gaji_karyawan,
            'alamat' => $request->alamat,
            'jenis_kelamin' => $request->jenis_kelamin, // update departemen_id
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data berhasil diperbarui');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //

        $data = Karyawan::where('nip', $id)->first();
        File::delete(public_path('foto').'/'.$data->foto);

        Karyawan::where('nip', $id)->delete();
        return redirect('karyawan')->with('success', 'Data Berhasil Dihapus!');
    }
}
