
<?php if (!isset($_GET["amp"])) : ?>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($map_api, ENT_QUOTES); ?>&loading=async"></script>
	<script src="https://cdn.jsdelivr.net/npm/viewport-extra@1.0.3/dist/viewport-extra.min.js"></script>
	<script>
		new ViewportExtra(375)
	</script>
	<script>
	  window.LOCAL_PATH = "<?= htmlspecialchars($local_path, ENT_QUOTES); ?>";
	</script>
<?php endif; ?>

