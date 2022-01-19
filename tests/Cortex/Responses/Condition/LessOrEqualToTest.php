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

it('passes valid less in <= condition', function () {
    $expected = "In fact 2 equals 2";
    $actual = $this->rivescript->reply("Would you think 2 is less or equal to 2");

    $this->assertEquals($expected, $actual);
});

it('passes valid equal than in <= condition', function () {
    $expected = "In fact 1 is smaller than 2.";
    $actual = $this->rivescript->reply("Would you think 1 is less or equal to 2");

    $this->assertEquals($expected, $actual);
});

it('passes valid equal in <= condition using *', function () {
    $expected = "Yes that is correct. In fact 34 is equals 34.";
    $actual = $this->rivescript->reply("Would you think 34 is less or equal than 34");

    $this->assertEquals($expected, $actual);
});

it('passes valid less than in <= condition using *', function () {
    $expected = "Yes that is correct. 29 is less than 34.";
    $actual = $this->rivescript->reply('Would you think 29 is less than 34');

    $this->assertEquals($expected, $actual);
});

it('rejects invalid equal in <= condition', function () {
    $expected = "No that is incorrect. 16 is the greater number.";
    $actual = $this->rivescript->reply('Do you think 16 is less than 15');

    $this->assertEquals($expected, $actual);
});

it('rejects invalid less than <= condition', function () {
    $expected = "No that is incorrect. 20 is the greater number.";
    $actual = $this->rivescript->reply('Do you think 20 is less than 19');

    $this->assertEquals($expected, $actual);
});