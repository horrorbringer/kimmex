<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function showSubmitForm(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This link is invalid or has expired.');
        }

        $project = Project::where('slug', $request->query('project'))->firstOrFail();

        return view('pages.testimonials.submit', [
            'project' => $project,
            'email' => $request->query('email'),
        ]);
    }

    public function store(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This link is invalid or has expired.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'project_slug' => ['required', 'string', 'exists:projects,slug'],
        ]);

        $project = Project::where('slug', $validated['project_slug'])->firstOrFail();

        Testimonial::create([
            'clientName' => $validated['name'],
            'clientRole' => $validated['role'] ?? null,
            'company' => $validated['company'] ?? null,
            'content' => $validated['message'],
            'rating' => $validated['rating'],
            'isFeatured' => false,
            'isActive' => false,
            'orderIndex' => 0,
        ]);

        return view('pages.testimonials.thank-you', [
            'project' => $project,
        ]);
    }
}
