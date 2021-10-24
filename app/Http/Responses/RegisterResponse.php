<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json(['user' => auth()->user()->only(['name', 'email', 'phone', 'city', 'country'])])
            : redirect(config('fortify.home'));
    }
}
