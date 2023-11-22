<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('plantilla');
    }

    public function salir()
    {
        $session = session();
        $session->destroy();

        return redirect()->to(base_url('/'));
    }
}
