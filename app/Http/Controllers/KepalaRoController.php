<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KepalaROController extends Controller
{
    // Dashboard: Daftar permintaan menunggu approval
    public function index()
    {
        $user = Auth::user(); // Kepala RO login

        $requests = Permintaan::with(['user', 'details'])
            ->where('status', 'pending')
            ->whereHas('user', function($q) use ($user) {
                $q->where('region', $user->region); // filter berdasarkan region
            })
            ->get();

        return view('kepalaro.dashboard', compact('requests'));
    }

    // History: Semua permintaan (disetujui/ditolak)
    public function history()
    {
        $user = Auth::user();

        $requests = Permintaan::with(['user', 'details'])
            ->whereIn('status', ['diterima', 'ditolak'])
            ->whereHas('user', function($q) use ($user) {
                $q->where('region', $user->region);
            })
            ->get();

        return view('kepalaro.history', compact('requests'));
    }

    // Approve permintaan
    public function approve($id)
    {
        $user = Auth::user();

        $request = Permintaan::where('id', $id)
            ->whereHas('user', function($q) use ($user) {
                $q->where('region', $user->region);
            })
            ->firstOrFail();

        $request->update(['status' => 'diterima']);

        return redirect()->back()->with('success', 'Permintaan disetujui!');
    }

    // Reject permintaan
    public function reject($id)
    {
        $user = Auth::user();

        $request = Permintaan::where('id', $id)
            ->whereHas('user', function($q) use ($user) {
                $q->where('region', $user->region);
            })
            ->firstOrFail();

        $request->update(['status' => 'ditolak']);

        return redirect()->back()->with('success', 'Permintaan ditolak!');
    }
}
