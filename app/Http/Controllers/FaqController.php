<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::active()
            ->orderBy('sort_order')
            ->get(['id', 'question', 'answer']);

        return Inertia::render('Faq', ['faqs' => $faqs]);
    }
}
