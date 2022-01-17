<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');
    })
    ->group('tags');


it("An array variable should return a random value.", function () {

    $script = <<< EOF
! array greek = alpha beta gamma
! array test = testing trying
! array format = <uppercase>|<lowercase>|<formal>|<sentence>

+ test random array
- Testing (@greek) array.

+ test two random arrays
- {formal}(@test){/formal} another (@greek) array.

+ test nonexistant array
- This (@array) does not exist.

+ test more arrays
- I'm (@test) more (@greek) (@arrays).

+ test weird syntax
- This (@ greek) shouldn't work, and neither should this @test.

+ random format *
- (@format)
EOF;

    $possibleResults = [
        'Testing alpha array.',
        'Testing beta array.',
        'Testing gamma array.'
    ];

    $this->rivescript->stream($script);

    $reply = $this->rivescript->reply("test random array");

    $value = in_array($reply, $possibleResults);

    $this->assertTrue($value);
});
