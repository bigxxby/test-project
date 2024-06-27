<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * Регистрация нового пользователя.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {


        // Валидация данных запроса
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|unique:users',
            'email' => 'required|string|email:rfc,dns|unique:users', //TODO: как валидировать емейл? при //email:rfc,dns долгий запрос
            'password' => 'required|string|min:8',
        ]);

        // Если валидация не прошла, возвращаем ошибку 400
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Создание пользователя, если валидация успешна
        $user = User::create([
            'login' => $request->login,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Возвращаем успешный ответ 201 с данными пользователя
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (Auth::attempt(['login' => $request->login, 'password' => $request->password])) {
            // Аутентификация успешна
            $user = Auth::user();
            $token = $user->createToken('AuthToken')->plainTextToken;
            return response()->json(['message' => 'Login successful', 'token' => $token, 'user' => $user], 200);
        } else {
            // Неверные учетные данные
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }


    public function whoami(Request $request): JsonResponse
    {
        $user = Auth::user();

        return response()->json(['user' => $user], 200);
    }

}
