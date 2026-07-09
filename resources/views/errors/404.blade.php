<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ in_array(app()->getLocale(), ['kh', 'km']) ? 'រកមិនឃើញទំព័រ' : 'Page Not Found' }} | KIMMEX</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Droid+Serif:wght@400;700&family=Suwannaphum:wght@100;300;400;700;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kmd-navy': '#0B2B5C',
                        'kmd-gold': '#D4A017',
                        'kmd-bg-alt': '#F0EFE9',
                        // Backward compatibility
                        'titan-navy': '#0B2B5C',
                        'titan-red': '#D4A017',
                    },
                    fontFamily: {
                        'sans': ['Droid Serif', 'Suwannaphum', 'serif'],
                        'khmer': ['Suwannaphum', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .khmer-text { font-family: 'Suwannaphum', sans-serif; }
    </style>
</head>
<body class="bg-kmd-bg-alt text-kmd-navy font-sans min-h-screen flex items-center justify-center p-6 overflow-hidden">
    
    <!-- Decorative Background -->
    <div class="fixed inset-0 -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-kmd-gold/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-kmd-navy/5 rounded-full blur-[150px]"></div>
    </div>

    <div class="max-w-xl w-full text-center relative">
        <!-- SVG Illustration -->
        <div class="relative inline-block mb-12 animate-float">
            <div class="relative w-48 h-48 md:w-64 md:h-64 mx-auto flex items-center justify-center">
                <svg class="w-full h-full text-kmd-navy/5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-8xl md:text-9xl font-black tracking-tighter opacity-10">404</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-6 relative z-10 {{ in_array(app()->getLocale(), ['kh', 'km']) ? 'khmer-text' : '' }}">
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-kmd-navy uppercase leading-tight">
                {{ in_array(app()->getLocale(), ['kh', 'km']) ? 'រកមិនឃើញទំព័រ' : 'Page Not Found' }}
            </h1>
            
            <p class="text-gray-500 text-lg md:text-xl font-medium leading-relaxed max-w-md mx-auto">
                {{ in_array(app()->getLocale(), ['kh', 'km']) 
                    ? 'ទំព័រដែលអ្នកកំពុងស្វែងរកត្រូវបានផ្លាស់ប្តូរ ឬមិនមានក្នុងប្រព័ន្ធរបស់យើងឡើយ។' 
                    : 'The page you are looking for has been moved or doesn\'t exist in our system.' 
                }}
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
                <a href="{{ url('/') }}" 
                   class="w-full sm:w-auto px-10 py-4 bg-kmd-navy text-white font-bold rounded-2xl hover:bg-black transition-all hover:scale-105 active:scale-95 shadow-2xl shadow-kmd-navy/20 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ in_array(app()->getLocale(), ['kh', 'km']) ? 'ត្រឡប់ទៅទំព័រដើម' : 'Take Me Home' }}
                </a>
                
                <button onclick="window.history.back()" 
                   class="w-full sm:w-auto px-10 py-4 bg-white text-kmd-navy border-2 border-gray-100 font-bold rounded-2xl hover:border-kmd-gold transition-all shadow-sm flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ in_array(app()->getLocale(), ['kh', 'km']) ? 'ត្រឡប់ក្រោយ' : 'Go Back' }}
                </button>
            </div>
        </div>

        <p class="mt-20 text-gray-400 text-sm font-medium uppercase tracking-widest opacity-50">
            &copy; {{ date('Y') }} KIMMEX CO., LTD.
        </p>
    </div>

</body>
</html>
