<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Cortex\Response;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__ . '/../../../resources/test.rive');
    })
    ->group('responses');

it('appends additional continue responses', function () {
    $expected = sprintf("Little Miss Muffit sat on her tuffet\nin a nonchalant sort of way.\nWith her forcefield around her,\nthe Spider, the bounder,\nIs not in the picture today.");

    $actual = $this->rivescript->reply('tell me a poem');
    $this->assertEquals($expected, $actual);
});