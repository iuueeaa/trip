<?php

function setHtmlSearch($class = "p-search")
{
?>
	<form action="get" class="<?php echo $class; ?>">
		<label for="">
			<input type="text" name="s" placeholder="キーワードを入力">
		</label>
		<button type="submit"><?php setHtmlSvg("icon-search"); ?></button>
	</form>
<?php }
