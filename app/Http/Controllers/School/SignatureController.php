<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Signature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SignatureController extends Controller
{
    public function index(): View
    {
        $signatures = Signature::latest()->get();

        return view('school.signatures.index', compact('signatures'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('signatures', 'public');
        }

        unset($data['image']);

        Signature::create($data);

        return redirect()->route('school.signatures.index')->with('status', 'Signature created.');
    }

    public function uploadImage(Request $request, Signature $signature): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        if ($signature->image_path) {
            Storage::disk('public')->delete($signature->image_path);
        }

        $signature->update([
            'image_path' => $request->file('image')->store('signatures', 'public'),
        ]);

        return redirect()->route('school.signatures.index')->with('status', 'Signature image updated.');
    }

    public function destroy(Signature $signature): RedirectResponse
    {
        if ($signature->image_path) {
            Storage::disk('public')->delete($signature->image_path);
        }

        $signature->delete();

        return redirect()->route('school.signatures.index')->with('status', 'Signature deleted.');
    }
}
