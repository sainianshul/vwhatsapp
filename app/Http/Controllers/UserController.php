<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive,banned',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'job_title' => $request->job_title,
            'company' => $request->company,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        // Fetch user's login history
        $loginHistory = \App\Models\LoginHistory::where('user_id', $user->id)
                            ->orderBy('logged_in_at', 'desc')
                            ->take(10)
                            ->get();

        return view('users.show', compact('user', 'loginHistory'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive,banned',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'job_title' => $request->job_title,
            'company' => $request->company,
            'status' => $request->status,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete(); // Soft delete
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = User::onlyTrashed()->where('role', '!=', User::ROLE_ADMIN);
            return datatables()->eloquent($query)
                ->editColumn('created_at', function(User $user) {
                    return $user->created_at ? $user->created_at->format('d M Y, h:i A') : '-';
                })
                ->addColumn('deleted_at', function (User $user) {
                    return $user->deleted_at ? $user->deleted_at->format('d M Y, h:i A') : '-';
                })
                ->addColumn('status', function (User $user) {
                    $color = $user->status_color;
                    return '<span class="badge badge-light-'.$color.'">'.ucfirst($user->status).'</span>';
                })
                ->addColumn('actions', function (User $user) {
                    return '
                        <div class="d-flex gap-1 justify-content-end">
                            <form action="'.route('users.restore', $user->id).'" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm(\'Are you sure you want to restore this user?\');">
                                '.csrf_field().'
                                <button type="submit" class="btn btn-sm btn-icon btn-light-success border border-success w-30px h-30px" title="Restore">
                                    <i class="ki-outline ki-arrows-circle fs-5"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }
        return view('users.trash');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('users.trash')->with('success', 'User restored successfully.');
    }
}
