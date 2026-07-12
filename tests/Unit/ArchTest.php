<?php

declare(strict_types=1);

arch('should strict')
    ->expect('App')
    ->toUseStrictTypes();

arch()
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend('Illuminate\Database\Eloquent\Model');

arch()
    ->expect('App\Http')
    ->toOnlyBeUsedIn('App\Http');

arch()
    ->preset()
    ->php();

arch()
    ->preset()
    ->security();

arch('no debugging statements are left in the application code')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();

arch('controllers are suffixed correctly')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

arch('form requests are well-formed')
    ->expect('App\Http\Requests')
    ->toHaveSuffix('Request')
    ->toExtend('Illuminate\Foundation\Http\FormRequest');

arch('service providers are well-formed')
    ->expect('App\Providers')
    ->toHaveSuffix('Provider')
    ->toExtend('Illuminate\Support\ServiceProvider');

arch('concerns are traits')
    ->expect('App\Concerns')
    ->toBeTraits();
