<?php

if (!function_exists('auto_asset')) {
    function auto_asset($path) {
        return app()->environment('local') ? asset($path) : secure_asset($path);
    }
}
