<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeveloperSettingController extends Controller
{
    public function index(\App\DataTables\DeveloperTokenDataTable $dataTable)
    {
        return $dataTable->render('admin.developer_settings.index');
    }

    public function docs()
    {
        return view('admin.developer_settings.docs');
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string|max:255'
        ]);

        $user = auth()->user();
        $token = $user->createToken($request->token_name);

        return redirect()->route('admin.developer_settings.index')
            ->with('success', 'API Key generated successfully!')
            ->with('new_token', $token->plainTextToken);
    }

    public function revokeToken($id)
    {
        $user = auth()->user();
        $user->tokens()->where('id', $id)->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.developer_settings.index')->with('success', 'API Key revoked successfully.');
    }
}
