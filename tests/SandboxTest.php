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
            * <get met> == undefined => <set met=true>{ok}
            * <get name> != undefined => <get name>: {ok}
            - {ok}
        < begin
        + hello bot
        - Hello human.

        + my name is *
        - <set name=<formal>>Hello, <get name>.
EOF;
    $this->rivescript->stream($script);
    $this->rivescript->setUservar("met", "true", "local-user");
    $this->rivescript->setUservar("name", "undefined", "local-user");

//    $expected = "Hello, Bob.";
//    $actual = $this->rivescript->reply("My name is bob");

    $expected = "Bob: Hello human.";
    $actual = $this->rivescript->reply("Hello bot");

    $this->assertEquals($expected, $actual);
    echo "Response: {$actual}\n";
});