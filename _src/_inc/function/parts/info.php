<?php
function setHtmlInfo()
{
	global $client_name, $add, $buil, $zip, $tel, $mapurl;
?>
	<p class="p-info">
		<span>
			<span><?php echo $client_name; ?></span><br>
			<?php echo $zip; ?> <br class="show_pctb"><?php echo $add; ?> <br><?php echo $buil; ?> [ <a href="<?php echo $mapurl; ?>" class="map">Google Maps</a> ]<br>
			TEL : <a href="<?php echo setHtmlTel($tel); ?>" class="tel"><?php echo $tel; ?></a>
		</span>
	</p>
<?php
}
