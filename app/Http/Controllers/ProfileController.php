<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class ProfileController extends Controller
{
public function show()
{
return view('profile');
}

public function update(Request $request)
{
$validated = $request->validate([
'name' => 'required|string|max:255',
'phone' => 'required|string|max:20'
]);

auth()->user()->update($validated);

return response()->json([
'success' => true,
'message' => 'Profile updated successfully'
]);
}
}