<?php
$page_value_name = 'page_top';

$url = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__));
$url = ltrim($url, '/');
if (strpos($url, '/') == "") :
	$url = $url;
else :
	$num = strpos($url, '/');
	$url = substr($url, 0, $num);
endif;
$local_path       = is_numeric($url) ? '/' . $url : '';
$root_path        = $_SERVER['DOCUMENT_ROOT'] . $local_path;

include($root_path . "/assets/inc/_l-head.php");
include($root_path . "/assets/inc/_l-header.php");
?>

<?php
$sectionId = "vueApp";
$sectionClass = "page-xxxxx__" . $sectionId;
?>
<section id="vueApp" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">

			<!-- 投稿 -->
			<ul>
				<li v-for="(works, index) in restApiData.works" :key="index">
					<a :href="works.link">
						<p><span v-html="works.title.rendered"></span></p>
						<p><span v-html="works.status"></span></p>
					</a>
				</li>
			</ul>



			<!-- たくそのミー -->
			<dl>
				<dt><span>カテゴリ一覧</span></dt>
				<dd>
					<div class="p-checkbox">
						<div v-for='(term, termindex) in restApiData.works_type' :key="termindex">
							<label v-if="term.count > 0">
								<input type="checkbox" @change="changeFilter($event, 'works_type')" v-model="selectTerm" :value="term.slug" />
								<span v-text="term.name"></span>
							</label>
						</div>
					</div>
				</dd>
			</dl>
		</div>
	</div>
</section>

<?php include($root_path . "/assets/inc/_l-foot.php"); ?>
</body>

</html>
