<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::with(['createdBy:id,name', 'updatedBy:id,name'])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Faq/Index', ['faqs' => $faqs]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'status'     => 'required|in:active,inactive',
            'sort_order' => 'required|integer|min:0',
        ]);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Faq::create($data);

        return back();
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'status'     => 'required|in:active,inactive',
            'sort_order' => 'required|integer|min:0',
        ]);

        $data['updated_by'] = Auth::id();

        $faq->update($data);

        return back();
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back();
    }

    public function toggleStatus(Faq $faq)
    {
        $faq->update([
            'status'     => $faq->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id(),
        ]);

        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items'            => 'required|array',
            'items.*.id'       => 'required|exists:faqs,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Faq::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'updated_by' => Auth::id(),
            ]);
        }

        return back();
    }
}
