<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<meta name="description" content="Supercharge Your Team’s Productivity">

<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ config('app.name') }}">
<meta property="og:description" content="Supercharge Your Team’s Productivity">
<meta property="og:image" content="{{ url('/opengraph.png') }}">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:type" content="website">

<meta name="twitter:title" content="{{ config('app.name') }}">
<meta name="twitter:description" content="Supercharge Your Team’s Productivity">
<meta name="twitter:image" content="{{ url('/opengraph.png') }}">
<meta name="twitter:image:alt" content="{{ config('app.name') }}">
<meta name="twitter:card" content="summary_large_image">

<link rel="icon" href="/favicon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
