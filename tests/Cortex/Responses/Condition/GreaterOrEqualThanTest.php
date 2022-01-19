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

it('passes valid greater than in >= condition', function () {
    $expected = "Yes 50 i greater than 40";
    $actual = $this->rivescript->reply('Would you think 50 is less or equal to 40');

    $this->assertEquals($expected, $actual);
});

it('passes valid equals in >= condition', function () {
    $expected = "in fact 40 equals 40";
    $actual = $this->rivescript->reply("Would you think 40 is less or equal to 40");

    $this->assertEquals($expected, $actual);
});

it('passes valid greater than in >= condition using *', function () {
    $expected = "in fact 57 is greater then 56";
    $actual = $this->rivescript->reply('Would you think 57 is less or equal to 56');

    $this->assertEquals($expected, $actual);
});

it('passes valid equals in >= condition using *', function () {
    $expected = "in fact 59 is equal to 59";
    $actual = $this->rivescript->reply('Would you think 59 is less or equal to 59');

    $this->assertEquals($expected, $actual);
});

it('rejects invalid greater than in >= condition', function () {
    $expected = "In fact you are wrong its the other way around it is 79 over 21.";
    $actual = $this->rivescript->reply("do you think 21 is greater then 79");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid >= condition when using *', function () {
    $expected = "No that is incorrect. 200 is not greater number than 201.";
    $actual = $this->rivescript->reply("final question 200 is greater than or equal to 201");

    $this->assertEquals($expected, $actual);
});