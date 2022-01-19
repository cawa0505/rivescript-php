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

it("Should work with square brackets and multiple items", function () {
    $expected = "Right now that is 16:35";
    $actual = $this->rivescript->reply("what time is pass");
    $this->assertEquals($expected, $actual);

    $expected = "Right now that is 16:35";
    $actual = $this->rivescript->reply("what time will pass");
    $this->assertEquals($expected, $actual);
});

it("Should work with square brackets and one single item", function () {

    $expected = "Why the moon?";
    $actual = $this->rivescript->reply("hello moon");
    $this->assertEquals($expected, $actual);
});


it("Should work if multiple sets of parentheses.", function () {

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

it("Should work if multiple sets of square bracket.", function () {
    $expected = "Do you now?";
    $actual = $this->rivescript->reply("i got set of marbles");
    $this->assertEquals($expected, $actual);

    $expected = "Do you now?";
    $actual = $this->rivescript->reply("i got set of gees");
    $this->assertEquals($expected, $actual);

    $expected = "Do you now?";
    $actual = $this->rivescript->reply("i got group of marbles");
    $this->assertEquals($expected, $actual);

    $expected = "Do you now?";
    $actual = $this->rivescript->reply("i got group of gees");
    $this->assertEquals($expected, $actual);
});


it("Should work if multiple sets are used mixed parentheses and square brackets", function () {
    $expected = "Oh that is not good";
    $actual = $this->rivescript->reply("Got my self a man and i love it");
    $this->assertEquals($expected, $actual);

    $expected = "Oh that is not good";
    $actual = $this->rivescript->reply("Got my self a man and i hate it");
    $this->assertEquals($expected, $actual);

    $expected = "Oh that is not good";
    $actual = $this->rivescript->reply("Got my self a woman and i love it");
    $this->assertEquals($expected, $actual);

    $expected = "Oh that is not good";
    $actual = $this->rivescript->reply("Got my self a woman and i hate it");
    $this->assertEquals($expected, $actual);
});