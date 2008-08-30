<?php

/*
http://fitnesse.org/FitNesse.FitServerProtocol

put this in your wiki pages:
!define COMMAND_PATTERN {php /path/to/phpfit/php-fitnesse.php}

TODO: complete the "Transaction Error in the Protocol spec.
*/

require_once 'PHPFIT/FitServer.php';

$fitserver = new PHPFIT_FitServer(new PHPFIT_Socket());
$out = $fitserver->run($argv);
exit($out);

?>
