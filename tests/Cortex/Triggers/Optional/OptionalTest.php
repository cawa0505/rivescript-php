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

    $expected = "They are +316123456789";
    $actual = $this->rivescript->reply("What are your phone digits");
    $this->assertEquals($expected, $actual);

    $actual = $this->rivescript->reply("What are your digits");
    $this->assertEquals($expected, $actual);
});

it("Should work with multiple optional items", function () {

    $expected = "Yes it does actually";
    $actual = $this->rivescript->reply("Does your home have a movie room");
    $this->assertEquals($expected, $actual);

    $actual = $this->rivescript->reply("Does your home have a recreation room");
    $this->assertEquals($expected, $actual);

    $actual = $this->rivescript->reply("Does your home have a room");
    $this->assertEquals($expected, $actual);
});

it("Should work with multiple optional item sets", function () {

    $expected = "Why is the sky blue";
    $actual = $this->rivescript->reply("please ask me a question about something anything");
    $this->assertEquals($expected, $actual);

    $actual = $this->rivescript->reply("please ask me a question about anything");
    $this->assertEquals($expected, $actual);

    $actual = $this->rivescript->reply("can you ask me a question about something anything");
    $this->assertEquals($expected, $actual);

    $actual = $this->rivescript->reply("can you ask me a question about anything");
    $this->assertEquals($expected, $actual);

    $actual = $this->rivescript->reply("ask me a question about something anything");
    $this->assertEquals($expected, $actual);

    $actual = $this->rivescript->reply("ask me a question about anything");
    $this->assertEquals($expected, $actual);
});