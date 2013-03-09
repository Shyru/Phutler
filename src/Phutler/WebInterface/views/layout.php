<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */
?>
<html>
	<head>
		<title><?php echo $request->phutlerName; ?> WebInterface</title>
		<link rel="stylesheet" type="text/css" href="/style.css">
	</head>
	<body>
		<h1><?php echo $request->phutlerName; ?></h1>
		<?php echo $this->content(); ?>
	</body>

</html>
