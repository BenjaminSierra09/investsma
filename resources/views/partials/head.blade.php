<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

{!! \Artesaos\SEOTools\Facades\SEOTools::generate() !!}

<link rel="icon" href="/favicon/favicon.ico" sizes="any">
<link rel="icon" href="/favicon/favicon.svg" type="image/svg+xml">
<link rel="icon" href="/favicon/favicon-96x96.png" type="image/png" sizes="96x96">
<link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<script async src="https://www.googletagmanager.com/gtag/js?id=G-B7R99X31XB"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag()
    {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    gtag('config', 'G-B7R99X31XB');
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
