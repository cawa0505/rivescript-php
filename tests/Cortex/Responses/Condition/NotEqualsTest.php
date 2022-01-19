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
        $this->rivescript->load(__DIR__.'/../../../resources/conditions/conditions.rive');
    })
    ->group('responses');

it('passes valid != condition', function () {
    $expected = "yes that is correct, 4 does not equals 2";
    $actual = $this->rivescript->reply('is it correct that 4 not equals 2');

    $this->assertEquals($expected, $actual);
});

it('passes valid <> condition', function () {
    $expected = "yes that is correct, 6 does not equals 1";
    $actual = $this->rivescript->reply('is it correct that 6 not equals 1');

    $this->assertEquals($expected, $actual);
});

it('passes valid ne alias condition', function () {
    $expected = "yes that is correct, 5 does not equals 2";
    $actual = $this->rivescript->reply('is it correct that 5 not equals 2');

    $this->assertEquals($expected, $actual);
});

it('passes valid not equals using star', function () {
    $expected = "yes that is correct, 9 does not equal 10";
    $actual = $this->rivescript->reply("what do you think 9 not equals 10");

    $this->assertEquals($expected, $actual);
});

it("rejects invalid != condition", function () {
    $expected = "Your statement is incorrect 90 does match 90.";
    $actual = $this->rivescript->reply("is it correct that 90 not equals 90");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid ne alias condition', function () {
    $expected = "Your statement is incorrect 81 does match 81.";
    $actual = $this->rivescript->reply("is it correct that 81 not equals 81");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid <= condition', function () {
    $expected = "Your statement is incorrect 72 does match 72.";
    $actual = $this->rivescript->reply("is it correct that 72 not equals 72");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid <= condition using *', function () {
    $expected = "Your statement is incorrect 102 does match 102.";
    $actual = $this->rivescript->reply("what do you think is 102 not equals 102");

    $this->assertEquals($expected, $actual);
});