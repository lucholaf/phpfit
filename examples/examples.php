<?php

$examplesDir = "input";
$files = scandir($examplesDir);

?>

<html>
<title>PHPFIT Examples</title>
<body>
<table width="100%">
	<tr><td bgcolor="#ABCDEF"><b>INPUT FILES</b></td></tr>
	<?php foreach ($files as $key => $val): ?>
	<?php if (strstr($val, ".html")): ?>
	<tr>
		<td>
			<a href="run-web.php?input_filename=<?php echo $examplesDir . DIRECTORY_SEPARATOR . $val; ?> "> <?php echo $val; ?> </a>
		</td>
	</tr>
	<?php endif; ?>
	<?php endforeach; ?>
</table>
</body>
</html>
