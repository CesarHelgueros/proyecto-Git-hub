<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // 1. INDEX: Listar todos los usuarios
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    // 2. STORE: Guardar un nuevo usuario con los campos solicitados
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:255',
            'correo'   => 'required|string|email|max:255|unique:users,email',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'foto'     => 'nullable|string|max:255', // Guardamos el nombre o ruta de la foto
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Crear el usuario mapeando tus campos a las columnas de la BD
        $user = User::create([
            'name'     => $request->nombre,
            'email'    => $request->correo,
            'telefono' => $request->telefono,
            'foto'     => $request->foto ?? 'default.png',
            'password' => Hash::make($request->password),
        ]);

        // Retornamos el usuario creado. Laravel ya incluirá el "created_at" (timestamp) aquí.
        return response()->json([
            'message' => 'Usuario guardado exitosamente',
            'user' => $user
        ], 201);
    }

    // 3. SHOW: Mostrar un usuario específico por su ID
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user, 200);
    }
}