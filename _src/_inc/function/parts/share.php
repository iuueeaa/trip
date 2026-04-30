<?php
function setHtmlSnsshare()
{
	global $url, $page_title, $site_title;
	$title = $page_title;
	$title = ($page_title != "") ? $page_title . "_" . $site_title : $site_title;
?>
	<ul class="p-share">
		<li class="fb">
			<a href="https://www.facebook.com/sharer.php?u=<?php echo urlencode($url); ?>" onclick="javascript:window.open(this.href, '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600');return false;">
				<?php setHtmlSvg('sns-facebook'); ?>
			</a>
		</li>
		<li class="x">
			<a href="//twitter.com/intent/tweet?url=<?php echo urlencode($url); ?>&text=<?php echo $title; ?>" onclick="javascript:window.open(this.href, '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600');return false;">
				<?php setHtmlSvg('sns-x'); ?>
			</a>
		</li>
		<li class="hatena">
			<a target="_blank" href="http://b.hatena.ne.jp/entry/<?php echo urlencode($url); ?>&title=<?php echo $title; ?>">
				<?php setHtmlSvg('sns-hatena'); ?>
			</a>
		</li>
		<li class="line">
			<a href="//line.me/R/msg/text/?<?php echo $title; ?><?php echo urlencode($url); ?>" target="_blank">
				<?php setHtmlSvg('sns-line'); ?>
			</a>
		</li>
		<li class="pocket">
			<a href="http://getpocket.com/edit?url=<?php echo urlencode($url); ?>&title=<?php echo $title; ?>" onclick="window.open(this.href, 'PCwindow', 'width=550, height=350, menubar=no, toolbar=no, scrollbars=yes'); return false;">
				<?php setHtmlSvg('sns-pocket'); ?>
			</a>
		</li>
		<li class="note">
			<a href="https://note.com/intent/post?url=<?php echo $url; ?>/&ref=<?php echo $url; ?>" onclick="window.open(this.href, 'PCwindow', 'width=550, height=350, menubar=no, toolbar=no, scrollbars=yes'); return false;">
				<?php setHtmlSvg('sns-note'); ?>
			</a>
		</li>
		<li class="feedly">
			<a href="https://feedly.com/i/subscription/feed/<?php echo urlencode($url . "/feed/"); ?>" onclick="window.open(this.href, 'PCwindow', 'width=550, height=350, menubar=no, toolbar=no, scrollbars=yes'); return false;">
				<?php setHtmlSvg('sns-feedly'); ?>
			</a>
		</li>
		<!-- <li class="pinterest">
			<a data-pin-custom=true data-pin-do="buttonBookmark" href="https://www.pinterest.com/pin/create/button/" target="_blank">
				<?php setHtmlSvg('sns-pinterest'); ?></a>
			<script async defer src="//assets.pinterest.com/js/pinit.js"></script>
		</li> -->
		<li class="copy">
			<a href="javascript:void(0);" id="ShareCopy" onclick="copyLink()" data-copy="<?php echo $url; ?>">
				<?php setHtmlSvg('icon-copy'); ?>
			</a>
		</li>
		<script>
			function copyLink() {
				var copyText = document.getElementById("ShareCopy");
				navigator.clipboard.writeText(copyText.dataset.copy);
			}
		</script>
	</ul>
<?php
}
