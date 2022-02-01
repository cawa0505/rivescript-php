<?php
namespace Tests;

use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');

//        $this->rivescript->onSay = function($msg) {
//            echo "{$msg}\n";
//        };
    })
    ->group('debug');


it("Should work", function() {
    $script=<<<EOF
! var globaltest = set test name test

+ test
- {topic=test}{@<get test_name>}

+ test without redirect
- {topic=test}<get test_name>

+ set test name *
- <set test_name=<star>>{@test}

+ get global test
@ <bot globaltest>

+ get bad global test
@ <bot badglobaltest>

> topic test
  + test
  - hello <get test_name>!{topic=random}

  + *
  - {topic=random}<@>
< topic

+ *
- Wildcard "<star>"!
EOF;

    $this->rivescript->stream($script);

    $expected = 'Wildcard "undefined"!';
    $actual = $this->rivescript->reply("test");
    $this->assertEquals($expected, $actual);
//    $this->assertEquals($expected, $actual);
    echo "Response: {$actual}\n";
});