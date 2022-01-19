<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Cortex\Triggers;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__ . '/../../../resources/test.rive');
    })
    ->group('triggers');


it("Should work with one single item", function () {

    $expected = "One optional response";
    $actual = $this->rivescript->reply("One optional test");
    $this->assertEquals($expected, $actual);
});

it("Should work with multiple items", function () {

    $expected = "Two optional response";
    $actual = $this->rivescript->reply("Two optional test");
    $this->assertEquals($expected, $actual);

    $expected = "Two optional response";
    $actual = $this->rivescript->reply("Two opt test");
    $this->assertEquals($expected, $actual);
});

it("Should work with multiple sets", function () {

    $expected = "Are they not cute?";
    $actual = $this->rivescript->reply("Two sets special sets of sheep");
    $this->assertEquals($expected, $actual);

    $expected = "Are they not cute?";
    $actual = $this->rivescript->reply("Two sets special sets of shuffles");
    $this->assertEquals($expected, $actual);
});
