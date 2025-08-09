<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CodigoSeguridadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas para usuarios
Route::post('/register', [UsuarioController::class, 'register']);
Route::post('/login', [UsuarioController::class, 'login'])->name('login');
Route::post('/cambiar-contrasena', [UsuarioController::class, 'actualizarContrasena']);
Route::post('/enviar-codigo', [CodigoSeguridadController::class, 'enviarCodigo']);
Route::post('/validar-codigo', [CodigoSeguridadController::class, 'validarCodigo']);

// Rutas públicas para administradores
Route::post('/admin/register', [AdminController::class, 'register']);
Route::post('/admin/login', [AdminController::class, 'login']);

// Rutas protegidas para usuarios regulares
Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckRoleUsuario::class])->group(function () {
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

// Rutas protegidas para administradores
Route::middleware(['auth:admin-sanctum', \App\Http\Middleware\CheckRoleAdmin::class])->group(function () {
    Route::get('/usuarios', [AdminController::class, 'list']);
    Route::get('/admin/sesion', [SessionController::class, 'index']);
    Route::get('/admin/validar-sesion', [SessionController::class, 'validarSesion']);
    Route::post('/admin/editar-perfil', [AdminController::class, 'editarPerfil']);
    Route::post('/admin/actualizar-contrasena', [AdminController::class, 'newPassword']);
    Route::post('/admin/sesiones/cerrar-actual', [SessionController::class, 'destroyCurrent']);
    Route::delete('/admin/sesiones/{id}', [SessionController::class, 'destroy']);
    Route::delete('/admin/sesiones', [SessionController::class, 'destroyAll']);
});