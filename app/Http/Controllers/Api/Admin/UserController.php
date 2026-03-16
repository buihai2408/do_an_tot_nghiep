<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:customer,admin,staff',
        ]);

        $user->update($data);
        return response()->json(['message' => 'Đã cập nhật!', 'user' => $user]);
    }
}
