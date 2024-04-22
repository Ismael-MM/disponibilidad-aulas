<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SuscriptorController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\AulaCursoController;
use App\Http\Controllers\FestivoController;
use App\Http\Controllers\CursoSedeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/', function () {
    return redirect()->route('login');
})->name('landing.index');

Route::middleware('auth')->prefix('dashboard')->group(function () {

    //Routes must have the following structure to work with the dialogs:
    // index: /item
    // store: /item
    // update: /item/{id}
    // destroy: /item/{id}
    // destroyPermanent: /item/{id}/permanent
    // restore: /item/{id}/restore
    // exportExcel: /item/export-excel


    //Suscriptor
    Route::get('/suscriptores', [SuscriptorController::class, 'index'])->name('dashboard.suscriptores');
    Route::post('/suscriptores/load-items', [SuscriptorController::class, 'loadItems'])->name('dashboard.suscriptores.load-items');
    Route::post('/suscriptores', [SuscriptorController::class, 'store'])->name('dashboard.suscriptores.store');
    Route::put('/suscriptores/{subscriber}', [SuscriptorController::class, 'update'])->name('dashboard.suscriptores.update');
    Route::delete('/suscriptores/{subscriber}', [SuscriptorController::class, 'destroy'])->name('dashboard.suscriptores.destroy');
    Route::delete('/suscriptores/{subscriber}/permanent', [SuscriptorController::class, 'destroyPermanent'])->name('dashboard.suscriptores.destroyPermanent');
    Route::post('/suscriptores/{id}/restore', [SuscriptorController::class, 'restore'])->name('dashboard.suscriptores.restore');
    Route::get('/suscriptores/export-excel', [SuscriptorController::class, 'exportExcel'])->name('dashboard.suscriptores.exportExcel');

    Route::get('/cursos',[CursoController::class, 'index'])->name('dashboard.cursos');
    Route::post('/cursos/load-items',[CursoController::class, 'loadItems'])->name('dashboard.cursos.load-itmes');
    Route::post('/cursos',[CursoController::class, 'store'])->name('dashboard.cursos.store');
    Route::put('/cursos/{curso}',[CursoController::class, 'update'])->name('dashboard.cursos.update');
    Route::delete('/cursos/{curso}',[CursoController::class, 'destroy'])->name('dashboard.cursos.destroy');
    Route::delete('/cursos/{curso}/permanent',[CursoController::class, 'destroyPermanent'])->name('dashboard.cursos.destroyPermanent');
    Route::post('/cursos/{curso}/restore',[CursoController::class, 'restore'])->name('dashboard.cursos.restore');
    Route::get('/cursos/export-excel',[CursoController::class, 'exportExcel'])->name('dashboard.cursos.exportExcel');
    Route::get('/cursos/list',[CursoController::class, 'cursosList'])->name('dashboard.cursos.list');
    Route::post('/cursos/load-autocomplete-items',[CursoController::class, 'loadAutocompleteItems'])->name('dashboard.cursos.autocomplete');

    Route::get('/sedes',[SedeController::class, 'index'])->name('dashboard.sedes');
    Route::post('/sedes/load-items',[SedeController::class, 'loadItems'])->name('dashboard.sedes.load-itmes');
    Route::post('/sedes',[SedeController::class, 'store'])->name('dashboard.sedes.store');
    Route::put('/sedes/{sede}',[SedeController::class, 'update'])->name('dashboard.sedes.update');
    Route::delete('/sedes/{sede}',[SedeController::class, 'destroy'])->name('dashboard.sedes.destroy');
    Route::delete('/sedes/{sede}/permanent',[SedeController::class, 'destroyPermanent'])->name('dashboard.sedes.destroyPermanent');
    Route::post('/sedes/{sede}/restore',[SedeController::class, 'restore'])->name('dashboard.sedes.restore');
    Route::get('/sedes/export-excel',[SedeController::class, 'exportExcel'])->name('dashboard.sedes.exportExcel');

    Route::get('/aulas',[AulaController::class, 'index'])->name('dashboard.aulas');
    Route::post('/aulas/load-items',[AulaController::class, 'loadItems'])->name('dashboard.aulas.load-itmes');
    Route::post('/aulas',[AulaController::class, 'store'])->name('dashboard.aulas.store');
    Route::put('/aulas/{aula}',[AulaController::class, 'update'])->name('dashboard.aulas.update');
    Route::delete('/aulas/{aula}',[AulaController::class, 'destroy'])->name('dashboard.aulas.destroy');
    Route::delete('/aulas/{aula}/permanent',[AulaController::class, 'destroyPermanent'])->name('dashboard.aulas.destroyPermanent');
    Route::post('/aulas/{aula}/restore',[AulaController::class, 'restore'])->name('dashboard.aulas.restore');
    Route::get('/aulas/export-excel',[AulaController::class, 'exportExcel'])->name('dashboard.aulas.exportExcel');
    Route::get('/aulas/list',[AulaController::class, 'aulasList'])->name('dashboard.aulas.list');

    Route::get('/reservar',[AulaCursoController::class, 'index'])->name('dashboard.reservar');
    Route::post('/reservar/load-items',[AulaCursoController::class, 'loadItems'])->name('dashboard.reservar.load-itmes');
    Route::post('/reservar',[AulaCursoController::class, 'store'])->name('dashboard.reservar.store');
    Route::put('/reservar/{reservas}',[AulaCursoController::class, 'update'])->name('dashboard.reservar.update');
    Route::delete('/reservar/{reservas}',[AulaCursoController::class, 'destroy'])->name('dashboard.reservar.destroy');
    Route::delete('/reservar/{reservas}/permanent',[AulaCursoController::class, 'destroyPermanent'])->name('dashboard.reservar.destroyPermanent');
    Route::post('/reservar/{reservas}/restore',[AulaCursoController::class, 'restore'])->name('dashboard.reservar.restore');
    Route::get('/reservar/export-excel',[AulaCursoController::class, 'exportExcel'])->name('dashboard.reservar.exportExcel');
    Route::get('/reservar/list',[AulaCursoController::class, 'reservaslist'])->name('dashboard.reservar.list');
    
    Route::get('/festivo',[FestivoController::class, 'index'])->name('dashboard.festivo');
    Route::post('/festivo/load-items',[FestivoController::class, 'loadItems'])->name('dashboard.festivo.load-itmes');
    Route::post('/festivo',[FestivoController::class, 'store'])->name('dashboard.festivo.store');
    Route::put('/festivo/{festivo}',[FestivoController::class, 'update'])->name('dashboard.festivo.update');
    Route::delete('/festivo/{festivo}',[FestivoController::class, 'destroy'])->name('dashboard.festivo.destroy');
    Route::delete('/festivo/{festivo}/permanent',[FestivoController::class, 'destroyPermanent'])->name('dashboard.festivo.destroyPermanent');
    Route::post('/festivo/{festivo}/restore',[FestivoController::class, 'restore'])->name('dashboard.festivo.restore');
    Route::get('/festivo/export-excel',[FestivoController::class, 'exportExcel'])->name('dashboard.festivo.exportExcel');
    Route::get('/festivo/list',[FestivoController::class, 'festivoList'])->name('dashboard.festivo.list');

    Route::get('/asignacion',[CursoSedeController::class, 'index'])->name('dashboard.asignacion');
    Route::post('/asignacion/load-items',[CursoSedeController::class, 'loadItems'])->name('dashboard.asignacion.load-itmes');
    Route::post('/asignacion',[CursoSedeController::class, 'store'])->name('dashboard.asignacion.store');
    Route::put('/asignacion/{asignacion}',[CursoSedeController::class, 'update'])->name('dashboard.asignacion.update');
    Route::delete('/asignacion/{asignacion}',[CursoSedeController::class, 'destroy'])->name('dashboard.asignacion.destroy');
    Route::delete('/asignacion/{asignacion}/permanent',[CursoSedeController::class, 'destroyPermanent'])->name('dashboard.asignacion.destroyPermanent');
    Route::post('/asignacion/{asignacion}/restore',[CursoSedeController::class, 'restore'])->name('dashboard.asignacion.restore');
    Route::get('/asignacion/export-excel',[CursoSedeController::class, 'exportExcel'])->name('dashboard.asignacion.exportExcel');
    Route::get('/asignacion/list',[CursoSedeController::class, 'asignacionList'])->name('dashboard.asignacion.list');

    Route::get('/calendario',[CalendarioController::class, 'index'])->name('dashboard.calendario');
});

require __DIR__ . '/auth.php';

