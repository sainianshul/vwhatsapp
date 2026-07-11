<?php

namespace App\Http\Controllers;

use App\DataTables\SubjectsDataTable;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SubjectsDataTable $dataTable)
    {
        return $dataTable->render('subjects.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'notes'       => 'nullable|string',
            'photo'       => 'nullable|image|max:2048',
        ]);

        $subject = new Subject($validated);
        $subject->user_id       = auth()->id();
        $subject->created_by_id = auth()->id();
        $subject->status        = Subject::STATUS_ACTIVE;

        if ($request->hasFile('photo')) {
            $subject->photo_url = $request->file('photo')->store('subjects', 'public');
        }

        $subject->save();

        return redirect()->route('subjects.show', $subject)->with('success', 'Profile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subject = Subject::with(['socialAccounts.posts', 'socialAccounts.automationRule.template', 'creator'])
            ->when(auth()->user()->role !== \App\Models\User::ROLE_ADMIN, function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->findOrFail($id);

        $templates = \App\Models\AutomationTemplate::where('created_by_id', auth()->id())->get();

        return view('subjects.show', compact('subject', 'templates'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subject = Subject::when(auth()->user()->role !== \App\Models\User::ROLE_ADMIN, function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);

        return view('subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subject = Subject::when(auth()->user()->role !== \App\Models\User::ROLE_ADMIN, function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'notes'       => 'nullable|string',
            'status'      => 'required|string|in:' . implode(',', array_keys(Subject::getStatusList())),
            'photo'       => 'nullable|image|max:2048',
        ]);

        $subject->fill($validated);

        if ($request->hasFile('photo')) {
            if ($subject->photo_url) {
                Storage::disk('public')->delete($subject->photo_url);
            }
            $subject->photo_url = $request->file('photo')->store('subjects', 'public');
        }

        $subject->save();

        return redirect()->route('subjects.show', $subject)->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subject::when(auth()->user()->role !== \App\Models\User::ROLE_ADMIN, function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);

        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Profile moved to trash.');
    }

    /**
     * Display a listing of trashed resources.
     */
    public function trash(SubjectsDataTable $dataTable)
    {
        return $dataTable->with('trash', true)->render('subjects.trash');
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(string $id)
    {
        $subject = Subject::onlyTrashed()
            ->when(auth()->user()->role !== \App\Models\User::ROLE_ADMIN, function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->findOrFail($id);

        $subject->restore();

        return redirect()->route('subjects.trash')->with('success', 'Profile restored successfully.');
    }
}
