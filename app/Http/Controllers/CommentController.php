<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string|max:255',
            'commentable_id' => 'required|integer',
            'body' => 'required|string|max:2000',
        ]);

        $validated['created_by'] = auth()->id() ?? 1; // Fallback to 1 if not logged in

        $comment = Comment::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Comment added successfully.', 'comment' => $comment]);
        }

        return back()->with('success', 'Comment added successfully.');
    }

    public function destroy(Request $request, Comment $comment)
    {
        // Optional: authorize user is the creator or an admin
        // if ($comment->created_by !== auth()->id()) { abort(403); }

        $comment->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Comment deleted successfully.']);
        }

        return back()->with('success', 'Comment deleted successfully.');
    }
}
