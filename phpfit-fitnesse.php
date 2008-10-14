<?php

/*
http://fitnesse.org/FitNesse.FitServerProtocol
https://fitnesse.svn.sourceforge.net/svnroot/fitnesse/trunk/srcFitServerTests

put this in your wiki pages:
!define COMMAND_PATTERN {php /path/to/phpfit/php-fitnesse.php}
If you use a custom fixture path, put this in your wiki pages:
!define COMMAND_PATTERN {php /path/to/phpfit/php-fitnesse.php /your/fixture/path}

TODO: complete the "Transaction Error in the Protocol spec.
*/

set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
require_once 'PHPFIT/FitServer.php';

$fitserver = new PHPFIT_FitServer(new PHPFIT_Socket());
$out = $fitserver->run($argv);
exit($out);

