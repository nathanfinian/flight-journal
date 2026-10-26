<?php

use App\Exports\MonthlyGseRecapExport;

test('GSE export calculates elapsed seconds across midnight', function ($start, $finish, $expected) {
    expect(MonthlyGseRecapExport::durationSeconds($start, $finish))->toBe($expected);
})->with([
    ['10:15', '11:45', 5400],
    ['23.00', '1.00', 7200],
    ['23:45:00', '00:15:00', 1800],
    ['00:00', '01:00', 3600],
    ['12:00', '12:00', 0],
    ['23:59:30', '00:00:30', 60],
    [null, '01:00', null],
    ['23:00', '', null],
    ['invalid', '01:00', null],
    ['25:00', '01:00', null],
    ['12:65', '13:00', null],
]);
