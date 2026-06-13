<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['updatedBy:id,name'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contacts = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Contacts/Index', [
            'contacts'   => $contacts,
            'filters'    => $request->only('status'),
            'statusCounts' => [
                'all'     => Contact::count(),
                'unread'  => Contact::where('status', 'unread')->count(),
                'read'    => Contact::where('status', 'read')->count(),
                'replied' => Contact::where('status', 'replied')->count(),
            ],
        ]);
    }

    public function show(Contact $contact)
    {
        // Auto-mark as read when opened
        if ($contact->status === 'unread') {
            $contact->update([
                'status'     => 'read',
                'updated_by' => Auth::id(),
            ]);
        }

        return Inertia::render('Admin/Contacts/Show', [
            'contact' => $contact->load(['updatedBy:id,name']),
        ]);
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:unread,read,replied',
        ]);

        $contact->update([
            'status'     => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return back();
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return back();
    }
}
