<?php

namespace App\Http\Controllers;

use App\Models\Empleados;
use App\Models\Perfil_x_Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Método para registrar usuarios
    public function register(Request $request)
    {
        // Validar los datos del request
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:8'
        ]);

        // Crear el usuario
        $user = Empleados::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'id_perfil' => 1,
            'id_permiso' => 1
        ]);

        return response()->json(['empleado' => $user], 201);

        // Generar un token de acceso personal
        $token = $user->createToken('auth_token')->plainTextToken;
        // ...

        // Retornar el usuario y el token
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
    ]);
    }



    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ], [
            'password.min' => 'La contraseña debe de ser de al menos 8 caracteres.',
        ]);

        if (!Auth::attempt($validatedData)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        $perfilController = new PerfilesController();
        $perfil = $perfilController->showPerfil();

        return response()->json([
            'user' => $user,
            'perfil' => $perfil,
            'permiso' => $user->id_permiso,
            'access_token' => $token,
            'ventas' => $user->ventas,
            'token_type' => 'Bearer',
        ]);

        
    }

    public function logout()
    {
        if (auth()->check()) {
            auth()->user()->token()->revoke();
            return response()->json(['message' => 'Sesión cerrada con éxito']);
        } else {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    }


}
