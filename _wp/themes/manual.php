<?php
$image_path = get_template_directory_uri() . "/assets/image/manual/";
$manual_value = array(
  array(
    "ttl" => "共通",
    "box" => array(
      array(
        "ttl" => "タイトルとパーマリンク",
        "body" => array(
          "image" => $image_path . "dummy.webp",
          "txt" => "タイトルを入力すると自動でパーマリンクが設定されます<br>ただし、URLは英語の方がSEOとして良いため、英語の表記に変更をしますaaa。"
        )
      ),
      array(
        "ttl" => "画像の登録",
        "body" => array(
          "image" => $image_path . "dummy.webp",
          "txt" => "Wordpressでサイズの出し分けを自動でできるので、<b>1800px × 1200px</b>で登録をしてください。<br>ドラッグ&ドロップで登録できます。"
        )
      ),
      array(
        "ttl" => "リンクの登録",
        "body" => array(
          "image" => $image_path . "dummy.webp",
          "dl" => array(
            array(
              "dt" => "URL",
              "dd" => "https://google.com のようなURLを入力"
            ),
            array(
              "dt" => "リンク文字列",
              "dd" => "[詳しくはこちら]など表示するテキストを入力"
            ),
            array(
              "dt" => "リンクを新しいタブで開く",
              "dd" => "違うドメインにリンクするときなどに、チェックを入れます。"
            ),
          ),
          "txt" => "Wordpressに登録しているページへのリンクなら、[または既存のコンテンツにリンク]の部分で検索キーワードからページを指定することができます。"
        )
      ),
      array(
        "ttl" => "ブロックの並び替え",
        "body" => array(
          "image" => $image_path . "dummy.webp",
          "txt" => "行に連番が降ってある時は、数字のところにカーソルを合わせると並び替えができます"
        )
      ),
      array(
        "ttl" => "公開作業",
        "body" => array(
          "image" => $image_path . "dummy.webp",
          "txt" => "各編集画面の右に公開に関するボタンがあります。",
          "dl" => array(
            array(
              "dt" => "変更をプレビュー",
              "dd" => "編集内容を確認できます"
            ),
            array(
              "dt" => "ステータス",
              "dd" => "公開済み・下書きで表示・非表示を選択できます。(レビューは使用しません)"
            ),
            array(
              "dt" => "公開状態",
              "dd" => "表示非表示を制御できますが、ステータスで管理をお願いします。"
            ),
            array(
              "dt" => "投稿日時",
              "dd" => "任意の時刻に設定できます。未来の日時にすると予約投稿になります。表示順が日時の場合、ここを編集して並び替えをできます。"
            ),
          ),
        )
      )
    )
  ),
  array(
    "ttl" => "初期設定",
    "box" => array(
      array(
        "ttl" => "お客様情報",
        "body" => array(
          "image" => $image_path . "dummy.webp",
          "txt" => "サイト内で共通で使用する内容についてはこちらで設定をしています。"
        )
      ),
      array(
        "ttl" => "メタ情報",
        "body" => array(
          "image" => $image_path . "dummy.webp",
          "txt" => "検索などで使用するパラメータを設定します。ディスクリプションやog画像はページ毎に設定をしていただきますが、こちらはTOPや未設定の場合に表示される共通のものになります。",
          "dl" => array(
            array(
              "dt" => "サイトタイトル",
              "dd" => "Googleの検索結果で表示されるタイトルです。"
            ),
            array(
              "dt" => "ディスクリプション",
              "dd" => "Googleの検索結果で表示されるサイト説明です。"
            ),
            array(
              "dt" => "og画像",
              "dd" => "facebookなどでサイトのURLをシェアしたときに表示される画像です。"
            ),
            array(
              "dt" => "Google Map API",
              "dd" => 'サイト内でマップを表示させるときはこちらの設定が必要です。<a href="" target="_blank">API取得方法</a>を参考にしてAPIと緯度・経度・マップのリンクを取得してください'
            ),
            array(
              "dt" => "GoogleAnalytics",
              "dd" => 'ページビュー数などを計測してくれるためのGoogleのサービスです。サイト毎にトラッキングコードを取得して設定します。<a href="https://analytics.google.com/" target="_blank">Analyticsのログインはこちら</a>'
            ),
          ),
        )
      )
    )
  )
);
?>
<div class="p-wpadmin">
  <h1>マニュアル</h1>
  <?php foreach ($manual_value as $manual_sec) : ?>
    <section class="p-manual">
      <h2><span><?php echo $manual_sec["ttl"]; ?></span></h2>
      <?php foreach ($manual_sec["box"] as $manual_box) : ?>
        <div class="p-manual_box">
          <h3 class="p-manual_box_ttl"> <?php echo $manual_box["ttl"]; ?></h3>
          <?php foreach ($manual_box["body"] as $manual_type => $manual_body) : ?>
            <?php if ($manual_type == "image") : ?>
              <img src="<?php echo $manual_body; ?>" alt="">
            <?php elseif ($manual_type == "ol") : ?>
            <?php elseif ($manual_type == "dl") : ?>
              <table>
                <tbody>
                  <?php foreach ($manual_body as $key => $dl) : ?>
                    <tr>
                      <th><?php echo $dl["dt"]; ?></th>
                      <td><?php echo $dl["dd"]; ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php elseif ($manual_type == "txt") : ?>
              <p><?php echo $manual_body; ?></p>
            <?php endif; ?>

          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </section>
  <?php endforeach; ?>
</div>
