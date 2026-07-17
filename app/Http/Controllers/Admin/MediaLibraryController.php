<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MediaGroup;
use App\Models\MediaAsset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\DataTables\MediaGroupDataTable;
use App\DataTables\MediaAssetDataTable;

class MediaLibraryController extends Controller
{
    public function index(MediaGroupDataTable $dataTable)
    {
        return $dataTable->render('admin.media_library.index');
    }

    public function show(MediaGroup $group, MediaAssetDataTable $dataTable)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }
        return $dataTable->withGroupId($group->id)->render('admin.media_library.show', compact('group'));
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MediaGroup::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
        ]);

        return redirect()->route('admin.media_library.index')->with('success', 'Media Group created successfully.');
    }

    public function updateGroup(Request $request, MediaGroup $group)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.media_library.index')->with('success', 'Media Group renamed successfully.');
    }

    public function destroyGroup(MediaGroup $group)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete all assets files
        foreach ($group->assets as $asset) {
            Storage::delete($asset->file_path);
        }
        
        $group->delete();

        return redirect()->route('admin.media_library.index')->with('success', 'Media Group deleted successfully.');
    }

    public function storeAsset(Request $request, MediaGroup $group)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:16384', // 16MB max
        ]);

        $file = $request->file('file');
        
        // Auto-generate unique readable asset code
        $baseSlug = strtoupper(Str::slug($request->name, '_'));
        $randomSuffix = strtoupper(Str::random(4));
        $assetCode = $baseSlug . '_' . $randomSuffix;

        $path = $file->store('media_library/' . Auth::id(), 'public');

        MediaAsset::create([
            'media_group_id' => $group->id,
            'asset_code' => $assetCode,
            'name' => $request->name,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'status' => 'active',
        ]);

        return redirect()->route('admin.media_library.groups.show', $group->id)->with('success', 'Media Asset uploaded successfully. Asset Code: ' . $assetCode);
    }

    public function updateAsset(Request $request, MediaAsset $asset)
    {
        if ($asset->group->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $asset->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.media_library.groups.show', $asset->media_group_id)->with('success', 'Asset renamed successfully.');
    }

    public function updateAssetStatus(Request $request, MediaAsset $asset)
    {
        $request->validate(['status' => 'required|in:active,inactive']);
        
        if ($asset->group->user_id !== Auth::id()) {
            abort(403);
        }

        $asset->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function destroyAsset(MediaAsset $asset)
    {
        if ($asset->group->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($asset->file_path);
        $asset->delete();

        return redirect()->route('admin.media_library.groups.show', $asset->media_group_id)->with('success', 'Media Asset deleted successfully.');
    }
}
