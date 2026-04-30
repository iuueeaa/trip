<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>

<?php
$sectionId = "outline";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>

			<div class="p-table">
				<table>
					<tbody>
						<?php foreach ($sectionValue['dl'] as $tr) : ?>
							<tr>
								<th><span><?php echo $tr['th']; ?></span></th>
								<td><span><?php echo $tr['td']; ?></span></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>


<?php
$sectionId = "access";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>

			<?php setHtmlMap($sectionValue["map"]); ?>
		</div>
	</div>
</section>
