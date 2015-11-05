# Fun Tests

Written for working project (that does not belong to any popular frameworks).

Fun Tests is not PHPUnit killer and is not supposed to replace this library. It's just a different tool: smaller (and faster) but also poorer.

It should be easy to start using it: you can either add this library into your project directly (downloading somewhere in namespace of your project) or through composer.


=========================================
Feel free to ask me anything regarding fun tests
=========================================



Direct usage
------------

Will describe it later. It must be pretty easy: clone, set up (that opposite might be not easy )) and use. Sounds easy, but I should notice reality might differ. 


Composer way
------------

Well, now it might be a bit tricky  - just because of my hand must be growing from my bottom.
Anyway you can try.

First of all add this repository to your composer.json:

<pre>
"repositories": [
    {
        "url": "https://github.com/aaleksu/fun_tests.git",
        "type": "git"
    }
]
</pre>

and then add this into "require" block:

<pre>
"forfun/fun_tests": "dev-master"
</pre>

To run tests you should go into directory where you tests are located. (Yeah, that's kind of stupid - I'm working on it.)
