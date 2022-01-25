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

> begin
    + request
    * <get name> eq undefined => {topic=newuser}{ok}
    * <get name> ne undefined => Hello <get name>{ok}
    - {ok}
< begin

> topic newuser
    + my name is *
    - <set name=<star>>Nice to meet you <star>!{topic=random}

    + *
    - before we start I need your username <get name>
< topic

+ go
- Hi {topic=newuser}

+ hello bot
- Hello human.
EOF;
    $this->rivescript->stream($script);

    $expected = "Hi";
    $actual = $this->rivescript->reply("go");

    $this->assertEquals($expected, $actual);
    echo "Response: {$actual}\n";
});