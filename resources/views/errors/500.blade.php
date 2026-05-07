<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ in_array(app()->getLocale(), ['kh', 'km']) ? 'កំហុសប្រព័ន្ធ' : 'System Error' }} | KIMMEX</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Kantumruy+Pro:wght@400;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'titan-navy': '#0B2B5C',
                        'titan-red': '#D4A017',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'Kantumruy Pro', 'sans-serif'],
                        'khmer': ['Kantumruy Pro', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-15px) rotate(0.5deg); }
        }
        .animate-float {
            animation: float 8s ease-in-out infinite;
        }
        .khmer-text { font-family: 'Kantumruy Pro', sans-serif; }
    </style>
</head>
<body class="bg-white text-titan-navy font-sans min-h-screen flex items-center justify-center p-6 overflow-hidden">
    
    <!-- Decorative Background -->
    <div class="fixed inset-0 -z-10">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-orange-50/30"></div>
        <div class="absolute top-[20%] right-[10%] w-[40%] h-[40%] bg-orange-100/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-xl w-full text-center relative">
        <!-- SVG Illustration -->
        <div class="relative inline-block mb-12 animate-float">
            <div class="relative w-48 h-48 md:w-64 md:h-64 mx-auto flex items-center justify-center">
                <svg class="w-full h-full text-orange-500/10" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zM7 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-8xl md:text-9xl font-black tracking-tighter opacity-10 text-orange-600">500</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-6 relative z-10 {{ in_array(app()->getLocale(), ['kh', 'km']) ? 'khmer-text' : '' }}">
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-titan-navy uppercase leading-tight">
                {{ in_array(app()->getLocale(), ['kh', 'km']) ? 'មានបញ្ហាបច្ចេកទេស' : 'Something Went Wrong' }}
            </h1>
            
            <p class="text-gray-500 text-lg md:text-xl font-medium leading-relaxed max-w-md mx-auto">
                {{ in_array(app()->getLocale(), ['kh', 'km']) 
                    ? 'ម៉ាស៊ីនមេរបស់យើងកំពុងជួបបញ្ហាបច្ចេកទេសបន្តិចបន្តួច។ យើងកំពុងដោះស្រាយវាឱ្យបានឆាប់តាមដែលអាចធ្វើទៅបាន។' 
                    : 'Our server is having a brief technical difficulty. We\'ve been notified and are looking into it.' 
                }}
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
                <button onclick="window.location.reload()" 
                   class="w-full sm:w-auto px-10 py-4 bg-orange-600 text-white font-bold rounded-2xl hover:bg-orange-700 transition-all hover:scale-105 active:scale-95 shadow-2xl shadow-orange-600/20 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ in_array(app()->getLocale(), ['kh', 'km']) ? 'ព្យាយាមម្តងទៀត' : 'Try Again' }}
                </button>
                
                <a href="{{ url('/') }}" 
                   class="w-full sm:w-auto px-10 py-4 bg-white text-titan-navy border-2 border-gray-100 font-bold rounded-2xl hover:border-titan-navy transition-all shadow-sm flex items-center justify-center gap-2">
                    {{ in_array(app()->getLocale(), ['kh', 'km']) ? 'ទំព័រដើម' : 'Home Page' }}
                </a>
            </div>
        </div>

        <!-- Footer Note -->
        <p class="mt-20 text-gray-400 text-sm font-medium uppercase tracking-widest opacity-50">
            KIMMEX TECHNICAL SUPPORT
        </p>
    </div>

</body>
</html>
