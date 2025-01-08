<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    public function index()
    {
        if (Auth::user()->type === 'admin') {
            $announcements = Announcement::with('enterprise')->latest()->get();
        } else {
            $announcements = Announcement::where('user_id', Auth::id())->latest()->get();
        }
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        if (Auth::user()->type !== 'enterprise') {
            abort(403, 'Unauthorized action.');
        }
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->type !== 'enterprise') {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $announcement = new Announcement();
        $announcement->title = $validated['title'];
        $announcement->description = $validated['description'];
        $announcement->user_id = Auth::id();
        $announcement->status = 'pending';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcements', 'public');
            $announcement->image = $path;
        }

        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('success', 'Annonce créée avec succès et en attente de validation.');
    }

    public function edit(Announcement $announcement)
    {
        if (Auth::user()->type !== 'enterprise' || Auth::id() !== $announcement->user_id) {
            abort(403, 'Unauthorized action.');
        }
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if (Auth::user()->type !== 'enterprise' || Auth::id() !== $announcement->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $announcement->title = $validated['title'];
        $announcement->description = $validated['description'];
        $announcement->status = 'pending'; // Repasse en attente après modification

        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $path = $request->file('image')->store('announcements', 'public');
            $announcement->image = $path;
        }

        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('success', 'Annonce mise à jour avec succès et en attente de nouvelle validation.');
    }

    public function destroy(Announcement $announcement)
    {
        if (Auth::user()->type !== 'enterprise' || Auth::id() !== $announcement->user_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Annonce supprimée avec succès.');
    }

    public function validate(Announcement $announcement)
    {
        if (Auth::user()->type !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $announcement->status = 'approved';
        $announcement->save();

        return redirect()->back()->with('success', 'Annonce validée avec succès.');
    }

    public function reject(Request $request, Announcement $announcement)
    {
        if (Auth::user()->type !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|max:255'
        ]);

        $announcement->status = 'rejected';
        $announcement->rejection_reason = $validated['rejection_reason'];
        $announcement->save();

        return redirect()->back()->with('success', 'Annonce rejetée avec succès.');
    }
}
