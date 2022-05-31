<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $data = 'Teste';

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => $data
        ], 200);
    }
}
