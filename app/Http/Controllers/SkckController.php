<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ayah;
use App\Fisik;
use App\Ibu;
use App\Keterangan;
use App\Lampiran;
use App\Pasangan;
use App\Pendidikan;
use App\Pidana;
use App\Pribadi;
use App\Satwil;
use App\Saudara;
use App\User;
use Alert;
use Session;
use Illuminate\Support\Facades\Hash;

class SkckController extends Controller
{
    public function index()
    {
        return view('layouts.app');
    }
    public function pendaftaran()
    {
        return view('layouts.client-side.pendaftaran-skck');
    }

    public function store_skck(Request $request)
    {

        // dd($request->agama_pasangan);
        $pribadi = Pribadi::create($request->all());
        $pribadi_id = Pribadi::all()->pluck('id')->last();
        if ($pribadi) {
            $request->request->add(['kode_unik' => date('dmyhs') ]);
            $satwil = $pribadi->satwil()->create($request->all());
            $ayah = $pribadi->ayah()->create($request->all());
            $pasangan = $pribadi->pasangan()->create($request->all());
            $ibu = $pribadi->ibu()->create($request->all());
            $pendidikan = $pribadi->pendidikan()->create($request->all());
            $pidana = $pribadi->pidana()->create($request->all());
            $fisik = $pribadi->fisik()->create($request->all());
            $keterangan = $pribadi->keterangan()->create($request->all());
            $saudara = $pribadi->saudaras()->create($request->all());
            if ($request->hasFile('ktp')) {
                $fileExtention = $request->file('ktp')->getClientOriginalExtention();
                $uploadKTP = $request->file('ktp')->move('lampiran/'. $request->nama_lengkap_pendaftar . "_lapiran_ktp" . '.' . $fileExtention);
            }else{
                $uploadKTP = "Belum Di Upload";
            }
            if ($request->hasFile('paspor')) {
                $fileExtention = $request->file('paspor')->getClientOriginalExtention();
                $uploadPaspor = $request->file('paspor')->move('lampiran/'. $request->nama_lengkap_pendaftar . "_lapiran_paspor" . '.' . $fileExtention);
            }else{
                $uploadPaspor = "Belum Di Upload";
            }
            if ($request->hasFile('kartu_keluarga')) {
                $fileExtention = $request->file('kartu_keluarga')->getClientOriginalExtention();
                $uploadkartu_keluarga = $request->file('kartu_keluarga')->move('lampiran/'. $request->nama_lengkap_pendaftar . "_lapiran_kartu_keluarga" . '.' . $fileExtention);
            }else{
                $uploadkartu_keluarga = "Belum Di Upload";
            }
            if ($request->hasFile('akte')) {
                $fileExtention = $request->file('akte')->getClientOriginalExtention();
                $uploadAkte = $request->file('akte')->move('lampiran/'. $request->nama_lengkap_pendaftar . "_lapiran_akte" . '.' . $fileExtention);
            }else{
                $uploadAkte = "Belum Di Upload";
            }

            if ($request->hasFile('sidik_jari')) {
                $fileExtention = $request->file('sidik_jari')->getClientOriginalExtention();
                $uploadSidik_jari = $request->file('sidik_jari')->move('lampiran/'. $request->nama_lengkap_pendaftar . "_lapiran_sidik_jari" . '.' . $fileExtention);
            }else{
                $uploadSidik_jari = "Belum Di Upload";
            }

            $lampiran = $pribadi->lampiran()->create([
                'ktp' => $uploadKTP,
                'paspor'    => $uploadPaspor,
                'kartu_keluarga'    => $uploadkartu_keluarga,
                'akte'  => $uploadAkte,
                'sidik_jari'    => $uploadSidik_jari,
            ]);

            return "Berhasil Kesimpan";
        }else{
            return redirect()->back()->withInput();
        }
        return $request->all();
    }

    public function daftar_skck()
    {
        $dataPendaftar = Pribadi::all();
        return view('layouts.admin-side.skck.daftar-skck')->with(['semua_skck' => $dataPendaftar]);
    }

    public function install()
    {
        $total_user = User::all()->count();
        if ($total_user < 1) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@mail.com',
                'password' => Hash::make('rahasia'),
            ]);
            Alert::success('Berhasil', 'Penambahan Akun Berhasil');
            return redirect('admin');
        }else{
            Alert::warning('Peringatan', 'Akun Sudah Tersedia Silahkan Login');
            return redirect('/login');
        }
    }

    public function template()
    {
        return view('layouts.admin-side.template');
    }

    public function detail_pendaftar($id)
    {
        $biodata  = Pribadi::find($id);
        return view('layouts.admin-side.skck.detail-skck')->with(['biodata' => $biodata]);
    }

    public function sunting_pendaftar($id)
    {
        $biodata = Pribadi::find($id);
        return view('layouts.admin-side.skck.edit-skck')->with(['biodata' => $biodata]);
    }

    public function hapus_pendaftar($id)
    {
        $biodata = Pribadi::find($id)->delete();
        Session::flash('hapus-pendaftar');
        return redirect()->back();
    }
}
