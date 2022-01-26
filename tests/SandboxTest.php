<?php
namespace Tests;

use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');
    })
    ->group('debug');


it("Should work", function() {
    $script=<<<EOF
        + my name is *
        * <get name> != undefined => <set oldname=<get name>>I thought\s
          ^ your name was <get oldname>?
          ^ <set name=<formal>>
        - <set name=<formal>>OK.

        + what is my name
        - Your name is <get name>, right?

        + html test
        - <set name=<b>Name</b>>This has some non-RS <em>tags</em> in it.
EOF;
    $this->rivescript->stream($script);
    $this->rivescript->setUservar("met", "true", "local-user");
    $this->rivescript->setUservar("name", "undefined", "local-user");
//
//    - input: "What is my name?"
//      reply: "Your name is undefined, right?"
//
//    - input: "My name is Alice."
//      reply: "OK."
//
//    - input: "My name is Bob."
//      reply: "I thought your name was Alice?"
//
//    - input: "What is my name?"
//      reply: "Your name is Bob, right?"
//
//    - input: "HTML Test"
//      reply: "This has some non-RS <em>tags</em> in it."
    $expected = 'Your name is undefined, right?';
    $actual = $this->rivescript->reply("What is my name?");
    $this->assertEquals($expected, $actual);

    $expected = 'OK.';
    $actual = $this->rivescript->reply("My name is Alice.");
    $this->assertEquals($expected, $actual);

    $expected = 'I thought your name was Alice?';
    $actual = $this->rivescript->reply("My name is Bob.");
    $this->assertEquals($expected, $actual);

    $expected = 'Your name is Bob, right?';
    $actual = $this->rivescript->reply("What is my name?");
    $this->assertEquals($expected, $actual);

    $expected = 'This has some non-RS <em>tags</em> in it.';
    $actual = $this->rivescript->reply("HTML Test");
    $this->assertEquals($expected, $actual);
//    $this->assertEquals($expected, $actual);
    echo "Response: {$actual}\n";
});