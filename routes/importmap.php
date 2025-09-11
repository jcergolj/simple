<?php

use Tonysm\ImportmapLaravel\Facades\Importmap;

Importmap::pinAllFrom('resources/js', to: 'js/');
Importmap::pin('@hotwired/turbo', to: '/js/vendor/@hotwired--turbo.js'); // @hotwired/turbo@8.0.13 downloaded from https://ga.jspm.io/npm:@hotwired/turbo@8.0.13/dist/turbo.es2017-esm.js
Importmap::pin('@hotwired/stimulus', to: '/js/vendor/@hotwired--stimulus.js'); // @hotwired/stimulus@3.2.2 downloaded from https://ga.jspm.io/npm:@hotwired/stimulus@3.2.2/dist/stimulus.js
Importmap::pin('@hotwired/hotwire-native-bridge', to: '/js/vendor/@hotwired--hotwire-native-bridge.js'); // @hotwired/hotwire-native-bridge@1.2.2 downloaded from https://ga.jspm.io/npm:@hotwired/hotwire-native-bridge@1.2.2/dist/hotwire-native-bridge.js
Importmap::pin('@hotwired/stimulus-loading', to: 'vendor/stimulus-laravel/stimulus-loading.js', preload: true);
Importmap::pin('@hotwired/stimulus-loading', to: 'vendor/stimulus-laravel/stimulus-loading.js', preload: true);
Importmap::pin('@hotwired/stimulus-loading', to: 'vendor/stimulus-laravel/stimulus-loading.js', preload: true);
