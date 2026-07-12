<x-layouts.app title="Submit Testimonial" description="Share your experience working with Kimmex.">
    <div class="min-h-[60vh] flex items-center justify-center bg-white px-6 py-20">
        <div class="w-full max-w-lg">
            <div class="text-center mb-10">
                <h1 class="text-2xl font-black text-titan-navy uppercase tracking-tight mb-3">Share Your Experience</h1>
                <p class="text-sm text-titan-navy/50 leading-relaxed">
                    We'd love to hear about your experience with the
                    <span class="font-semibold text-titan-navy/70">{{ $project->getTranslation('title', 'en') }}</span> project.
                </p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ URL::signedRoute('testimonials.store', ['project' => $project->slug, 'email' => $email]) }}" class="space-y-6">
                @csrf

                <input type="hidden" name="project_slug" value="{{ $project->slug }}">

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-xs font-bold text-titan-navy uppercase tracking-wide mb-2">Your Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full h-11 px-4 border border-gray-200 rounded-lg text-sm text-titan-navy focus:ring-2 focus:ring-titan-navy/20 focus:border-titan-navy transition-colors"
                        placeholder="John Doe">
                </div>

                {{-- Company --}}
                <div>
                    <label for="company" class="block text-xs font-bold text-titan-navy uppercase tracking-wide mb-2">Company</label>
                    <input type="text" id="company" name="company" value="{{ old('company') }}"
                        class="w-full h-11 px-4 border border-gray-200 rounded-lg text-sm text-titan-navy focus:ring-2 focus:ring-titan-navy/20 focus:border-titan-navy transition-colors"
                        placeholder="Your company name">
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-xs font-bold text-titan-navy uppercase tracking-wide mb-2">Your Role</label>
                    <input type="text" id="role" name="role" value="{{ old('role') }}"
                        class="w-full h-11 px-4 border border-gray-200 rounded-lg text-sm text-titan-navy focus:ring-2 focus:ring-titan-navy/20 focus:border-titan-navy transition-colors"
                        placeholder="CEO, Project Manager, etc.">
                </div>

                {{-- Rating --}}
                <div>
                    <label class="block text-xs font-bold text-titan-navy uppercase tracking-wide mb-2">Rating <span class="text-red-500">*</span></label>
                    <div class="flex gap-2" x-data="{ rating: {{ old('rating', 5) }} }">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                @click="rating = {{ $i }}"
                                :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                class="text-3xl transition-colors hover:text-yellow-400 focus:outline-none">
                                ★
                            </button>
                        @endfor
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                </div>

                {{-- Message --}}
                <div>
                    <label for="message" class="block text-xs font-bold text-titan-navy uppercase tracking-wide mb-2">Your Testimonial <span class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="5" required minlength="10" maxlength="2000"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm text-titan-navy focus:ring-2 focus:ring-titan-navy/20 focus:border-titan-navy transition-colors resize-y"
                        placeholder="Tell us about your experience working with Kimmex...">{{ old('message') }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Minimum 10 characters</p>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                        class="w-full h-12 rounded-lg bg-titan-navy text-white text-xs font-black uppercase tracking-[0.2em] hover:bg-titan-red transition-colors">
                        Submit Testimonial
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
