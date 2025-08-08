<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CodigoSeguridadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UsuarioController::class, 'register']);
Route::post('/login', [UsuarioController::class, 'login'])->name('login');
Route::post('/cambiar-contrasena', [UsuarioController::class, 'actualizarContrasena']);
Route::post('/enviar-codigo', [CodigoSeguridadController::class, 'enviarCodigo']);
Route::post('/validar-codigo', [CodigoSeguridadController::class, 'validarCodigo']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'list']);
    Route::post('/multas', [MultaController::class, 'store']);
    Route::get('/multas/usuario/{id}', [MultaController::class, 'multasPorUsuario']);
    Route::post('/multas/{id}/vista', [MultaController::class, 'marcarComoVista']);
    Route::get('/multas/reciente/{id}', [MultaController::class, 'multaRecientePorUsuario']);
    Route::get('/sesion', [SessionController::class, 'index']);
    Route::get('/validar-sesion', [SessionController::class, 'validarSesion']);
    Route::delete('/sesiones/{id}', [SessionController::class, 'destroy']);
    Route::delete('/sesiones', [SessionController::class, 'destroyAll']);
    Route::post('/sesiones/cerrar-actual', [SessionController::class, 'destroyCurrent']);
    Route::post('/editar-perfil', [UsuarioController::class, 'editarPerfil']);
    Route::post('/editar-datos', [UsuarioController::class, 'editarDatos']);
    Route::post('/cambiar-correo', [UsuarioController::class, 'cambiarCorreo']);
    Route::post('/validar-password-actual', [UsuarioController::class, 'validarPasswordActual']);
    Route::post('/cambiar-contrasena-autenticado', [UsuarioController::class, 'cambiarContrasena']);
    Route::post('/enviar-codigo-auth', [CodigoSeguridadController::class, 'enviarCodigo']);
    Route::post('/validar-codigo-auth', [CodigoSeguridadController::class, 'validarCodigo']);
});