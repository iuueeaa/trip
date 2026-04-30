<?php
$sectionId = "styleguide";
$sectionClass = "page-styleguide__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
  <div class="section__wrap">
    <div class="section__inner">

      <?php $boxValue = $sectionValue['logo']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="list">
                <ul>
                  <li><?php setHtmlLogo('logo', 'p-logo', $site_title);  ?></li>
                  <li><?php setHtmlLogo('logo', 'p-logo', $site_title);  ?></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      

    
      <?php $boxValue = $sectionValue['color']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="list">
                <?php foreach ($boxValue['list'] as $list) : ?>
                <?php setHtmlTitle($list['title'], 'box__subtitle', 'h2'); ?>
                <ul class="<?php echo $list['class']; ?>">
                  <?php foreach ($list['list'] as $li) : ?>
                    <li>
                      <dl>
                        <dt style="--c: var(--<?php echo $li; ?>); <?php if($li === "Base1"){ echo "border: 1px solid var(--Base2)"; }; ?>">
                          <span></span>
                        </dt>
                        <dd>
                          <span class="data"><?php echo $li; ?></span>
                          <span class="var data">var(--<?php echo $li; ?>)</span>
                        </dd>
                      </dl>
                    </li>
                  <?php endforeach; ?>
                </ul>
                
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php $boxValue = $sectionValue['font']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="list">
                <ul>
                  <?php foreach ($boxValue['list'] as $li) : ?>
                    <li>
                      <dl style="font-family: var(--<?php echo $li['var']; ?>);">
                        <dt><span><?php echo $li['main']; ?></span></dt>
                        <dd><span><?php echo $li['sub']; ?></span></dd>
                      </dl>
                      <span class="var data">var(--<?php echo $li['var']; ?>)</span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php $boxValue = $sectionValue['title']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="list">
                <ul>
                  <?php foreach ($boxValue['list'] as $li) : ?>
                    <li>
                      <?php setHtmlTitle($li['title'], $li['class'], 'h2'); ?>
                      <!-- <span class="data"><?php echo $li['name']; ?></span> -->
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      

      <?php $boxValue = $sectionValue['text']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="list">
                <ul>
                  <?php foreach ($boxValue['list'] as $li) : ?>
                    <li>
                      <?php setHtmlText($li['text'], $li['class']); ?>
                      <span class="data"><?php echo $li['name']; ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php $boxValue = $sectionValue['button']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="list">
                <ul>
                  <?php foreach ($boxValue['list'] as $li) : ?>
                    <li>
                      <?php setHtmlLink($li['link'], $li['class'], ''); ?>
                      <span class="data"><?php echo $li['name']; ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php $boxValue = $sectionValue['image']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="list">
                <ul>
                  <?php foreach ($boxValue['list'] as $li) : ?>
                    <li>
                      <?php setHtmlBgImage($li['image'], $li['class']); ?>
                      <span class="data"><?php echo $li['name']; ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php $boxValue = $sectionValue['icon']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="list">
                <ul>
                  <?php foreach ($boxValue['list'] as $li) : ?>
                    <li>
                      <?php setHtmlSvg($li['svg']); ?>
                      <!-- <span class="data"><?php echo $li['name']; ?></span> -->
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php $boxValue = $sectionValue['grid']; ?>
      <div class="box is-<?php echo $boxValue['class']; ?>">
        <div class="box__wrap">
          <div class="box__inner">
            <div class="box__head">
              <?php setHtmlTitle($boxValue['title'], 'box__title', 'h2'); ?>
            </div>
            <div class="box__body">
              <div class="grid">
    
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

  <!-- <div class="<?php echo $sectionClass; ?>__wrap">
    <?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
    <?php
    $boxClass = 'p-box__yoko';
    $boxValue = $sectionValue['imageText'];
    ?>

    <div class="<?php echo $boxClass; ?>">
      <div class="<?php echo $boxClass; ?>__wrap">
        <div class="<?php echo $boxClass; ?>__image imageBox">
          <?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
        </div>
        <div class="<?php echo $boxClass; ?>__text textBox">
          <div class="titleBox">
            <?php setHtmlTitle($boxValue['title'], 'p-title__sub', 'h3'); ?>
            <?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
            <?php setHtmlText($boxValue['text'], 'p-text'); ?>
            <?php setHtmlLink($boxValue['link'], 'p-button'); ?>
          </div>
        </div>
      </div>
    </div>
  </div> -->

<script>
    // CSS変数の値を取得して、必要に応じて解決する関数
    function resolveCSSVariable(variable) {
        let value = getComputedStyle(document.documentElement).getPropertyValue(variable);
        if (value.startsWith('var(--')) {
            // 再帰的にCSS変数を解決
            return resolveCSSVariable(value.trim().slice(4, -1));
        }
        return value.trim();
    }

    // HEXからRGBAへ変換する関数
    function hexToRGBA(hex) {
        let r = 0,
            g = 0,
            b = 0,
            a = 1;
        if (hex.length === 7) {
            r = parseInt(hex.slice(1, 3), 16);
            g = parseInt(hex.slice(3, 5), 16);
            b = parseInt(hex.slice(5, 7), 16);
        } else if (hex.length === 9) {
            r = parseInt(hex.slice(1, 3), 16);
            g = parseInt(hex.slice(3, 5), 16);
            b = parseInt(hex.slice(5, 7), 16);
            a = parseInt(hex.slice(7, 9), 16) / 255;
        }
        return `rgba(${r}, ${g}, ${b}, ${a})`;
    }

    // RGBAからHEXへ変換する関数
    function rgbaToHex(rgba) {
        const parts = rgba.replace(/rgba?|\(|\)|\s/g, '').split(',');
        let r = parseInt(parts[0]).toString(16);
        let g = parseInt(parts[1]).toString(16);
        let b = parseInt(parts[2]).toString(16);
        let a = Math.round(parseFloat(parts[3]) * 255).toString(16);

        if (r.length === 1) r = "0" + r;
        if (g.length === 1) g = "0" + g;
        if (b.length === 1) b = "0" + b;
        if (a.length === 1) a = "0" + a;

        // return `#${r}${g}${b}${a}`;
        return `#${r}${g}${b}`;
    }

    // HSLAからRGBAへ変換する関数
    function hslaToRGBA(hsla) {
        const parts = hsla.replace(/hsla?|\(|\)|\s/g, '').split(',');
        let h = parseInt(parts[0]) / 360;
        let s = parseInt(parts[1]) / 100;
        let l = parseInt(parts[2]) / 100;
        let a = parseFloat(parts[3]);

        function hue2rgb(p, q, t) {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        }

        let r, g, b;
        if (s === 0) {
            r = g = b = l; // achromatic
        } else {
            const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            const p = 2 * l - q;
            r = hue2rgb(p, q, h + 1 / 3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1 / 3);
        }

        return `rgba(${Math.round(r * 255)}, ${Math.round(g * 255)}, ${Math.round(b * 255)}, ${a})`;
    }

    // RGBAからHSLAへの変換関数
    function rgbaToHSLA(rgba) {
        let [r, g, b, a] = rgba.match(/\d+\.?\d*/g).map(Number);
        r /= 255;
        g /= 255;
        b /= 255;
        const max = Math.max(r, g, b),
            min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0; // achromatic
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r:
                    h = (g - b) / d + (g < b ? 6 : 0);
                    break;
                case g:
                    h = (b - r) / d + 2;
                    break;
                case b:
                    h = (r - g) / d + 4;
                    break;
            }
            h /= 6;
        }

        return `hsla(${Math.round(h * 360)}, ${Math.round(s * 100)}%, ${Math.round(l * 100)}%, ${a})`;
    }

    function isColor(value) {
        return /^#([0-9a-f]{3}){1,2}$/i.test(value) || // HEX
            /^rgba?\(\d{1,3},\s*\d{1,3},\s*\d{1,3}(,\s*[\d.]+)?\)$/i.test(value) || // RGBA
            /^hsla?\(\d{1,3},\s*[\d.]+%,\s*[\d.]+%(,\s*[\d.]+)?\)$/i.test(value); // HSLA
    }


    function outputColor(target, value) {

        let content;
        if (isColor(value)) {


            let rgba, hex, hsla;

            if (value.startsWith('hsla')) {
                hsla = value;
                rgba = hslaToRGBA(value);
                hex = rgbaToHex(rgba);
            } else if (value.startsWith('rgba')) {
                rgba = value;
                hex = rgbaToHex(value);
                hsla = rgbaToHSLA(value);
            } else if (value.startsWith('#')) {
                hex = value;
                rgba = hexToRGBA(value);
                hsla = rgbaToHSLA(rgba);

            }
            content = `HEX: ${hex}<br>RGBA: ${rgba}<br>HSLA: ${hsla}`;
            // content = `${hex}<br>${rgba}<br>${hsla}`;
            content = `${hex}`;


        } else {
            // カラー形式ではない場合の処理
            // content = `カラー形式ではない: ${value}`;
            const list = value.split(',').map(font => font.trim());
            // if (list.length >= 2) {
            //     // フォントリストから最初の2つのフォントを取得
            //     content = `<span>${list[0]}</span><span>,${list[1]}</span>`;
            // }
            content = '';
            if (list.length > 0) {
              list.forEach((target, index) => {
                // if(index != 0){
                 content += '<span>'+target+'</span>'
                // }
            });
            } else {
                // フォントリストが1つまたは空の場合
                // content = `Value: ${value}`;
                content = `${value}`;
            }
        }

        target.innerHTML = content;


    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.var').forEach(target => {
            let variableName = target.textContent.trim().slice(4, -1);
            let resolvedValue = resolveCSSVariable(variableName);
            outputColor(target, resolvedValue);
        });
    });
</script>
