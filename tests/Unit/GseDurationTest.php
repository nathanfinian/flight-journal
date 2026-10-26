<?php

use App\Support\GseDuration;

test('GSE billing rounds elapsed usage up to whole hours with a one hour minimum', function ($start, $finish, $expected) {
    expect(GseDuration::billableHours($start, $finish))->toBe($expected);
})->with([
    ['23:00', '01:00', 2.0],
    ['23.00', '1.01', 3.0],
    ['23:59:30', '01:00:31', 2.0],
    ['10:00', '10:01', 1.0],
    ['10:00', '11:00', 1.0],
    ['10:00', '11:01', 2.0],
    ['00:00', '00:00', 1.0],
    [null, '01:00', 1.0],
    ['invalid', '01:00', 1.0],
]);
