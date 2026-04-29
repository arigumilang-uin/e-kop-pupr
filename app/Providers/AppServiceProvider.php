<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gunakan Host bawaan request, karena ini pasti lolos dari Docker
        $host = request()->getHost();

        // Jika URL mengandung trycloudflare.com (atau domain ngrok dll)
        if (str_contains($host, 'trycloudflare.com')) {
            // Paksa Root URL menggunakan HTTPS dan host Cloudflare
            URL::forceRootUrl('https://' . $host);
            
            // Paksa generator link (termasuk Vite) untuk menggunakan HTTPS
            URL::forceScheme('https');
        }
    }
}