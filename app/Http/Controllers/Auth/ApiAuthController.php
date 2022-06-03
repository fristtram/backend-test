<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Auth;

class ApiAuthController extends Controller
{
    private $user;
    private $baseService;

    /**
    * @return void
    */
    public function __construct(User $user, BaseService $baseService)
    {
        $this->user = $user;
        $this->baseService = $baseService;
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
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function validated($request)
    {
        return $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
            $user = auth()->user();

            $token = $user->createToken($user->email);
            return ['token' => $token->plainTextToken];
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email or password is incorrect'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function validateLogin($request)
    {
        $data = $request->all();
        $validation = Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string'
        ]);
        if ($validation->fails()) {
            $erro = $validation->errors();
            $message = '';
            for ($i=0; $i < count($erro) ; $i++) {
                $result = json_decode($erro);
                if (isset($result->email[0])) {
                   $message = $result->email[0];
                }
                if (isset($result->password[0])) {
                    $message = $message.' '.$result->password[0];
                }
            }
            return response()->json([
                'success' => false,
                'message' => $message
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }
    }
}
