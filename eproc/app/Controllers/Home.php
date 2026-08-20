<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(string $path = '')
    {
        // Load Vite manifest to get hashed asset filenames
        $manifestPath = FCPATH . 'build/.vite/manifest.json';
        $manifest = [];

        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        $jsFile = null;
        $cssFile = null;
        $isDev = (ENVIRONMENT === 'development');

        if ($isDev) {
            // Use Vite dev server in development
            // Use 127.0.0.1 instead of localhost to bypass IPv6/wslrelay conflicts on Windows
            $jsFile = 'http://127.0.0.1:5173/resources/js/main.jsx';
            $cssFile = null; // CSS is injected by Vite client
        } else {
            // Get the main JS entry from manifest in production
            if (!empty($manifest)) {
                $entry = $manifest['resources/js/main.jsx'] ?? $manifest['main.jsx'] ?? null;
                if ($entry) {
                    $jsFile = '/build/' . $entry['file'];
                    if (!empty($entry['css'])) {
                        $cssFile = '/build/' . $entry['css'][0];
                    }
                }
            }
        }

        $data = [
            'jsFile'  => $jsFile,
            'cssFile' => $cssFile,
            'isDev'   => $isDev,
        ];

        return view('app', $data);
    }
}
