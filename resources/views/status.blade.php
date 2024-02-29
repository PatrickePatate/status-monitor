<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{env('APP_NAME')}}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/scss/page.scss', 'resources/js/page.js'])
</head>
<body>
    <main>
        <div class="alert alert-success">
            🎉 Tous les composants sont fonctionnels !
        </div>
        <div class="services">
            <div class="service">
                <div class="service-core">
                    <p class="service-name">Brocoli - serveur web</p>
                    <div class="badge badge-outline badge-success">
                        <div class="dot dot-success"></div>
                        Opérationnel
                    </div>
                </div>
                <div class="service-description">
                    <p class="service-description">Brocoli est le serveur web de l'infrastructure fruits-et-legumes.ovh. C'est sur brocoli que la plupart des sites web importants sont hebergés.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
