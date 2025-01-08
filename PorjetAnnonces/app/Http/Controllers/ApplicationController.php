<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    // Liste des candidatures (pour entreprise et admin)
    public function index()
    {
        if (Auth::user()->type === 'enterprise') {
            // Pour une entreprise, on récupère les candidatures de ses annonces
            $applications = Application::whereHas('announcement', function($query) {
                $query->where('user_id', Auth::id());
            })->with(['student', 'announcement'])->get();
        } elseif (Auth::user()->type === 'admin') {
            // Pour l'admin, toutes les candidatures
            $applications = Application::with(['student', 'announcement'])->get();
        } else {
            // Pour un étudiant, ses propres candidatures
            $applications = Application::where('user_id', Auth::id())
                ->with(['announcement'])->get();
        }
        
        return view('applications.index', compact('applications'));
    }

    // Voir les candidatures pour une annonce spécifique (entreprise)
    public function showForAnnouncement(Announcement $announcement)
    {
        if (Auth::user()->type !== 'enterprise' || Auth::id() !== $announcement->user_id) {
            abort(403, 'Non autorisé');
        }

        $applications = $announcement->applications()->with('student')->get();
        return view('applications.show-for-announcement', compact('applications', 'announcement'));
    }

    // Postuler à une annonce (étudiant)
    public function apply(Request $request, Announcement $announcement)
    {
        if (Auth::user()->type !== 'student') {
            abort(403, 'Non autorisé');
        }

        // Vérifier si l'étudiant n'a pas déjà postulé
        $existingApplication = Application::where('announcement_id', $announcement->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()->back()
                ->with('error', 'Vous avez déjà postulé à cette annonce');
        }

        Application::create([
            'announcement_id' => $announcement->id,
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Votre candidature a été envoyée avec succès');
    }

    // Retirer sa candidature (étudiant)
    public function withdraw(Announcement $announcement)
    {
        if (Auth::user()->type !== 'student') {
            abort(403, 'Non autorisé');
        }

        $application = Application::where('announcement_id', $announcement->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $application->delete();

        return redirect()->back()
            ->with('success', 'Votre candidature a été retirée avec succès');
    }

    // Mettre à jour le statut d'une candidature (entreprise)
    public function updateStatus(Request $request, Application $application)
    {
        // Vérifier que c'est bien l'entreprise propriétaire de l'annonce
        if (Auth::user()->type !== 'enterprise' || 
            Auth::id() !== $application->announcement->user_id) {
            abort(403, 'Non autorisé');
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,pending',
        ]);

        $application->update([
            'status' => $validated['status']
        ]);

        return redirect()->back()
            ->with('success', 'Le statut de la candidature a été mis à jour');
    }
}
