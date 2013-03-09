<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */
$this->layout( 'layout' );
?>
<script type="text/javascript" src="/logger.js"></script>
<h2>Log</h2>
<div class="log"><pre id="log">Attaching to log...
</pre>
</div>
<script type="text/javascript">
	connect(<?php echo $websocketPort; ?>);
</script>