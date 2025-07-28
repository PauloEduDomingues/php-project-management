<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function changeRole (Request $request, User $user) {
        $validated = $request->validate([
            'changeTo' => 'required|exists:roles,name'
        ]);

        if ($validated['changeTo'] === 'admin') {
            $user->syncRoles(['admin', 'manager', 'colaborator']);
        } else if ($validated['changeTo'] === 'manager') {
            $user->syncRoles(['manager', 'colaborator']);
        } else {
            $user->syncRoles(['colaborator']);
        }

        return response()->json($validated);
    }
}
