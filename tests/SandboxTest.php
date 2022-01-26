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
        + google *
        - <a href="https://www.google.com/search?q=<star>">Results are here</a>
EOF;
    $this->rivescript->stream($script);
    $this->rivescript->setUservar("met", "true", "local-user");
    $this->rivescript->setUservar("name", "undefined", "local-user");

    $expected = '<a href="https://www.google.com/search?q=<star>">Results are here</a>';
    $actual = $this->rivescript->reply("google test");


    $this->assertEquals($expected, $actual);
    echo "Response: {$actual}\n";
});