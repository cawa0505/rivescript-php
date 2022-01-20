<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/*
|--------------------------------------------------------------------------
| Create The Synapse
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Synapse connection, which
| will act as the "glue" between the core pieces of the Rivescript
| Interpreter.
|
*/

$synapse = new Axiom\Rivescript\Cortex\Synapse();

/*
|--------------------------------------------------------------------------
| Bind Important Variables
|--------------------------------------------------------------------------
|
| Next, we will bind some important variables within the synapse so
| we will be able to resolve them when needed.
|
*/

$synapse->commands = Axiom\Collections\Collection::make(
    [
        "VariablePerson",
        "VariableSubstitute",
        "VariableGlobal",
        "VariableLocal",
        "VariableArray",
        "Variable",
        "Redirect",
        "Response",
        "Topic",
        "Trigger",
    ]
);

$synapse->triggers = Axiom\Collections\Collection::make(
    [
        "Atomic",
        "Wildcard",
        "Arrays",
        "Alternation",
//        "Optional",
    ]
);

/**
 * Within Replies
 * The order that the tags should be processed within a response or anywhere else that a tag is allowed is as follows:
 *
 * <star>      # Static text macros
 * <botstar>/<botstarN> # will match any wildcards that matched the bot's previous response.
 * <input>     #
 * <reply>     #
 * <id>        #
 * \s          #
 * \n          #
 * \\          #
 * \#          #
 * {random}    # Random text insertion (which may contain other tags)
 * <bot>       # Insert bot variables
 * <env>       # Insert environment variables
 * <person>    # String modifiers
 * <formal>    #
 * <sentence>  #
 * <uppercase> #
 * <lowercase> #
 * <set>       # User variable modifiers
 * <add>       #
 * <sub>       #
 * <mult>      #
 * <div>       #
 * <get>       # Get user variables
 * {topic}     # Set user topic
 * <@>         # Inline redirection
 * (@array)    # Arrays
 */
$synapse->tags = Axiom\Collections\Collection::make([
    "Star",
    "BotStar",
    "Input",
    "Reply",
    "Id",
    "SpecialChars",
    "Random",
    "Bot",
    "Env",
    "Person",
    "Formal",
    "Sentence",
    "Uppercase",
    "Lowercase",
    "Set",
    "Add",
    "Sub",
    "Mult",
    "Div",
    "Get",
    "Topic",
    "InlineRedirect",
    "ArrayTag",
]);

$synapse->responses = Axiom\Collections\Collection::make(
    [
        "Weighted",
        "ContinueResponse",
        "Atomic",
        "Condition",
        "Previous",
    ]
);

$synapse->conditions = Axiom\Collections\Collection::make(
    [
        "Equals",
        "NotEquals",
        "LessThan",
        "LessOrEqualTo",
        "GreaterThan",
        "GreaterThanOrEqual",
    ]
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Finally, we will bind some important interfaces within the synapse so
| we will be able to resolve them when needed as well.
|
*/

$synapse->memory = new Axiom\Rivescript\Cortex\Memory();
$synapse->brain = new Axiom\Rivescript\Cortex\Brain();

/*
|--------------------------------------------------------------------------
| Autoload Additional Files
|--------------------------------------------------------------------------
|
| Now we will autoload some files to aid in using the Rivescript
| interpreter.
|
*/

include 'helpers.php';
