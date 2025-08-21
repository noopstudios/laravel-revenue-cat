<?php

test('package class exists', function () {
    expect(class_exists('NoopStudios\LaravelRevenueCat\LaravelRevenueCatServiceProvider'))->toBeTrue();
});
arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
