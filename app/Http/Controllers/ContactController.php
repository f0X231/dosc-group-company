<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        $data['ip_address'] = $request->ip();

        Contact::create($data);

        return back()->with('success', 'ส่งข้อความสำเร็จ เราจะติดต่อกลับโดยเร็วที่สุด');
    }
}
