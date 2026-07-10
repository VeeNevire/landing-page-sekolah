<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function beranda()
    {
        return view('beranda');
    }

    public function profil()
    {
        return view('profil');
    }

    public function akademik()
    {
        return view('akademik');
    }

    public function ppdb()
    {
        return view('ppdb');
    }

    public function kontak()
    {
        return view('kontak');
    }
}
