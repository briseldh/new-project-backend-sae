<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\GetDataController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfilePicController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


//=================Public Routes=================//
Route::post('/register', RegisterController::class);
Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class);
Route::get('/post/getAll', [PostController::class, 'getAll']);



Route::middleware('auth:web')->group(function () {

    Route::controller(GetDataController::class)->group(function () {

        Route::get('/getUserData', 'getUserData');
        Route::get('/getUser/{user}', 'getUserById');
    });

    Route::controller(UserController::class)->group(function () {
        Route::delete('/user/delete', 'delete');
    });

    Route::controller(PostController::class)->group(function () {

        Route::post('/post/insert', 'insert');
        Route::patch('/post/update/{id}', 'update');
        Route::delete('/post/delete/{id}', 'delete');
        Route::get('/post/getById/{id}', 'getById');
    });

    Route::controller(CommentController::class)->group(function () {

        Route::post('/comment/insert/{post}', 'insert');
        Route::patch('/comment/update/{id}', 'update');
        Route::delete('/comment/delete/{id}', 'delete');
        Route::get('/comment/getById/{id}', 'getById');
        Route::get('/comment/getAll', 'getAll');
    });

    Route::controller(LikeController::class)->group(function () {
        Route::get('/like/getUserLikes', 'getUserLikes');
        Route::get('/like/getLikeCount', 'getLikeCount');
        Route::post('/like/{post}', 'like');
        Route::post('/dislike/{post}', 'dislike');
    });

    Route::controller(ProfilePicController::class)->group(function () {
        Route::post('/profilePic/insert', 'insert');
        Route::post('/profilePic/update/{id}', 'update');
        Route::delete('profilePic/delete/{id}', 'delete');
        Route::get('/profilePic/getById/{id}', 'getById');
        Route::get('/profilePic/getAll', 'getAll');
    });
});


Route::fallback(function () {
    return response()->json(['message' => 'Route Not Found'], 404);
});



// pm.sendRequest({
//     url: 'http://localhost/sanctum/csrf-cookie',
//     method: 'GET',
// }, (error, response) => {
//     console.log(error)
//     console.log(response.headers.all())

//     response.headers.all().find(( header ) => {
//         if(header.key === "Set-Cookie"){
//             if(header.value.startsWith("XSRF-TOKEN")){
//                 const pattern = new RegExp(`(?<=XSRF-TOKEN=)[^;]+`, 'g');
//                 const token = header.value.match(pattern)[0];
//                 const decodedToken = decodeURIComponent(token)
//                 pm.environment.set('xsrf-token', decodedToken)
//             }
//         }
//     });
// })
