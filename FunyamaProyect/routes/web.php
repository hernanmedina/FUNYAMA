<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard: only show the dashboard view to users with the `admin` role.
    // If the authenticated user does NOT have the admin role, redirect them
    // to the `not-authorized` route where we will show a placeholder view.
    Route::get('/dashboard', function () {
        $user = auth ()->user();

        // If the user model has a `hasRole` method (e.g. spatie/permission),
        // use it to check the role. If not, fall back to checking a `role`
        // attribute on the user model. Adjust as needed for your app.
        $isAdmin = false;
        if ($user) {
            if (method_exists($user, 'hasRole')) {
                $isAdmin = $user->hasRole('admin');
            } elseif (isset($user->role)) {
                $isAdmin = $user->role === 'admin';
            }
        }

        if ($isAdmin) {
            return view('dashboard');
        }

        return redirect()->route('not-authorized');
    })->name('dashboard');

    // Route for users that are authenticated but not authorized as admin.
    // The view `not-authorized` can be created later and will be shown to
    // non-admin users.
    Route::get('/not-authorized', function () {
        return view('not-authorized');
    })->name('not-authorized');

    //Route::post('/admin/posts', [PostController::class, 'store'])->name('admin.posts.store');
});
