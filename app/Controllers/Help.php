<?php

namespace App\Controllers;

class Help extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Help & Documentatie',
        ];

        return view('help/index', $data);
    }
}
