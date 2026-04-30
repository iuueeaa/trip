<?php

function setHtmlTaxonomy($taxonomy, $class = "p-category", $displayAll = false, $link_flg = true)
{
?>
	<div class="<?php echo $class; ?>">
		<?php if ($displayAll) : ?>
			<a class="" href=""><span>すべて</span></a>
		<?php endif; ?>
		<?php
		foreach ($taxonomy as $term) {
			$taxonomyName = (string)($term['taxonomy'] ?? '');
			$postType = '';
			$taxSlug = '';
			if (!empty($term['parent']) && !is_numeric($term['parent'])) {
				$postType = (string)$term['parent'];
			}
			if (!empty($term['term']) && is_string($term['term'])) {
				$taxSlug = ltrim($term['term'], '_');
			}
			if ($postType === '' || $taxSlug === '') {
				$parts = explode('_', $taxonomyName, 2);
				$postType = $postType !== '' ? $postType : ($parts[0] ?? '');
				$taxSlug = $taxSlug !== '' ? $taxSlug : ltrim(($parts[1] ?? ''), '_');
			}
			$link = buildTaxonomyUrl($postType, $taxSlug, (string)($term['slug'] ?? ''));
			$color = (!empty($term['color'])) ? $term['color'] : "var(--Key1)";
		?>
			<?php if ($link_flg) : ?>
				<?php if ($term['name'] != "") : ?>
					<a class="" href="<?php echo $link; ?>" style="--TaxColor:<?php echo $color; ?>"><span><?php echo str_replace(array("\r\n", "\r", "\n"), '', strip_tags($term['name'])); ?></span></a>
				<?php endif; ?>
			<?php else : ?>
				<p><span><?php echo str_replace(array("\r\n", "\r", "\n"), '', strip_tags($term['name'])); ?></span></p>
			<?php endif; ?>
		<?php } ?>
	</div>
<?php
}
