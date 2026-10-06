<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('code') — WebWorksLab</title><meta name="robots" content="noindex, nofollow"><link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%232744ed'/%3E%3Cpath d='m10 18 9 28 13-21 13 21 9-28' fill='none' stroke='white' stroke-width='6'/%3E%3C/svg%3E"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;450;500;550;600;650;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset('assets/style.css') }}?v={{ substr(hash_file('sha256', public_path('assets/style.css')), 0, 12) }}"><link rel="stylesheet" href="{{ asset('assets/studio.css') }}?v={{ substr(hash_file('sha256', public_path('assets/studio.css')), 0, 12) }}"><link rel="stylesheet" href="{{ asset('assets/variations.css') }}?v={{ substr(hash_file('sha256', public_path('assets/variations.css')), 0, 12) }}">
</head>
<body class="variation-c error-page">
    <a class="skip" href="#main">Skip to content</a>
    <header class="header wrap">
        <a class="brand" href="/" aria-label="WebWorksLab home"><img class="custom-brand-logo" src="{{ asset('assets/webworkslab-logo-a.png') }}" alt="WebWorksLab" width="2172" height="724"></a>
        <a class="text-link" href="mailto:info@webworkslab.dev">Get in touch</a>
    </header>
    <main id="main" class="error-main wrap">
        <div class="error-copy">
            <h1>@yield('heading')</h1>
            <p>@yield('description')</p>
            <a class="button primary" href="/">Back to home</a>
        </div>
        <div class="error-code" aria-label="Error @yield('code')">@yield('code')</div>
    </main>
    <footer class="wrap"><span>WebWorksLab</span><span>Software. Built together.</span></footer>
</body>
</html>
