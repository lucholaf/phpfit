PHPFIT version 0.1
==================

PHPFIT is a PHP5 port of the FIT acceptance test framework.
FIT was originally developed for Java by Ward Cunningham.

SYNOPSYS

1) The PHPFIT installation directory must be in the PHP include path directory. 

2) Check the file 'phpfit' and change the first line according to your PHP path installation.

3) Create a symbolic link to 'phpfit' or put it in the path.

4) Run it:

4a) From the CLI:

phpfit path/to/input.html path/to/output.html [path/to/fixtures]

NOTE: [path/to/fixtures] is optional, by default it will check for fixtures in the include path and also relative to where you run 'phpfit'.

e.g: phpfit examples/input/arithmetic.html output.html

4b) From a Browser:

e.g: http://domain/path/to/phpfit/examples/run-web.php?input_filename=input/arithmetic.html

4c) From your own scripts:

<?php
require_once 'PHPFIT.php';

PHPFIT::run(input.html, output.html, [fixturesDirectory]);

echo file_get_contents(output.html);
?>


TODO

- PHPFIT::run() a way to specify the output. eg: to a file, to the screen, etc.
- Achieve more tests coverage.
- It doesn't pass the FIT specification tests yet.


AUTHOR

Luis Floreani <luis.floreani@gmail.com>


COPYRIGHT AND LICENCE

Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
Released under the terms of the GNU General Public License version 2 or later.
