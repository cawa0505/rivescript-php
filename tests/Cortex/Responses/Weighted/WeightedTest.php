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
        $this->rivescript->load(__DIR__.'/../../../resources/test.rive');
    })
    ->group('responses');

it('Prioritizes the correct weight', function () {
    $expected = "i am 2 kilo";
    $actual = $this->rivescript->reply('weight test');

    $this->assertEquals($expected, $actual);
});