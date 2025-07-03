<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RolesController extends Controller
{
    /**
     * Login de usuario con verificación de roles
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Buscar usuario por email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Crear token de acceso
        $token = $user->createToken('auth_token')->plainTextToken;

        // Obtener roles del usuario
        $roles = $user->getRoleNames();

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Logout del usuario
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ]);
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $roles = $user->getRoleNames();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles
                ]
            ]
        ]);
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function checkRole(Request $request, $role)
    {
        $user = $request->user();
        $hasRole = $user->hasRole($role);

        return response()->json([
            'success' => true,
            'data' => [
                'has_role' => $hasRole,
                'role' => $role,
                'user_roles' => $user->getRoleNames()
            ]
        ]);
    }

    /**
     * Asignar rol a usuario (solo administradores)
     */
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:administrador,empleado'
        ]);

        // Verificar que el usuario actual es administrador
        if (!$request->user()->hasRole('administrador')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        $user = User::findOrFail($request->user_id);
        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => "Rol '{$request->role}' asignado exitosamente",
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()
                ]
            ]
        ]);
    }

    /**
     * Remover rol de usuario (solo administradores)
     */
    public function removeRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:administrador,empleado'
        ]);

        // Verificar que el usuario actual es administrador
        if (!$request->user()->hasRole('administrador')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        $user = User::findOrFail($request->user_id);
        $user->removeRole($request->role);

        return response()->json([
            'success' => true,
            'message' => "Rol '{$request->role}' removido exitosamente",
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()
                ]
            ]
        ]);
    }

    /**
     * Listar usuarios con sus roles (solo administradores)
     */
    public function listUsers(Request $request)
    {
        // Verificar que el usuario actual es administrador
        if (!$request->user()->hasRole('administrador')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        $users = User::with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
                'created_at' => $user->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Crear nuevo usuario con rol (solo administradores)
     */
    public function createUser(Request $request)
    {
        // Verificar que el usuario actual es administrador
        if (!$request->user()->hasRole('administrador')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:administrador,empleado'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()
                ]
            ]
        ], 201);
    }

    /**
     * Dashboard según el rol del usuario
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        if ($user->hasRole('administrador')) {
            return $this->adminDashboard($user);
        } elseif ($user->hasRole('empleado')) {
            return $this->empleadoDashboard($user);
        }

        return response()->json([
            'success' => false,
            'message' => 'No tienes un rol asignado'
        ], 403);
    }

    /**
     * Dashboard del administrador
     */
    private function adminDashboard($user)
    {
        // Aquí puedes agregar estadísticas específicas para administradores
        $stats = [
            'total_usuarios' => User::count(),
            'total_compras' => \App\Models\Compras::count(),
            'total_detalles' => \App\Models\DetalleCompras::count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard de administrador',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'role' => 'administrador'
                ],
                'stats' => $stats,
                'permissions' => [
                    'can_create_users' => true,
                    'can_assign_roles' => true,
                    'can_view_all_data' => true
                ]
            ]
        ]);
    }

    /**
     * Dashboard del empleado
     */
    private function empleadoDashboard($user)
    {
        // Aquí puedes agregar estadísticas específicas para empleados
        $stats = [
            'mis_compras' => \App\Models\Compras::where('id_empleado', $user->id)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard de empleado',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'role' => 'empleado'
                ],
                'stats' => $stats,
                'permissions' => [
                    'can_create_users' => false,
                    'can_assign_roles' => false,
                    'can_view_all_data' => false
                ]
            ]
        ]);
    }
}