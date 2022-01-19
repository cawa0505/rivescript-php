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


it("Should work with parentheses and multiple items", function () {

    $expected = "I am a robot.";
    $actual = $this->rivescript->reply("what are you");
    $this->assertEquals($expected, $actual);

    $expected = "I am a robot.";
    $actual = $this->rivescript->reply("what is you");
    $this->assertEquals($expected, $actual);
});

it("Should work with parentheses and one single item", function () {

    $expected = "How about 109.";
    $actual = $this->rivescript->reply("Give me a single digit");
    $this->assertEquals($expected, $actual);
});

it("Should work with multiple sets.", function () {

    $expected = "Yes i do.";
    $actual = $this->rivescript->reply("do you have one beer");
    $this->assertEquals($expected, $actual);

    $expected = "Yes i do.";
    $actual = $this->rivescript->reply("do you have one unicorn");
    $this->assertEquals($expected, $actual);

    $expected = "Yes i do.";
    $actual = $this->rivescript->reply("do you have multiple beer");
    $this->assertEquals($expected, $actual);

    $expected = "Yes i do.";
    $actual = $this->rivescript->reply("do you have multiple unicorn");
    $this->assertEquals($expected, $actual);
});

