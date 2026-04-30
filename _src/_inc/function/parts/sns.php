<?php
function setHtmlSns()
{
global $snslist;
?>
	<div class="p-sns">
		<ul>
			<?php foreach ($snslist as $sns) : ?>
				<li>
					<a href="<?php echo $sns['link'] ?>" target="_blank">
						<?php setHtmlSvg($sns['icon']); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php
}
