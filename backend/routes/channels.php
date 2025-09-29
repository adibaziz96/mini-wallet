<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('user.{id}', function ($authUser, $id) {
    return $authUser && ((int) $authUser->id === (int) $id);
});
