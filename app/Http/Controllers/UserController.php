<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    private $user;
    private $baseService;

    /**
    *
    * @return void
    */
    public function __construct(User $user, BaseService $baseService)
    {
        $this->user = $user;
        $this->baseService = $baseService;
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->baseService->getAll($this->user)
        ], 200);
    }

    public function create(Request $request)
    {
        try {
            $this->validated($request);
            $payload = [
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password'))
            ];

            $data = $this->baseService->create($this->user, $payload);

            return response()->json([
                'success' => true,
                'data' => $data
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    public function validated($request)
    {
        return $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);
    }
}
