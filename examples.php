<?php

$examplesDir = "examples" . DIRECTORY_SEPARATOR . "input";

$files = scandir($examplesDir);

//vardump($files);

?>

<table width="100%">
	<tr><td bgcolor="#ABCDEF"><b>INPUT FILES</b></td></tr>
	<?php foreach ($files as $key => $val): ?>
	<?php if (strstr($val, ".html")): ?>
	<tr>
		<td>
			<a href="run-web.php?file=<?php echo $examplesDir . DIRECTORY_SEPARATOR . $val; ?> "> <?php echo $val; ?> </a>
		</td>
	</tr>
	<?php endif; ?>
	<?php endforeach; ?>
</table>
