<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Регистрация пользователя
    public function register(Request $request): JsonResponse
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Если валидация не прошла
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Создание нового пользователя
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    // Логин пользователя
    public function login(Request $request): JsonResponse
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Если валидация не прошла
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Аутентификация пользователя
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token, // Возвращаем токен
            ], 200);
        }

        // Если аутентификация не прошла
        return response()->json([
            'error' => 'Unauthorized',
        ], 401);
    }

    // Логаут пользователя
    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'User logged out successfully']);
    }
}
