<?php

namespace KentarouTakeda\SafeRouting;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class SafeRoutingServiceProvider extends ServiceProvider
{
    public function map(SafeRouting $safeRouting)
    {
        $glob = base_path('routes/*.yml');
        $files = File::glob($glob);
        if($files === false) {
            abort(500);
        }
        foreach($files as $file) {
            $array = Yaml::parseFile($file);
            $safeRouting->makeRoute($array);
        }
    }
}
