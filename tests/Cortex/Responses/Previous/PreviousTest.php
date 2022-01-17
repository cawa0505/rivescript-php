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


it("Should be able to remember previous replies.", function () {

    $script =<<<EOF
! array colors = red green blue cyan magenta yellow black white orange brown

  + i have a dog
  - What color is it?

  + (@colors)
  % what color is it
  - That's an odd color for a dog.
EOF;

//    $this->rivescript->stream($script);
//
//
//    $expected = "What color is it?";
//    $actual = $this->rivescript->reply("i have a dog");
//
//    $this->assertEquals($expected, $actual);
//
//    $expected = "red";
//    $actual = $this->rivescript->reply("That's an odd color for a dog.");
//
//    $this->assertEquals($expected, $actual);
});