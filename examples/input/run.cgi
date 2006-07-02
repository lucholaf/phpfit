#!/bin/sh
#(c) 2002, Ward Cunningham

echo "Content-type: text/html"
echo 

(cd Release
export TERM=vt100
export TMPDIR=/tmp

# real work happens in next two lines
lynx -source $HTTP_REFERER >Documents/$$
php ../../lib/fit/FileRunner.php  Documents/$$ Reports/$$

cat Reports/$$
rm Documents/$$ Reports/$$) 2>run.log
