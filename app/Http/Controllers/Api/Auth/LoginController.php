<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 * name="Autenticación",
 * description="Endpoints para login y logout de usuarios"
 * )
 */
class LoginController extends Controller
{
    /**
     * @OA\Post(
     * path="/login",
     * operationId="loginUser",
     * tags={"Autenticación"},
     * summary="Iniciar sesión de usuario",
     * description="Retorna un token de API si las credenciales son correctas.",
     * @OA\RequestBody(
     * required=true,
     * description="Credenciales del usuario",
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="usuario@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Inicio de sesión exitoso",
     * @OA\JsonContent(
     * @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Error de validación o credenciales incorrectas",
     * @OA\JsonContent(ref="#/components/schemas/ErrorValidation")
     * )
     * )
     */
    public function login(LoginRequest $request)
    {
        $request->validated();

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $request->user()->tokens()->where('name', 'api-token')->delete();

        $token = $request->user()->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * @OA\Post(
     * path="/logout",
     * operationId="logoutUser",
     * tags={"Autenticación"},
     * summary="Cerrar sesión del usuario actual",
     * description="Invalida el token de API actual del usuario.",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Sesión cerrada exitosamente",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Sesión cerrada correctamente.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="No autenticado"
     * )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}
