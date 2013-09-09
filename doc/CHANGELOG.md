in https://github.com/metaclass-nl/phpfit

- added gitignore for eclipse project files and folders

- adapted to PHP 5.3.0
	PHPFIT/Parse.php 
	    line 238-241 ereg_replace replaced by str_replace
	    line 273 ereg_replace replaced by str_replace
	eg/music/Music.php line 30 Replaced call to depricated function split by explode
- Bug fixed: Fatal error if adapter is null
    PHPFIT/Fixture.php
	    line 326-333 moved if adapter == null to top so that cell is allways ignored if no adapter
- Bug fixed: On binding column, in case of an exception no key was made for the column index, 
  causing PHPFIT_Fixture_Row::checkList to use the adapter of the next column.
  	PHPFIT/Fixture/Column.php PHPFIT_Fixture_Column ::bind
	    added line 137:   $this->columnBindings[$i] = null;

- added composer.json
- added tests/AllTest110.php for testing using SimpleTest 1.1.0 
	(not included, included version of SimpleTest did not run on PHP 5.3)
- addded examples/output.html to .gitignore
- renamed doc/CHANGELOG to doc/CHANGELOG.md and added these descriptions 
- README.md added reference to fit-skeleton package for install with composer and Fit Shelf  


these modifications are Copyright (c) 2010-2012 MetaClass Groningen Nederland
and Licensed under the GNU General Public License version 3 or later.

these modifications are free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

  IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING
WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MODIFIES AND/OR CONVEYS
THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY
GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE
USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF
DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD
PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS),
EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF
SUCH DAMAGES.

v0.7:
-----

- FitServer separated from the running script (phpfit-fitnesse.php)
- PEAR-style classnames allowed for custom fixtures

notes: Thanks Gregor Gramlich for reporting the bugs


v0.6:
-----

- Now supports adding a custom fixture path when using fitnesse (see README)
- Fix some minor bugs on windows


v0.55:
------

- Strict (===) comparison in type adapters
- PHPFIT::run() now returns a string with the results instead of printing them to STDERR.
- PHPFIT::run() now output a more informative error in case a parse exception arise.
- TypeDict now case insensitive and the README file list the possibilities.
- "Surplus" and "Missing" rows added in the RowFixture
