<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:about', function () {
    $this->comment('ParkManager Lite listo.');
});
