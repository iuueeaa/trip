<?php
function setHtmlAgenda($body, $class = "p-agenda")
{
	$agendaarr = array();
	if (!empty($body)) {
		foreach ($body as $section) {
			foreach ($section["box"] as $box) {
				if (!empty($box["acf_fc_layout"])) {
					if ($box["acf_fc_layout"] == "title") {
						if ($box["title"]["h"] == "h2") {
							$agendaarr[]["title"] = $box["title"];
						}
					}
				}
			}
		}
	}
?>
	<div class="<?php echo $class; ?>">
		<p class="<?php echo $class; ?>__title"><span>目次</span></p>
		<ul class="<?php echo $class; ?>__ul">
			<?php
			$agendnum = 0;
			foreach ($agendaarr as $agenda) :
			?>
				<li>
					<a href="#contents_<?php echo $agendnum; ?>" class="">
						<?php setHtmlSvg("icon-arrow3"); ?>
						<p class="title">
							<span class="title__main"><?php echo $agenda["title"]["main"] ?></span>
							<span class="title__sub"><?php echo $agenda["title"]["sub"] ?></span>
						</p>
					</a>
				</li>
			<?php
				$agendnum++;
			endforeach; ?>
		</ul>
	</div>
<?php
}
