<?php
function setHtmlWysiwyg($body, $class = "p-wysiwyg", $post_type = "news") {
	global $wpflg, $local_path;
?>
	<section class="<?php echo $class; ?>">
		<div class="section__wrap">
			<div class="<?php echo $class; ?>__wrap">
				<div class="<?php echo $class; ?>__head">
					<h1 class="<?php echo $class; ?>__title"><span><?php echo $body['title']['main']; ?></span></h1>
					<div class="<?php echo $class; ?>__info">
						<p class="<?php echo $class; ?>__date"><span><?php echo $body['date']; ?></span></p>
						<?php if (!empty($body['taxonomy']['category'])) : ?>
							<div class="p-common__article__category">
								<?php setHtmlTaxonomy($body['taxonomy']['category'], false); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="<?php echo $class; ?>__body">
					<?php
					if (!empty($wpflg) && function_exists('the_content')) {
						// WordPress環境: the_content() で本文を出力
						the_content();
					} elseif (!empty($body['content'])) {
						// ローカル環境: 静的データの content をそのまま出力
						// echo $body['content'];
					?>
						<img src="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy.webp" alt="">
						<h2>見出しが入ります</h2>
						<p>この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。<a href="#">この文章はダミーです。</a>文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字</p>
						<h1>見出し1（h1）テスト</h1>
						<p>この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</p>

						<h2>見出し2（h2）テスト</h2>
						<p>この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</p>

						<h3>見出し3（h3）テスト</h3>
						<p>この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</p>

						<h4>見出し4（h4）テスト</h4>
						<p>この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</p>

						<h5>見出し5（h5）テスト</h5>
						<p>この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</p>

						<h6>見出し6（h6）テスト</h6>
						<p>この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</p>

						<hr>

						<h2>テキスト装飾</h2>
						<p>通常テキスト。<strong>太字（strong）</strong>のテスト。<b>太字（b）</b>のテスト。<em>斜体（em）</em>のテスト。<i>斜体（i）</i>のテスト。<u>下線（u）</u>のテスト。<s>取り消し線（s）</s>のテスト。</p>
						<p><strong><em>太字＋斜体の組み合わせ</em></strong>のテスト。<strong><u>太字＋下線</u></strong>のテスト。</p>
						<p><sup>上付き文字（sup）</sup>と<sub>下付き文字（sub）</sub>のテスト。H<sub>2</sub>O、E=mc<sup>2</sup></p>
						<p><code>インラインコード（code）</code>のテスト。</p>

						<h2>リンク</h2>
						<p><a href="https://furfolk.com/">通常リンク</a>のテスト。<a href="https://furfolk.com/" target="_blank">別タブリンク（target_blank）</a>のテスト。</p>

						<h2>順序なしリスト（ul）</h2>
						<ul>
							<li>リスト項目1。この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</li>
							<li>リスト項目2。この文章はダミーです。</li>
							<li>リスト項目3
								<ul>
									<li>ネストしたリスト項目3-1</li>
									<li>ネストしたリスト項目3-2</li>
								</ul>
							</li>
							<li>リスト項目4</li>
						</ul>

						<h2>順序ありリスト（ol）</h2>
						<ol>
							<li>番号付きリスト項目1。この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</li>
							<li>番号付きリスト項目2。この文章はダミーです。</li>
							<li>番号付きリスト項目3
								<ol>
									<li>ネストした番号付きリスト3-1</li>
									<li>ネストした番号付きリスト3-2</li>
								</ol>
							</li>
							<li>番号付きリスト項目4</li>
						</ol>

						<h2>引用（blockquote）</h2>
						<blockquote>
							<p>この文章はダミーです。引用ブロックのテストです。文字サイズと量・字間・行間等を確認するために入れています。この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</p>
						</blockquote>

						<h2>整形済みテキスト（pre）</h2>
						<pre>整形済みテキスト（pre）のテスト。
  インデントや改行が
  そのまま保持されます。
    さらにインデント。</pre>

						<h2>テーブル（table）</h2>
						<table>
							<thead>
								<tr>
									<th>見出し1</th>
									<th>見出し2</th>
									<th>見出し3</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>セル1-1</td>
									<td>セル1-2</td>
									<td>セル1-3</td>
								</tr>
								<tr>
									<td>セル2-1</td>
									<td>セル2-2。この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</td>
									<td>セル2-3</td>
								</tr>
								<tr>
									<td>セル3-1</td>
									<td>セル3-2</td>
									<td>セル3-3</td>
								</tr>
							</tbody>
						</table>

						<h2>画像（img）</h2>
						<p>画像はShopifyのWYSIWYGから挿入してください。</p>

						<h2>水平線（hr）</h2>
						<p>上のテキスト</p>
						<hr>
						<p>下のテキスト</p>

						<h2>改行（br）</h2>
						<p>1行目のテキスト<br>2行目のテキスト（brタグで改行）<br>3行目のテキスト</p>

						<h2>複合テスト</h2>
						<p>通常テキストの中に<strong>太字</strong>と<em>斜体</em>と<a href="https://furfolk.com/">リンク</a>と<code>コード</code>が混在するパターン。この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。この文章はダミーです。文字サイズと量・字間・行間等を確認するために入れています。</p>
						<blockquote>
							<p><strong>太字の引用</strong>。この文章はダミーです。<em>斜体も混在</em>させています。<a href="https://furfolk.com/">リンク付き引用</a>のテスト。</p>
						</blockquote>
						<ul>
							<li><strong>太字のリスト項目</strong> — 説明テキスト</li>
							<li><em>斜体のリスト項目</em> — 説明テキスト</li>
							<li><a href="https://furfolk.com/">リンク付きリスト項目</a> — 説明テキスト</li>
						</ul>
					<?php
					}
					?>
				</div>
				<?php
				setHtmlSnsshare();
				setHtmlPagerArr($post_type);
				?>
			</div>
		</div>
	</section>
<?php
}


function setAcfWysiwyg($name = "wysiwyg", $label = "Wysiwyg", $logic = array()) {
	$array = array(
		'type' => "wysiwyg",
		'label' => $label,
		'name' => $name,
		'tabs' => 'all', //'all' (Visual & Text), 'visual' (Visual Only) or text (Text Only)
		'toolbar' => 'full', // 'full' (Full), 'basic'
		'media_upload' => 1,
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
