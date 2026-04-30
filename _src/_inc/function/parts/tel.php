<?php
function setHtmlTel($tel)
{
	return 'tel:' . str_replace("-", "", $tel);
?>
<?php
}