<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Cortex\Responses;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../../resources/conditions/conditions.rive');
    })
    ->group('responses');


it('passes valid == condition', function () {
    $expected = "yes that is correct, 1 equals 1";
    $actual = $this->rivescript->reply('do you think 1 equals 1');

    $this->assertEquals($expected, $actual);
});

it('passes valid eq alias', function () {
    $expected = "yes that is correct, 2 equals 2";
    $actual = $this->rivescript->reply("do you think 2 equals 2");

    $this->assertEquals($expected, $actual);
});

it('passes valid condition using *', function () {
    $expected = "yes that is correct, 4 equals 4";
    $actual = $this->rivescript->reply("do you think 4 equals 4");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid == statement', function () {
    $expected = "No this is not correct";
    $actual = $this->rivescript->reply("do you think 4 equals 2");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid eq alias statement', function () {
    $expected = "No this is not correct. 5 equals 5 not 3";
    $actual = $this->rivescript->reply("do you think 5 equals 3");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid statement while using *', function () {
    $expected = "No that is the incorrect answer, my name is Beta";
    $actual = $this->rivescript->reply("is your name Hal");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid statement while using bot variable', function () {
    $expected = "No that is the incorrect answer, my name is Beta";
    $actual = $this->rivescript->reply("is your name Hal");

    $this->assertEquals($expected, $actual);
});