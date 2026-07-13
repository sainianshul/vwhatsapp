<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DataTables\LoginHistoryDataTable;

class LoginHistoryController extends Controller
{
    public function index(LoginHistoryDataTable $dataTable)
    {
        return $dataTable->render('users.login_history');
    }

    public function empty()
    {
        \App\Models\LoginHistory::truncate();
        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        \App\Models\LoginHistory::findOrFail($id)->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Log deleted');
    }
}
