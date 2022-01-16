<?php

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');
    })
    ->group('miscellaneous');


it("Should be able to set and get bot variables", function () {

    $script =<<< EOF
! var name = Aiden
! var age = 5

+ what is your name
- My name is <bot name>.

+ how old are you
- I am <bot age>.

+ what are you
- I'm <bot gender>.

+ happy birthday
- <bot age=6>Thanks!
EOF;

   $this->rivescript->stream($script);
   $x = $this->rivescript->reply("happy birthday");

   $expected = "I am 6.";
   $actual = $this->rivescript->reply("How old are you?");

   $this->assertEquals($expected, $actual);
});
