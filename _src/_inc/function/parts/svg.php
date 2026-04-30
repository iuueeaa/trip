<?php
function setHtmlSvg($path = "icon-array", $class = "")
{
	global $icon_path; ?>
	<svg class="<?php echo $class; ?>">
		<use xlink:href="<?php echo $icon_path . $path; ?>"></use>
	</svg>
<?php
}
