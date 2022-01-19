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

it('passes valid > condition', function() {
    $expected = "Yes that is correct";
    $actual = $this->rivescript->reply('Do you think 10 is greater then 5');

    $this->assertEquals($expected, $actual);
});

it('passes valid > condition when using *', function() {
    $expected = "Yes that is correct";
    $actual = $this->rivescript->reply('Do you think 11 is greater then 10');

    $this->assertEquals($expected, $actual);
});

it('rejects invalid > condition', function() {
    $expected = "No that is incorrect. 15 is the greater number.";
    $actual = $this->rivescript->reply("Do you think 4 is greater then 15");

    $this->assertEquals($expected, $actual);
});

it('rejects invalid > condition when using *', function() {
    $expected = "No that is incorrect.";
    $actual = $this->rivescript->reply('Do you think 2 is greater then 10');

    $this->assertEquals($expected, $actual);
});