"use strict";
import { Splide } from "@splidejs/splide";
import { AutoScroll } from "@splidejs/splide-extension-auto-scroll";
import Lenis from "lenis";

/**
 * ============================================================
 * index.js — モジュール一覧
 * ============================================================
 *
 * [Core]
 *   ScrollLockManager     スクロールロック管理（複数ロック対応）
 *   EnvironmentSupport    環境検出（passive, reducedMotion）
 *   DOM Utilities         toArray, addClass, removeClass 等
 *   Measurements          offsetTop 等
 *   BREAKPOINTS / Media   PC/TB/SP ブレークポイント判定
 *   Ease                  イージング関数（quart）
 *   Cookie Utilities      setCookie, getCookie, deleteCookie
 *   Environment           スクロール・ビューポート状態の共有オブジェクト
 *   ScrollBus / ResizeBus イベント配信バス
 *   UnifiedRAFManager     全モジュール共有の単一RAFループ
 *   IntersectionObserverHub  IO の一元管理
 *   BaseModule            モジュール基底クラス
 *   ModuleRegistry        モジュール登録・起動・デバッグ
 *
 * [Lenis]
 *   initLenis             慣性スクロール初期化 + LenisChips フロート
 *
 * [Loading]
 *   LoadSequence          ページ読み込みシーケンス管理
 *
 * [Metrics]
 *   ContentMetrics        --windowHeight, --vh, --pageHeight をCSS変数に反映
 *   FixedSVH              --svh をCSS変数に反映（iOS Safari対策）
 *   HeaderMetrics         --headerHeight をCSS変数に反映
 *   FooterMetrics         --footerHeight をCSS変数に反映
 *
 * [Scroll State]
 *   ScrollState           スクロール位置に応じた body クラス付与
 *   ScrollActionElements  .js-sa 要素のスクロール連動アニメーション
 *   ParallaxImages        .js-sa__image のパララックス
 *   SectionState          section の表示状態・カレント管理・ヘッダー状態
 *   InvertParts           固定要素が反転エリアに入ったら is-invert 付与
 *   SubNavCurrent         .l-subNab のカレント追従 + 横スクロール中央寄せ
 *
 * [UI Components]
 *   AnchorScroll          ページ内アンカーリンクのスムーススクロール
 *   HamburgerMenu         ハンバーガーメニュー開閉
 *   Tabs                  タブ切り替え
 *   Accordion             アコーディオン開閉
 *   GoogleMap              Google Maps 初期化
 *   SlideShow             自動再生スライドショー（IO連動）
 *   SplideController      Splide スライダー一括管理（同期ペア対応）
 *   TileEqualizer         タイル高さ揃え
 *   PankuzuNarrow         パンくずの is-narrow 連動
 *   BrWrap                連続 br を span.is-brwrap で囲む
 *
 * [Media]
 *   LazyBackground        背景画像の遅延読み込み
 *   YouTubePlayers        YouTube 埋め込み管理（IO連動）
 *   VideoPlayers          HTML5 Video 管理（IO連動）
 *
 * [Modal]
 *   ModalModule           モーダル基盤（スクロールロック統合）
 *   ModalGallerySplide    モーダル内ギャラリースライダー
 *   ModalYouTube          モーダル内 YouTube 再生
 *   ModalVideo            モーダル内 Video 再生
 *
 * [Form]
 *   FormValidation        フォーム入力バリデーション
 *
 * [Theme]
 *   InvertMode            反転モード切替（Cookie保持） ※案件ごとに有効化
 * ============================================================
 */

/* ==========================================================================
Core — ユーティリティ・環境検出・DOM操作
========================================================================== */
const DEBUG = true;

/* ========== Lenis 設定 ========== */
const LENIS_ENABLED = true; // 慣性スクロール ON/OFF
const LENIS_ENABLED_SP = true; // SP でも有効にするか（様子を見て false に切り替え可）

let lenisInstance = null;

/* ========== ScrollLockManager（コールバック対応版） ========== */
class ScrollLockManager {
  constructor() {
    this.locks = new Set();
    this.lastScrollY = 0;
    this.isScrollAnimating = false;
    this.onAnimationEndCallbacks = [];
  }

  lock(key) {
    if (this.locks.size === 0) {
      this.lastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
      document.body.style.position = "fixed";
      document.body.style.top = -this.lastScrollY + "px";
      document.body.style.left = "0";
      document.body.style.right = "0";
      document.body.style.width = "100%";
      if (lenisInstance) lenisInstance.stop();
    }
    this.locks.add(key);
  }

  unlock(key) {
    this.locks.delete(key);
    if (this.locks.size === 0) {
      document.body.style.position = "";
      document.body.style.top = "";
      document.body.style.left = "";
      document.body.style.right = "";
      document.body.style.width = "";
      if (this.lastScrollY) {
        window.scrollTo(0, this.lastScrollY);
      }
      if (lenisInstance) lenisInstance.start();
    }
  }

  isLocked() {
    return this.locks.size > 0 || this.isScrollAnimating;
  }

  startScrollAnimation() {
    this.isScrollAnimating = true;
  }

  endScrollAnimation() {
    this.isScrollAnimating = false;
    this.onAnimationEndCallbacks.forEach((callback) => {
      try {
        callback();
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    });
  }

  onScrollAnimationEnd(callback) {
    if (typeof callback === "function") {
      this.onAnimationEndCallbacks.push(callback);
    }
  }
}

const scrollLockManager = new ScrollLockManager();

/* ========== 1) 環境検出 ========== */
const EnvironmentSupport = (() => {
  let supportsPassiveEventListener = false;
  try {
    const options = Object.defineProperty({}, "passive", {
      get() {
        supportsPassiveEventListener = true;
        return true;
      },
    });
    function noop() {}
    window.addEventListener("testPassive", noop, options);
    window.removeEventListener("testPassive", noop, options);
  } catch (_) {}

  const prefersReducedMotion = window.matchMedia ? window.matchMedia("(prefers-reduced-motion: reduce)").matches : false;

  return { supportsPassiveEventListener, prefersReducedMotion };
})();

/* ========== 2) DOM 基本 ========== */
function toArray(target) {
  if (!target) return [];
  if (Array.isArray(target)) return target;
  if (target instanceof NodeList || target instanceof HTMLCollection) {
    return Array.from(target);
  }
  return [target];
}

function hasClass(target, className) {
  if (!target || !target.classList) return false;
  return target.classList.contains(className);
}

function addClass(targets, className) {
  const elements = toArray(targets);
  const names = Array.isArray(className) ? className : [className];
  elements.forEach((element) => {
    if (!element || !element.classList) return;
    names.forEach((name) => {
      if (name) element.classList.add(name);
    });
  });
}

function removeClass(targets, className) {
  const elements = toArray(targets);
  const names = Array.isArray(className) ? className : [className];
  elements.forEach((element) => {
    if (!element || !element.classList) return;
    names.forEach((name) => {
      if (name) element.classList.remove(name);
    });
  });
}

/* ========== 3) 計測 ========== */
const ScrollingElement = document.scrollingElement || document.documentElement;

function offsetTop(element) {
  const rect = element.getBoundingClientRect();
  return rect.top + (ScrollingElement.scrollTop || 0);
}

/* ========== 4) メディア判定（設定化） ========== */
const BREAKPOINTS = {
  PC: 1001,
  TB: 681,
  SP: 0,
};

function computeMedia() {
  const width = window.innerWidth;
  if (width >= BREAKPOINTS.PC) return "PC";
  if (width >= BREAKPOINTS.TB) return "TB";
  return "SP";
}

let _media = computeMedia();

function getMedia() {
  return _media;
}

function setMedia(value) {
  _media = value;
}

/* ========== 5) イージング ========== */
const Ease = {
  quart(t) {
    if (t < 0.5) {
      return 8 * t * t * t * t;
    }
    return 1 - Math.pow(-2 * t + 2, 4) / 2;
  },
};

/* ========== 6) Cookieユーティリティ ========== */
function setCookie(name, value, days) {
  if (!name) return;
  let expires = "";
  if (typeof days === "number") {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function getCookie(name) {
  if (!name) return "";
  const nameEQ = name + "=";
  const parts = document.cookie.split(";");
  for (let i = 0; i < parts.length; i++) {
    let c = parts[i];
    while (c.charAt(0) === " ") {
      c = c.substring(1);
    }
    if (c.indexOf(nameEQ) === 0) {
      return decodeURIComponent(c.substring(nameEQ.length));
    }
  }
  return "";
}

function deleteCookie(name) {
  setCookie(name, "", -1);
}

/* ==========================================================================
Core — イベントバス・RAFループ・BaseModule
========================================================================== */

/* ===== 1) 環境設定 ===== */
const Environment = {
  scrollTop: 0,
  viewportWidth: window.innerWidth,
  viewportHeight: window.innerHeight,
  media: getMedia(),
  devicePixelRatio: Math.min(window.devicePixelRatio || 1, 2),
  prefersReducedMotion: EnvironmentSupport.prefersReducedMotion,
  passiveSupported: EnvironmentSupport.supportsPassiveEventListener,
};

/* ===== 2) イベントバス管理 ===== */
const ScrollBus = new Set();
const ResizeBus = new Set();

let _scrollRafId = 0;
let _resizeRafId = 0;
let _lastViewportWidth = window.innerWidth;
let _lastViewportHeight = window.innerHeight;
let _lastScrollTime = 0;
const SCROLL_THROTTLE_MS = 200;

/* ===== 3) スクロールイベント（iOS Safari 徹底対策版） ===== */
window.addEventListener(
  "scroll",
  function onScrollListener() {
    if (scrollLockManager.isLocked()) return;

    const now = performance.now();
    if (now - _lastScrollTime < SCROLL_THROTTLE_MS) {
      return;
    }

    if (_scrollRafId) return;

    _scrollRafId = requestAnimationFrame(() => {
      _scrollRafId = 0;
      _lastScrollTime = performance.now();
      Environment.scrollTop = ScrollingElement.scrollTop || 0;

      for (const subscriber of ScrollBus) {
        try {
          subscriber(Environment);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      }
    });
  },
  { passive: true },
);

/* ===== 4) リサイズイベント（最適化版） ===== */
window.addEventListener(
  "resize",
  function onResizeListener() {
    const currentWidth = window.innerWidth;
    const currentHeight = window.innerHeight;
    const prevMedia = getMedia();

    if (prevMedia !== "PC" && currentWidth === _lastViewportWidth && currentHeight !== _lastViewportHeight) {
      return;
    }

    if (currentWidth === _lastViewportWidth && currentHeight === _lastViewportHeight) {
      return;
    }

    _lastViewportWidth = currentWidth;
    _lastViewportHeight = currentHeight;

    if (_resizeRafId) return;

    _resizeRafId = requestAnimationFrame(() => {
      _resizeRafId = 0;
      Environment.viewportWidth = window.innerWidth;
      Environment.viewportHeight = window.innerHeight;

      const media = computeMedia();
      setMedia(media);
      Environment.media = media;
      Environment.devicePixelRatio = Math.min(window.devicePixelRatio || 1, 2);

      for (const subscriber of ResizeBus) {
        try {
          subscriber(Environment);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      }
    });
  },
  { passive: Environment.passiveSupported },
);

/* ==========================================================================
UnifiedRAFManager — 全モジュール共有の単一RAFループ
========================================================================== */

class UnifiedRAFManager {
  constructor() {
    this.scrollSubscribers = new Set(); // スクロール連動（activeItems方式）
    this.tickSubscribers = new Set(); // 常時実行（毎フレーム）
    this.animationFrameId = null;
    this.isRunning = false;
    this.loop = this.loop.bind(this);
  }

  // スクロール連動モジュール用（activeItems方式）
  subscribeScroll(callback) {
    this.scrollSubscribers.add(callback);
    this.start();

    return () => {
      this.scrollSubscribers.delete(callback);
      if (this.scrollSubscribers.size === 0 && this.tickSubscribers.size === 0) {
        this.stop();
      }
    };
  }

  // 常時実行モジュール用（毎フレーム）
  subscribeTick(callback) {
    this.tickSubscribers.add(callback);
    this.start();

    return () => {
      this.tickSubscribers.delete(callback);
      if (this.scrollSubscribers.size === 0 && this.tickSubscribers.size === 0) {
        this.stop();
      }
    };
  }

  start() {
    if (this.isRunning) return;
    this.isRunning = true;
    this.animationFrameId = requestAnimationFrame(this.loop);
  }

  stop() {
    if (!this.isRunning) return;
    this.isRunning = false;
    if (this.animationFrameId) {
      cancelAnimationFrame(this.animationFrameId);
      this.animationFrameId = null;
    }
  }

  loop(timestamp) {
    if (!this.isRunning) return;

    // Lenis を先に更新して scrollTop を確定させてから各モジュールに渡す
    if (lenisInstance) lenisInstance.raf(timestamp);

    const scrollTop = ScrollingElement.scrollTop || 0;
    const viewportHeight = window.innerHeight;

    // スクロール連動コンテキスト
    const scrollContext = {
      timestamp,
      scrollTop,
      viewportHeight,
    };

    // 常時実行コンテキスト
    const tickContext = {
      time: timestamp,
      env: Environment,
    };

    // スクロール連動サブスクライバーを実行
    for (const callback of this.scrollSubscribers) {
      try {
        callback(scrollContext);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }

    // 常時実行サブスクライバーを実行
    for (const callback of this.tickSubscribers) {
      try {
        callback(tickContext.time, tickContext.env);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }

    this.animationFrameId = requestAnimationFrame(this.loop);
  }

  debug() {
    console.log("[UnifiedRAF] scroll=" + this.scrollSubscribers.size + " tick=" + this.tickSubscribers.size + " running=" + this.isRunning);
  }
}

// グローバルインスタンス
const UnifiedRAF = new UnifiedRAFManager();

/* ===== 5) オブザーバーハブ ===== */
const IntersectionObserverHub = (() => {
  const map = new Map();
  let observer = null;

  if (typeof window !== "undefined" && "IntersectionObserver" in window) {
    observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        const set = map.get(entry.target);
        if (set) {
          set.forEach((callback) => {
            try {
              callback(entry);
            } catch (error) {
              if (DEBUG) console.warn(error);
            }
          });
        }
      });
    });
  }

  function observe(element, callback) {
    if (!observer || !element || typeof callback !== "function") {
      return () => {};
    }
    if (!map.has(element)) {
      map.set(element, new Set());
      observer.observe(element);
    }
    const set = map.get(element);
    set.add(callback);

    return () => unobserve(element, callback);
  }

  function unobserve(element, callback) {
    if (!observer) return;
    const set = map.get(element);
    if (!set) return;
    set.delete(callback);
    if (!set.size) {
      map.delete(element);
      observer.unobserve(element);
    }
  }

  return { observe, unobserve };
})();

/* ===== 6) BaseModuleクラス ===== */
class BaseModule {
  constructor(name) {
    this.name = name;
    this._mounted = false;
    this._stops = [];
    this._dirty = true;
  }

  setup() {}
  mount() {
    this._mounted = true;
  }
  update() {}
  onResize(env) {
    this._dirty = true;
    this.update();
  }

  markDirty() {
    this._dirty = true;
  }

  clearDirty() {
    this._dirty = false;
  }

  isDirty() {
    return this._dirty;
  }

  _setVar(name, value) {
    if (!this.root) return;
    try {
      this.root.style.setProperty(name, value);
    } catch (error) {
      if (DEBUG) console.warn(error);
    }
  }

  debug() {
    console.log("[" + this.name + "]");
  }

  disconnect() {
    this._stops.forEach((stop) => {
      try {
        if (stop) stop();
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    });
    this._stops = [];
    this._mounted = false;
  }
}

/* ===== 7) モジュール登録・起動 ===== */
const ModuleRegistry = [];

function registerModule(instance) {
  ModuleRegistry.push(instance);

  // tick()メソッドがあればUnifiedRAFに登録（毎フレーム実行）
  if (typeof instance.tick === "function") {
    const unsubscribe = UnifiedRAF.subscribeTick((time, env) => {
      try {
        instance.tick(time, env);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    });
    instance._stops.push(unsubscribe);
  }

  // onScroll()メソッドがあればScrollBusに登録
  if (typeof instance.onScroll === "function") {
    const wrapper = (env) => {
      try {
        instance.onScroll(env);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    };
    ScrollBus.add(wrapper);
    instance._stops.push(() => ScrollBus.delete(wrapper));
  }

  // onResize()メソッドがあればResizeBusに登録
  if (typeof instance.onResize === "function") {
    const wrapper = (env) => {
      try {
        instance.onResize(env);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    };
    ResizeBus.add(wrapper);
    instance._stops.push(() => ResizeBus.delete(wrapper));
  }
}

/* ===== 8) ページ読み込み完了後の強制リサイズ処理 ===== */
window.addEventListener(
  "load",
  function onWindowLoad() {
    for (const instance of ModuleRegistry) {
      try {
        instance.setup();
      } catch (error) {
        if (DEBUG) console.error(error);
      }
      try {
        instance.mount();
      } catch (error) {
        if (DEBUG) console.error(error);
      }
      try {
        instance.update();
      } catch (error) {
        if (DEBUG) console.error(error);
      }
    }

    try {
      ModalModule.init();
    } catch (error) {
      if (DEBUG) console.error(error);
    }

    // ページ読み込み完了後に強制的にリサイズイベントを発火
    setTimeout(() => {
      Environment.viewportWidth = window.innerWidth;
      Environment.viewportHeight = window.innerHeight;

      const media = computeMedia();
      setMedia(media);
      Environment.media = media;

      // 全てのリサイズサブスクライバーを実行
      for (const subscriber of ResizeBus) {
        try {
          subscriber(Environment);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      }

      if (DEBUG) {
        console.log("[System] Forced resize executed after page load");
      }
    }, 100);
  },
  { passive: Environment.passiveSupported },
);

/* ===== 9) デバッグ支援 ===== */
function dev() {
  ModuleRegistry.forEach((instance, index) => {
    console.log("[" + index + "] " + instance.name);
    try {
      if (typeof instance.debug === "function") {
        instance.debug();
      }
    } catch (error) {
      if (DEBUG) console.warn(error);
    }
  });

  // UnifiedRAFの状態も表示
  if (typeof UnifiedRAF !== "undefined" && UnifiedRAF.debug) {
    UnifiedRAF.debug();
  }
}

if (DEBUG) {
  window.addEventListener("keydown", function onKeydown(e) {
    if (e.key === "d" || e.key === "D") {
      dev();
    }
    if (e.key === "g" || e.key === "G") {
      document.body.classList.toggle("is-guide");
    }
  });
}
/* ==========================================================================
Lenis 慣性スクロール初期化
========================================================================== */

function initLenis() {
  const CONFIG = {
    duration: 1.3,
    wheelMultiplier: 1.1,
    touchMultiplier: 1.5,
    // LenisChips
    chipFactors: [1.0, 0.7, 1.2, 0.5],
    chipLerpFactors: [0.04, 0.02, 0.035, 0.015],
    chipVelocityMultiplier: -1.5,
    chipInputLerp: 0.12,
    chipDecay: 0.93,
    chipThreshold: 0.001,
  };

  if (!LENIS_ENABLED) return;
  if (EnvironmentSupport.prefersReducedMotion) return;
  if (!LENIS_ENABLED_SP && getMedia() === "SP") return;
  if (lenisInstance) return;

  lenisInstance = new Lenis({
    duration: CONFIG.duration,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // expo easing
    orientation: "vertical",
    gestureOrientation: "vertical",
    smoothWheel: true,
    wheelMultiplier: CONFIG.wheelMultiplier,
    touchMultiplier: CONFIG.touchMultiplier,
    // smoothTouch: false,
    // ハンバーガーメニュー内・モーダル内はネイティブスクロールを維持
    prevent: (node) => {
      return !!(node.closest(".js-hbg__modal") || node.closest(".js-modal__inner"));
    },
  });

  if (DEBUG) console.log("[Lenis] initialized");

  /* ---- LenisChips フロートエフェクト ---- */
  const chips = toArray(document.querySelectorAll(".js-lenisChips"));
  if (chips.length) {
    const factors = CONFIG.chipFactors;
    const lerpFactors = CONFIG.chipLerpFactors;

    let rawVelocity = 0; // Lenis から受け取った生の速度
    let targetOffset = 0; // rawVelocity を lerp で滑らかにした目標値
    const floatOffsets = new Array(chips.length).fill(0);

    lenisInstance.on("scroll", ({ velocity }) => {
      rawVelocity = velocity * CONFIG.chipVelocityMultiplier;
    });

    UnifiedRAF.subscribeTick(() => {
      // 入力側も lerp で滑らかにする（急激な変化を吸収）
      targetOffset += (rawVelocity - targetOffset) * CONFIG.chipInputLerp;
      // 余韻（rawVelocity を減衰させて止まった後じんわり戻す）
      rawVelocity *= CONFIG.chipDecay;

      chips.forEach((el, i) => {
        const lerp = lerpFactors[i] ?? 0.06;
        floatOffsets[i] += (targetOffset - floatOffsets[i]) * lerp;
        const val = floatOffsets[i] * (factors[i] ?? 1);

        if (Math.abs(val) < CONFIG.chipThreshold) {
          // 実質0なら書き込みをスキップしてスナップ
          if (floatOffsets[i] !== 0) {
            floatOffsets[i] = 0;
            el.style.setProperty("--float-offset", "0%");
          }
        } else {
          el.style.setProperty("--float-offset", `${val}%`);
        }
      });
    });
  }
}

/* ========== LoadSequence — ページ読み込みシーケンス ========== */
(function LoadSequence() {
  /* ---- タイミング設定 ---- */
  const CONFIG = {
    HASH_JUMP_DELAY: 20, // hash スクロール後に Lenis 起動するまでの待機(ms)
    LOADING_START_DELAY: 20, // is-loadingStart 付与の遅延(ms)
    LAZY_FALLBACK_TIMEOUT: 3000, // lazybackground 未完了時のフォールバック(ms)
    LOADING_END_DELAY: 600, // ローディング画面のフェードアウト用待機(ms)
  };

  const root = document.documentElement;
  const body = document.body;
  const hasHash = typeof location.hash === "string" && location.hash.length > 1;
  const loadingElement = document.querySelector(".l-loading");
  let lazyBackgroundComplete = false;
  let windowLoadFired = false;

  /* ---- lazybackground:complete 受信 ---- */
  document.addEventListener("lazybackground:complete", () => {
    lazyBackgroundComplete = true;

    if (!loadingElement) {
      normalProcess();
    } else {
      tryFinishLoading();
    }
  });

  /* ---- 通常の開始処理（ローディング後 or ローディングなし） ---- */
  function normalProcess() {
    if (!hasHash) {
      initLenis();
      removeClass(body, "is-ready");
      addClass(body, "is-load");
      addClass(root, "is-load");
      return;
    }

    setTimeout(() => {
      try {
        const id = decodeURIComponent(location.hash.slice(1));
        let target = document.getElementById(id);
        if (!target) {
          target = document.querySelector('[name="' + id.replace(/"/g, '\\"') + '"]');
        }
        if (target) {
          const y = offsetTop(target);
          window.scrollTo(0, y);
        }
      } catch (_) {}
      // hash へのジャンプが完了してから Lenis を起動
      initLenis();
      removeClass(body, "is-ready");
      addClass(body, "is-load");
      addClass(root, "is-load");
    }, CONFIG.HASH_JUMP_DELAY);
  }

  /* ---- ローディング完了判定 ---- */
  function tryFinishLoading() {
    if (!lazyBackgroundComplete || !windowLoadFired) return;

    // ローディング画面のフェードアウト用に少し待つ
    setTimeout(() => {
      addClass(body, "is-loadingEnd");
      scrollLockManager.unlock("Loading");
      normalProcess();
    }, CONFIG.LOADING_END_DELAY);
  }

  /* ==== ローディング画面なしの場合 ==== */
  if (!loadingElement) {
    window.addEventListener(
      "load",
      () => {
        if (lazyBackgroundComplete) {
          normalProcess();
          return;
        }

        setTimeout(() => {
          if (!lazyBackgroundComplete) {
            normalProcess();
          }
        }, CONFIG.LAZY_FALLBACK_TIMEOUT);
      },
      {
        passive: EnvironmentSupport.supportsPassiveEventListener,
      },
    );
    return;
  }

  /* ==== ローディング画面ありの場合 ==== */
  function initLoadingLock() {
    window.scrollTo(0, 0);
    scrollLockManager.lock("Loading");
    removeClass(body, "is-ready");
    setTimeout(() => {
      addClass(body, "is-loadingStart");
    }, CONFIG.LOADING_START_DELAY);
  }

  // <script type="module"> はDOMContentLoaded後に実行されるため、readyStateで分岐
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initLoadingLock, { passive: EnvironmentSupport.supportsPassiveEventListener });
  } else {
    initLoadingLock();
  }

  window.addEventListener(
    "load",
    () => {
      windowLoadFired = true;
      tryFinishLoading();
    },
    { passive: EnvironmentSupport.supportsPassiveEventListener },
  );
})();

/* ========== 1) ContentMetrics（ResizeObserver化） ========== */
class ContentMetrics extends BaseModule {
  constructor() {
    super("ContentMetrics");
    this.root = null;
    this._useVisualViewport = typeof window.visualViewport === "object";
    this._lastWindowHeight = 0;
    this._lastPageHeight = 0;
    this._onVVResize = null;
    this._resizeObserver = null;
  }

  setup() {
    this.root = document.documentElement || document.querySelector(":root");

    if (typeof ResizeObserver !== "undefined") {
      this._resizeObserver = new ResizeObserver(() => {
        if (!this.isDirty()) this.markDirty();
        this.update();
      });
      if (ScrollingElement) {
        this._resizeObserver.observe(ScrollingElement);
      }
    }
  }

  mount() {
    if (!this.root) {
      this.root = document.documentElement || document.querySelector(":root");
    }
    this.update();

    if (this._useVisualViewport) {
      this._onVVResize = this._onVisualViewportResize.bind(this);
      window.visualViewport.addEventListener("resize", this._onVVResize, {
        passive: Environment.passiveSupported,
      });
      this._stops.push(() => {
        try {
          window.visualViewport.removeEventListener("resize", this._onVVResize);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      });
    }
  }

  onResize() {
    this.markDirty();
    this.update();
  }

  update() {
    if (!this.root || !this.isDirty()) return;

    const windowHeight = window.innerHeight;
    const pageHeight = ScrollingElement?.scrollHeight || document.documentElement.scrollHeight;

    if (this._lastWindowHeight !== windowHeight) {
      this._lastWindowHeight = windowHeight;
      this._setVar("--windowHeight", windowHeight + "px");
      this._setVar("--vh", windowHeight + "px");
    }

    if (this._lastPageHeight !== pageHeight) {
      this._lastPageHeight = pageHeight;
      this._setVar("--pageHeight", pageHeight + "px");
    }

    this.clearDirty();
  }

  _onVisualViewportResize() {
    this.markDirty();
    this.update();
  }

  debug() {
    console.log("[ContentMetrics] window=" + this._lastWindowHeight + " page=" + this._lastPageHeight);
  }
}
registerModule(new ContentMetrics());

/* ========== 2) FixedSVH（ResizeObserver化） ========== */
class FixedSVH extends BaseModule {
  constructor() {
    super("FixedSVH");
    this.root = null;
    this._probe = null;
    this._useVV = typeof window.visualViewport === "object";
    this._lastWidth = 0;
    this._lastHeight = 0;
    this._resizeObserver = null;
  }

  setup() {
    this.root = document.documentElement || document.querySelector(":root");

    if (this._supports("height", "100svh")) {
      try {
        this._probe = document.createElement("div");
        this._probe.style.cssText = "position:fixed;visibility:hidden;pointer-events:none;inset:0 auto auto 0;width:1px;height:100svh";
        document.body.appendChild(this._probe);

        if (typeof ResizeObserver !== "undefined") {
          this._resizeObserver = new ResizeObserver(() => {
            this.markDirty();
            this._apply();
          });
          this._resizeObserver.observe(this._probe);
        }
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }

    this._lastWidth = this._getWindowWidth();
    this._lastHeight = this._getWindowHeight();
  }

  mount() {
    this._apply();
  }

  onResize() {
    const width = this._getWindowWidth();
    const height = this._getWindowHeight();

    if (width <= 0 || height <= 0) return;

    const mediaType = computeMedia();
    let needUpdate = false;

    if (width !== this._lastWidth) {
      needUpdate = true;
    } else if (mediaType === "PC" && height !== this._lastHeight) {
      needUpdate = true;
    }

    if (!needUpdate) return;

    this._lastWidth = width;
    this._lastHeight = height;
    this.markDirty();
    this._apply();
  }

  update() {}

  _apply() {
    if (!this.isDirty()) return;

    const px = this._calcSVHpx();
    if (px > 0) {
      this._setVar("--svh", px + "px");
    }

    this.clearDirty();
  }

  _calcSVHpx() {
    if (this._probe) {
      try {
        const rect = this._probe.getBoundingClientRect();
        const h = Math.round(rect.height);
        if (h > 0) return h;
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }

    if (this._useVV) {
      try {
        const vv = Math.round(window.visualViewport.height);
        if (vv > 0) return vv;
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }

    return window.innerHeight;
  }

  _supports(prop, val) {
    if (typeof CSS === "undefined" || !CSS.supports) return false;
    try {
      return CSS.supports(prop, val);
    } catch (_) {
      return false;
    }
  }

  _getWindowWidth() {
    if (typeof window.innerWidth === "number") return window.innerWidth;
    if (this.root?.clientWidth) return this.root.clientWidth;
    return 0;
  }

  _getWindowHeight() {
    if (typeof window.innerHeight === "number") return window.innerHeight;
    if (this.root?.clientHeight) return this.root.clientHeight;
    return 0;
  }

  debug() {
    if (!this.root) return;
    const value = getComputedStyle(this.root).getPropertyValue("--svh").trim();
    console.log("[FixedSVH] svh=" + value + " width=" + this._lastWidth + " height=" + this._lastHeight);
  }
}
registerModule(new FixedSVH());

/* ========== 3) ScrollState ========== */
class ScrollState extends BaseModule {
  constructor() {
    super("ScrollState");
    this.root = null;
    this.body = null;
    this.thresholds = [
      { threshold: 10, className: "scrollStart" },
      { threshold: 100, className: "scrollOver100" },
      { threshold: 200, className: "scrollOver200" },
      { threshold: 400, className: "scrollOver400" },
    ];
    this._lastFlags = new Map();
    this._lastScrollTop = -1;
    this._lastScrollMax = -1;
  }

  setup() {
    this.root = document.documentElement || document.querySelector(":root");
    this.body = document.body;
  }

  mount() {
    this.update();
  }

  onScroll(env) {
    this._apply(env.scrollTop);
  }

  onResize(env) {
    this.markDirty();
    this._apply(env.scrollTop);
  }

  update() {
    const scrollTop = ScrollingElement.scrollTop || 0;
    this._apply(scrollTop);
  }

  _apply(scrollTop) {
    this._updateScrollValues(scrollTop);
    this._updateFlags(scrollTop);
  }

  _updateScrollValues(scrollTop) {
    const scrollMax = this._calcScrollMax();

    if (scrollTop !== this._lastScrollTop) {
      this._lastScrollTop = scrollTop;
      this._setVar("--scrollTop", scrollTop + "px");
    }

    if (scrollMax !== this._lastScrollMax) {
      this._lastScrollMax = scrollMax;
      this._setVar("--scrollMax", scrollMax + "px");
    }
  }

  _updateFlags(scrollTop) {
    const scrollMax = this._lastScrollMax >= 0 ? this._lastScrollMax : this._calcScrollMax();

    this._flag("scrollEnd", scrollMax > 0 && scrollTop >= scrollMax);

    for (const item of this.thresholds) {
      this._flag(item.className, scrollTop > item.threshold);
    }
  }

  _flag(name, isActive) {
    if (!this.body) return;

    const previous = this._lastFlags.get(name);
    if (previous === isActive) return;

    this._lastFlags.set(name, isActive);
    const className = "is-" + name;

    if (isActive) {
      this.body.classList.add(className);
    } else {
      this.body.classList.remove(className);
    }
  }

  _calcScrollMax() {
    const height = ScrollingElement?.scrollHeight || document.documentElement.scrollHeight;
    const max = height - window.innerHeight;
    return max > 0 ? max : 0;
  }

  debug() {
    console.log("[ScrollState] top=" + this._lastScrollTop + " max=" + this._lastScrollMax);
  }
}
registerModule(new ScrollState());

/* ========== 4) HeaderMetrics（ResizeObserver化） ========== */
class HeaderMetrics extends BaseModule {
  constructor() {
    super("HeaderMetrics");
    this.root = null;
    this.header = null;
    this._lastHeight = -1;
    this._resizeObserver = null;
  }

  setup() {
    this.root = document.documentElement || document.querySelector(":root");
    this.header = document.querySelector(".l-header");

    if (this.header && typeof ResizeObserver !== "undefined") {
      this._resizeObserver = new ResizeObserver(() => {
        this.markDirty();
        this.update();
      });
      this._resizeObserver.observe(this.header);
    }
  }

  mount() {
    this.update();
  }

  onResize() {
    this.markDirty();
    this.update();
  }

  update() {
    if (!this.root || !this.header || !this.isDirty()) return;

    const h = this.header.getBoundingClientRect().height;
    if (h !== this._lastHeight) {
      this._lastHeight = h;
      this._setVar("--headerHeight", h + "px");
    }

    this.clearDirty();
  }

  debug() {
    console.log("[HeaderMetrics] height=" + this._lastHeight);
  }
}
registerModule(new HeaderMetrics());

/* ========== 5) FooterMetrics（ResizeObserver化） ========== */
class FooterMetrics extends BaseModule {
  constructor() {
    super("FooterMetrics");
    this.root = null;
    this.footer = null;
    this._lastHeight = -1;
    this._resizeObserver = null;
  }

  setup() {
    this.root = document.documentElement || document.querySelector(":root");
    this.footer = document.querySelector("footer");

    if (this.footer && typeof ResizeObserver !== "undefined") {
      this._resizeObserver = new ResizeObserver(() => {
        this.markDirty();
        this.update();
      });
      this._resizeObserver.observe(this.footer);
    }
  }

  mount() {
    this.update();
  }

  onResize() {
    this.markDirty();
    this.update();
  }

  update() {
    if (!this.root || !this.footer || !this.isDirty()) return;

    const h = this.footer.getBoundingClientRect().height;
    if (h !== this._lastHeight) {
      this._lastHeight = h;
      this._setVar("--footerHeight", h + "px");
    }

    this.clearDirty();
  }

  debug() {
    console.log("[FooterMetrics] height=" + this._lastHeight);
  }
}
registerModule(new FooterMetrics());

/* ========== 6) InvertMode ========== */
class InvertMode extends BaseModule {
  constructor() {
    super("InvertMode");
    this.root = null;
    this.body = null;
    this.switches = [];
    this.cookieName = "invertMode";
    this.cookieDays = 7;
    this._isInvert = false;
    this._keyHandler = null;
  }

  setup() {
    this.root = document.documentElement || document.querySelector(":root");
    this.body = document.body;
    this.switches = toArray(document.querySelectorAll(".js-invert__switch"));

    if (location?.hash === "#Reset") {
      deleteCookie(this.cookieName);
      this._isInvert = false;
    } else {
      const value = getCookie(this.cookieName);
      if (value === "1") {
        this._isInvert = true;
      }
    }
  }

  mount() {
    this._applyClass();
    this._bind();
    this._bindKey();
  }

  _bind() {
    this.switches.forEach((element) => {
      if (!element) return;

      const onClick = (event) => {
        event.preventDefault();
        this.toggle();
      };

      element.addEventListener("click", onClick, false);
      this._stops.push(() => {
        try {
          element.removeEventListener("click", onClick, false);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      });
    });
  }

  _bindKey() {
    this._keyHandler = (event) => {
      const key = event.key || event.keyCode;
      if (key === "b" || key === "B" || key === 66) {
        this.toggle();
      }
    };

    window.addEventListener("keydown", this._keyHandler, false);
    this._stops.push(() => {
      try {
        window.removeEventListener("keydown", this._keyHandler, false);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    });
  }

  toggle() {
    this._isInvert = !this._isInvert;
    this._applyClass();
    this._save();
  }

  _applyClass() {
    if (!this.body || !this.root) return;

    if (this._isInvert) {
      this.body.classList.add("is-invert");
      this.root.classList.add("is-invert");
    } else {
      this.body.classList.remove("is-invert");
      this.root.classList.remove("is-invert");
    }
  }

  _save() {
    if (this._isInvert) {
      setCookie(this.cookieName, "1", this.cookieDays);
    } else {
      deleteCookie(this.cookieName);
    }
  }

  debug() {
    console.log("[InvertMode] invert=" + this._isInvert);
  }
}
// NOTE: InvertMode は案件ごとに有効化する。使用時は下行のコメントを外す
registerModule(new InvertMode());

/* ==========================================================================
Scroll — スクロール連動モジュール群
========================================================================== */

/* ==========================================================================
ScrollActionElements（UnifiedRAF統合版）
========================================================================== */
class ScrollActionElements extends BaseModule {
  constructor() {
    super("ScrollActionElements");
    this.CONFIG = {
      activeStartRatio: 0.9,
      activeEndRatio: 0.1,
      halfLineRatio: 0.5,
    };
    this.targets = [];
    this.activeItems = new Set();
    this.itemMap = new Map();
    this.measureFrameId = null;
    this._unsubscribe = null;
    this.stateCache = new Map();
    this.completedItems = new Set(); // 完了した要素を記録
  }

  setup() {
    const allTargets = toArray(document.querySelectorAll('[class*="js-sa"]'));
    this.targets = allTargets.filter((el) => !el.classList.contains("js-sa__image"));

    this.targets.forEach((element) => {
      const item = {
        element: element,
        top: 0,
        height: 1,
        width: 0,
        isOnce: element.classList.contains("is-once"), // 一度だけ処理するフラグ
        needsProgress: element.classList.contains("is-progress"), // progress計算が必要かどうか
      };
      this.itemMap.set(element, item);

      this.stateCache.set(element, {
        progressBottom: -1,
        progressTop: -1,
        isStart: false,
        isEnd: false,
        isHalf: false,
        isActive: false,
      });

      // 既にis-saクラスがついている場合は完了済みとして扱う
      if (element.classList.contains("is-sa")) {
        this.completedItems.add(item);
      }
    });

    this.intersectionObserver = new IntersectionObserver(this.onIntersect.bind(this), { threshold: 0, rootMargin: "50% 0px" });

    this.resizeObserver = new ResizeObserver(this.requestMeasure.bind(this));

    for (const element of this.targets) {
      this.intersectionObserver.observe(element);
      this.resizeObserver.observe(element);
    }
  }

  mount() {
    this.measureAllElements();

    scrollLockManager.onScrollAnimationEnd(() => {
      this.measureAllElements();
    });
  }

  onResize() {
    if (!scrollLockManager.isLocked()) {
      this.requestMeasure();
    }
  }

  requestMeasure() {
    if (this.measureFrameId) return;
    this.measureFrameId = requestAnimationFrame(() => {
      this.measureFrameId = null;
      this.measureAllElements();
    });
  }

  measureAllElements() {
    const scrollTop = ScrollingElement.scrollTop || 0;

    for (const element of this.targets) {
      const item = this.itemMap.get(element);
      if (!item) continue;

      // 完了済みの要素はスキップ
      if (this.completedItems.has(item)) continue;

      const rect = element.getBoundingClientRect();
      item.top = rect.top + scrollTop;
      item.height = Math.max(1, rect.height);
      item.width = rect.width;

      element.style.setProperty("--thisWidth", item.width + "px");
      element.style.setProperty("--thisHeight", item.height + "px");
      element.style.setProperty("--thisTop", item.top + "px");
    }
  }

  onIntersect(entries) {
    for (const entry of entries) {
      const item = this.itemMap.get(entry.target);
      if (!item) continue;

      // 完了済みの要素は無視
      if (this.completedItems.has(item)) continue;

      if (entry.isIntersecting) {
        this.activeItems.add(item);
      } else {
        this.activeItems.delete(item);
      }
    }

    if (this.activeItems.size > 0 && !this._unsubscribe) {
      this._unsubscribe = UnifiedRAF.subscribeScroll(this.animationUpdate.bind(this));
    }

    if (this.activeItems.size === 0 && this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
  }

  animationUpdate(context) {
    if (scrollLockManager.isLocked()) return;

    const { scrollTop, viewportHeight } = context;
    const itemsToComplete = []; // この更新で完了する要素のリスト

    for (const item of this.activeItems) {
      const element = item.element;
      const cache = this.stateCache.get(element);
      if (!cache) continue;

      // 完了済みの要素はスキップ
      if (this.completedItems.has(item)) continue;

      const topVp = item.top - scrollTop;
      const bottomVp = topVp + item.height;

      // is-progressクラスがある場合のみprogress計算
      if (item.needsProgress) {
        const progressBottom = Math.min(1, Math.max(0, (viewportHeight - topVp) / (viewportHeight + item.height)));
        const progressTop = Math.min(1, Math.max(0, -topVp / item.height));

        const roundedBottom = Math.round(progressBottom * 100000) / 100000;
        const roundedTop = Math.round(progressTop * 100000) / 100000;

        if (Math.abs(cache.progressBottom - roundedBottom) > 0.0001) {
          element.style.setProperty("--thisProgressBottom", roundedBottom + "");
          cache.progressBottom = roundedBottom;
        }

        if (Math.abs(cache.progressTop - roundedTop) > 0.0001) {
          element.style.setProperty("--thisProgressTop", roundedTop + "");
          cache.progressTop = roundedTop;
        }
      }

      const isStart = topVp <= viewportHeight;
      const isEnd = bottomVp <= 0;
      const isHalf = topVp <= viewportHeight * this.CONFIG.halfLineRatio && bottomVp > 0;
      const isActive = topVp <= viewportHeight * this.CONFIG.activeStartRatio && bottomVp >= viewportHeight * this.CONFIG.activeEndRatio;

      if (cache.isStart !== isStart) {
        element.classList.toggle("is-start", isStart);
        cache.isStart = isStart;
      }
      if (cache.isEnd !== isEnd) {
        element.classList.toggle("is-end", isEnd);
        cache.isEnd = isEnd;
      }
      if (cache.isHalf !== isHalf) {
        element.classList.toggle("is-half", isHalf);
        cache.isHalf = isHalf;
      }
      if (cache.isActive !== isActive) {
        element.classList.toggle("is-active", isActive);
        element.classList.toggle("is-sa", isActive);
        cache.isActive = isActive;

        // is-onceフラグがある要素がis-saになったら完了とマーク
        if (item.isOnce && isActive) {
          itemsToComplete.push(item);
        }
      }
    }

    // 完了した要素を処理
    for (const item of itemsToComplete) {
      this.completedItems.add(item);
      this.activeItems.delete(item);

      // IntersectionObserverの監視も停止
      if (this.intersectionObserver) {
        this.intersectionObserver.unobserve(item.element);
      }

      // ResizeObserverの監視も停止
      if (this.resizeObserver) {
        this.resizeObserver.unobserve(item.element);
      }
    }

    // activeItemsが空になったらUnifiedRAFの購読を停止
    if (this.activeItems.size === 0 && this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
  }

  update() {}

  disconnect() {
    if (this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
    if (this.measureFrameId) {
      cancelAnimationFrame(this.measureFrameId);
    }
    if (this.intersectionObserver) {
      this.intersectionObserver.disconnect();
    }
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  }

  debug() {
    console.log("[ScrollActionElements] targets=" + this.targets.length + " active=" + this.activeItems.size + " completed=" + this.completedItems.size);
  }
}
registerModule(new ScrollActionElements());
/* ==========================================================================
ParallaxImages（UnifiedRAF統合版）
========================================================================== */

class ParallaxImages extends BaseModule {
  constructor() {
    super("ParallaxImages");
    this.targets = [];
    this.activeItems = new Set();
    this.itemMap = new Map();
    this.measureFrameId = null;
    this._unsubscribe = null;
    this.progressCache = new Map();
  }

  setup() {
    this.targets = toArray(document.querySelectorAll(".js-sa__image"));

    this.targets.forEach((element) => {
      const item = {
        element: element,
        top: 0,
        height: 1,
        denominator: 1,
      };
      this.itemMap.set(element, item);
      this.progressCache.set(element, -1);
    });

    this.intersectionObserver = new IntersectionObserver(this.onIntersect.bind(this), { threshold: 0, rootMargin: "50% 0px" });

    this.resizeObserver = new ResizeObserver(this.requestMeasure.bind(this));

    for (const element of this.targets) {
      this.intersectionObserver.observe(element);
      this.resizeObserver.observe(element);
    }
  }

  mount() {
    this.measureAllElements();
  }

  onResize() {
    this.requestMeasure();
  }

  requestMeasure() {
    if (this.measureFrameId) return;
    this.measureFrameId = requestAnimationFrame(() => {
      this.measureFrameId = null;
      this.measureAllElements();
    });
  }

  measureAllElements() {
    const scrollTop = ScrollingElement.scrollTop || 0;
    const vpH = window.innerHeight;

    for (const element of this.targets) {
      const item = this.itemMap.get(element);
      if (!item) continue;

      const rect = element.getBoundingClientRect();
      item.top = rect.top + scrollTop;
      item.height = Math.max(1, rect.height);
      item.denominator = vpH + item.height;
    }
  }

  onIntersect(entries) {
    for (const entry of entries) {
      const item = this.itemMap.get(entry.target);
      if (!item) continue;

      if (entry.isIntersecting) {
        this.activeItems.add(item);
      } else {
        this.activeItems.delete(item);
      }
    }

    if (this.activeItems.size > 0 && !this._unsubscribe) {
      this._unsubscribe = UnifiedRAF.subscribeScroll(this.animationUpdate.bind(this));
    }

    if (this.activeItems.size === 0 && this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
  }

  animationUpdate(context) {
    if (scrollLockManager.isLocked()) return;

    const { scrollTop, viewportHeight } = context;

    for (const item of this.activeItems) {
      const topVp = item.top - scrollTop;
      const numerator = viewportHeight - topVp;
      const progressBottom = Math.min(1, Math.max(0, numerator / item.denominator));

      const rounded = Math.round(progressBottom * 100000) / 100000;
      const cached = this.progressCache.get(item.element);

      if (Math.abs(cached - rounded) > 0.0001) {
        item.element.style.setProperty("--thisProgressBottom", rounded + "");
        this.progressCache.set(item.element, rounded);
      }
    }
  }

  update() {}

  disconnect() {
    if (this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
    if (this.measureFrameId) {
      cancelAnimationFrame(this.measureFrameId);
    }
    if (this.intersectionObserver) {
      this.intersectionObserver.disconnect();
    }
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  }

  debug() {
    console.log("[ParallaxImages] targets=" + this.targets.length + " active=" + this.activeItems.size);
  }
}
registerModule(new ParallaxImages());

/* ==========================================================================
SectionState（UnifiedRAF統合版 + ヘッダー状態管理機能追加）
========================================================================== */

class SectionState extends BaseModule {
  constructor() {
    super("SectionState");
    this.CONFIG = {
      activeLineRatio: 0.9,
      halfLineRatio: 0.5,
      topThreshold: 50,
      bottomThreshold: 50,
    };
    this.root = null;
    this.body = null;
    this.footer = null;
    this.sections = [];
    this.activeItems = new Set();
    this.itemMap = new Map();
    this.measureFrameId = null;
    this._unsubscribe = null;
    this._currentSection = null;
    this._navLinks = null;
    this.stateCache = new Map();
    this._footerTop = 0;
    this._isFooterOverHalf = false;
  }

  setup() {
    this.root = document.documentElement;
    this.body = document.body;
    this.footer = document.querySelector("footer");
    this.sections = toArray(document.querySelectorAll("section"));
    this._navLinks = toArray(document.querySelectorAll("nav a[href]"));

    this.sections.forEach((element) => {
      const item = {
        element: element,
        top: 0,
        height: 1,
        width: 0,
        needsProgress: element.classList.contains("is-progress"),
      };
      this.itemMap.set(element, item);

      this.stateCache.set(element, {
        pBottom: -1,
        pTop: -1,
        isStart: false,
        isEnd: false,
        isHalf: false,
        isActive: false,
      });
    });

    this.intersectionObserver = new IntersectionObserver(this.onIntersect.bind(this), { threshold: 0, rootMargin: "0px" });

    this.resizeObserver = new ResizeObserver(this.requestMeasure.bind(this));

    for (const element of this.sections) {
      this.intersectionObserver.observe(element);
      this.resizeObserver.observe(element);
    }

    if (this.footer) {
      this.resizeObserver.observe(this.footer);
    }
  }

  mount() {
    this.measureAllElements();

    scrollLockManager.onScrollAnimationEnd(() => {
      this.measureAllElements();
    });

    // ロード時の初期状態を設定
    requestAnimationFrame(() => {
      this._initializeHeaderState();
    });
  }

  onResize() {
    if (!scrollLockManager.isLocked()) {
      this.requestMeasure();
    }
  }

  requestMeasure() {
    if (this.measureFrameId) return;
    this.measureFrameId = requestAnimationFrame(() => {
      this.measureFrameId = null;
      this.measureAllElements();
    });
  }

  measureAllElements() {
    const scrollTop = ScrollingElement.scrollTop || 0;

    for (const element of this.sections) {
      const item = this.itemMap.get(element);
      if (!item) continue;

      const rect = element.getBoundingClientRect();
      item.top = rect.top + scrollTop;
      item.height = Math.max(1, rect.height);
      item.width = rect.width;

      element.style.setProperty("--sectionWidth", item.width + "px");
      element.style.setProperty("--sectionHeight", item.height + "px");
    }

    if (this.footer) {
      const rect = this.footer.getBoundingClientRect();
      this._footerTop = rect.top + scrollTop;
    }
  }

  onIntersect(entries) {
    for (const entry of entries) {
      const item = this.itemMap.get(entry.target);
      if (!item) continue;

      if (entry.isIntersecting) {
        this.activeItems.add(item);
      } else {
        this.activeItems.delete(item);
      }
    }

    if (this.activeItems.size > 0 && !this._unsubscribe) {
      this._unsubscribe = UnifiedRAF.subscribeScroll(this.animationUpdate.bind(this));
    }

    if (this.activeItems.size === 0 && this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
  }

  animationUpdate(context) {
    if (scrollLockManager.isLocked()) return;

    const { scrollTop, viewportHeight } = context;

    this._checkFooterPosition(scrollTop, viewportHeight);

    for (const item of this.activeItems) {
      const element = item.element;
      const cache = this.stateCache.get(element);
      if (!cache) continue;

      const topVp = item.top - scrollTop;
      const bottomVp = topVp + item.height;

      if (item.needsProgress) {
        const denomBottom = viewportHeight + item.height;
        const pBottom = Math.min(1, Math.max(0, (viewportHeight - topVp) / denomBottom));
        const pTop = Math.min(1, Math.max(0, -topVp / item.height));

        const roundedBottom = Math.round(pBottom * 100000) / 100000;
        const roundedTop = Math.round(pTop * 100000) / 100000;

        if (Math.abs(cache.pBottom - roundedBottom) > 0.0001) {
          element.style.setProperty("--sectionProgressBottom", roundedBottom + "");
          cache.pBottom = roundedBottom;
        }

        if (Math.abs(cache.pTop - roundedTop) > 0.0001) {
          element.style.setProperty("--sectionProgressTop", roundedTop + "");
          cache.pTop = roundedTop;
        }
      }

      this._applyFlags(element, cache, topVp, bottomVp, viewportHeight);
    }

    this._updateCurrent(viewportHeight, scrollTop);
    this._updateSectionInvert();
    this._updateNavCurrent();
    this._updateHeaderState();
  }

  _applyFlags(section, cache, topVp, bottomVp, vpH) {
    const startLine = vpH;
    const halfLine = vpH * this.CONFIG.halfLineRatio;
    const activeLine = vpH * this.CONFIG.activeLineRatio;

    const isStart = topVp <= startLine;
    const isEnd = bottomVp <= 0;
    const isHalf = topVp <= halfLine && bottomVp > 0;
    const isActive = topVp <= activeLine && bottomVp > 0;

    if (cache.isStart !== isStart) {
      section.classList.toggle("is-start", isStart);
      cache.isStart = isStart;
    }
    if (cache.isEnd !== isEnd) {
      section.classList.toggle("is-end", isEnd);
      cache.isEnd = isEnd;
    }
    if (cache.isHalf !== isHalf) {
      section.classList.toggle("is-half", isHalf);
      cache.isHalf = isHalf;
    }
    if (cache.isActive !== isActive) {
      section.classList.toggle("is-active", isActive);
      cache.isActive = isActive;
    }
  }

  _updateCurrent(vpH, scrollTop) {
    const topLimit = this.CONFIG.topThreshold;
    const bottomLimit = this.CONFIG.bottomThreshold;
    const pageH = ScrollingElement.scrollHeight;
    const maxScroll = pageH - vpH;

    if (scrollTop >= maxScroll - bottomLimit) {
      if (this._currentSection) {
        this._currentSection.classList.remove("is-current");
      }
      this._currentSection = null;
      this._applyCurrentAttr("End");
      return;
    }

    const centerLine = vpH * 0.5;
    let best = null;
    let bestDist = Infinity;

    if (scrollTop <= topLimit) {
      let bestTop = Infinity;
      for (const item of this.activeItems) {
        if (item.top < bestTop) {
          bestTop = item.top;
          best = item.element;
        }
      }

      if (!best && this.sections.length > 0) {
        best = this.sections[0];
      }
    } else {
      for (const item of this.activeItems) {
        const topVp = item.top - scrollTop;
        const bottomVp = topVp + item.height;
        const secCenter = (topVp + bottomVp) * 0.5;
        const d = Math.abs(secCenter - centerLine);

        if (d < bestDist) {
          bestDist = d;
          best = item.element;
        }
      }
    }

    if (best === this._currentSection) {
      if (this._currentSection?.id) {
        this._applyCurrentAttr(this._currentSection.id);
      }
      return;
    }

    if (this._currentSection) {
      this._currentSection.classList.remove("is-current");
    }
    this._currentSection = best;

    if (best) {
      best.classList.add("is-current");
      this._applyCurrentAttr(best.id || "");
    } else {
      this._applyCurrentAttr("");
    }
  }

  _applyCurrentAttr(id) {
    if (id) {
      this.body.setAttribute("data-currentSection", id);
      this.root.setAttribute("data-currentSection", id);
    } else {
      this.body.removeAttribute("data-currentSection");
      this.root.removeAttribute("data-currentSection");
    }
  }

  _updateSectionInvert() {
    const activeInvert = this._currentSection?.classList.contains("is-invert");

    if (activeInvert) {
      this.body.classList.add("is-sectionInvert");
      this.root.classList.add("is-sectionInvert");
    } else {
      this.body.classList.remove("is-sectionInvert");
      this.root.classList.remove("is-sectionInvert");
    }
  }

  _updateNavCurrent() {
    if (!this._navLinks) return;

    const cid = this._currentSection?.id || "";

    for (const link of this._navLinks) {
      const href = link.getAttribute("href") || "";
      let tid = "";

      if (href.startsWith("#")) {
        tid = href.slice(1);
      } else {
        try {
          const url = new URL(href, location.href);
          if (url.pathname === location.pathname && url.hash) {
            tid = url.hash.slice(1);
          }
        } catch (_) {}
      }

      if (tid === cid) {
        link.classList.add("is-current");
      } else {
        link.classList.remove("is-current");
      }
    }
  }

  _checkFooterPosition(scrollTop, viewportHeight) {
    if (!this.footer) return;

    const footerTopVp = this._footerTop - scrollTop;
    const halfLine = viewportHeight * 0.5;

    const isOverHalf = footerTopVp <= halfLine;

    if (isOverHalf !== this._isFooterOverHalf) {
      this._isFooterOverHalf = isOverHalf;
    }
  }

  _updateHeaderState() {
    if (!this.body || !this.root) return;

    if (this._isFooterOverHalf) {
      this.body.classList.remove("is-headerTransparent");
      this.body.classList.remove("is-headerInvert");
      this.root.classList.remove("is-white");
      return;
    }

    if (!this._currentSection) {
      this.body.classList.remove("is-headerTransparent");
      this.body.classList.remove("is-headerInvert");
      this.root.classList.remove("is-white");
      return;
    }

    const hasHeaderTransparent = this._currentSection.classList.contains("is-headerTransparent");
    const hasHeaderInvert = this._currentSection.classList.contains("is-headerInvert");
    const hasWhite = this._currentSection.classList.contains("is-white");

    if (hasHeaderTransparent) {
      this.body.classList.add("is-headerTransparent");
    } else {
      this.body.classList.remove("is-headerTransparent");
    }

    if (hasHeaderInvert) {
      this.body.classList.add("is-headerInvert");
    } else {
      this.body.classList.remove("is-headerInvert");
    }

    if (hasWhite) {
      this.root.classList.add("is-white");
    } else {
      this.root.classList.remove("is-white");
    }
  }

  _initializeHeaderState() {
    if (!this.body || !this.root) return;

    const scrollTop = ScrollingElement.scrollTop || 0;
    const viewportHeight = window.innerHeight;

    this._checkFooterPosition(scrollTop, viewportHeight);

    const topLimit = this.CONFIG.topThreshold;

    if (scrollTop <= topLimit) {
      if (this.sections.length > 0) {
        this._currentSection = this.sections[0];
        if (this._currentSection) {
          this._currentSection.classList.add("is-current");
          this._applyCurrentAttr(this._currentSection.id || "");
        }
      }
    } else {
      const centerLine = viewportHeight * 0.5;
      let best = null;
      let bestDist = Infinity;

      for (const element of this.sections) {
        const rect = element.getBoundingClientRect();
        const topVp = rect.top;
        const bottomVp = rect.bottom;
        const secCenter = (topVp + bottomVp) * 0.5;
        const d = Math.abs(secCenter - centerLine);

        if (d < bestDist) {
          bestDist = d;
          best = element;
        }
      }

      if (best) {
        this._currentSection = best;
        this._currentSection.classList.add("is-current");
        this._applyCurrentAttr(this._currentSection.id || "");
      }
    }

    this._updateSectionInvert();
    this._updateNavCurrent();
    this._updateHeaderState();
  }

  update() {}

  disconnect() {
    if (this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
    if (this.measureFrameId) {
      cancelAnimationFrame(this.measureFrameId);
    }
    if (this.intersectionObserver) {
      this.intersectionObserver.disconnect();
    }
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  }

  debug() {
    console.log(
      "[SectionState] sections=" + this.sections.length + " active=" + this.activeItems.size + " current=" + (this._currentSection?.id || "none") + " footerOverHalf=" + this._isFooterOverHalf,
    );
  }
}
registerModule(new SectionState());
/* ==========================================================================
InvertParts — 固定/sticky要素が js-invertArea 内に入ったら is-invert を付与
========================================================================== */

class InvertParts extends BaseModule {
  constructor() {
    super("InvertParts");
    this.parts = [];
    this.areas = [];
    this.activeAreas = new Set();
    this.partsMap = new Map();
    this.areasMap = new Map();
    this.measureFrameId = null;
    this._unsubscribe = null;
    this.invertCache = new Map();
  }

  setup() {
    this.parts = toArray(document.querySelectorAll(".js-invertParts"));
    this.areas = toArray(document.querySelectorAll(".js-invertArea"));

    this.parts.forEach((element) => {
      const item = {
        element: element,
        top: 0,
        height: 1,
      };
      this.partsMap.set(element, item);
      this.invertCache.set(element, false);
    });

    this.areas.forEach((element) => {
      const item = {
        element: element,
        top: 0,
        bottom: 0,
      };
      this.areasMap.set(element, item);
    });

    this.intersectionObserver = new IntersectionObserver(this.onIntersect.bind(this), { threshold: 0 });

    this.resizeObserver = new ResizeObserver(this.requestMeasure.bind(this));

    for (const element of this.areas) {
      this.intersectionObserver.observe(element);
      this.resizeObserver.observe(element);
    }

    for (const element of this.parts) {
      this.resizeObserver.observe(element);
    }
  }

  mount() {
    this.measureAllElements();

    scrollLockManager.onScrollAnimationEnd(() => {
      this.measureAllElements();
    });
  }

  onResize() {
    if (!scrollLockManager.isLocked()) {
      this.requestMeasure();
    }
  }

  requestMeasure() {
    if (this.measureFrameId) return;
    this.measureFrameId = requestAnimationFrame(() => {
      this.measureFrameId = null;
      this.measureAllElements();
    });
  }

  measureAllElements() {
    const scrollTop = ScrollingElement.scrollTop || 0;

    for (const element of this.parts) {
      const item = this.partsMap.get(element);
      if (!item) continue;

      const rect = element.getBoundingClientRect();
      item.top = rect.top + scrollTop;
      item.height = rect.height;
    }

    for (const element of this.areas) {
      const item = this.areasMap.get(element);
      if (!item) continue;

      const rect = element.getBoundingClientRect();
      item.top = rect.top + scrollTop;
      item.bottom = rect.bottom + scrollTop;
    }
  }

  onIntersect(entries) {
    for (const entry of entries) {
      const item = this.areasMap.get(entry.target);
      if (!item) continue;

      if (entry.isIntersecting) {
        this.activeAreas.add(item);
      } else {
        this.activeAreas.delete(item);
      }
    }

    if (this.activeAreas.size > 0 && !this._unsubscribe) {
      this._unsubscribe = UnifiedRAF.subscribeScroll(this.animationUpdate.bind(this));
    }

    if (this.activeAreas.size === 0 && this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
  }

  animationUpdate(context) {
    if (scrollLockManager.isLocked()) return;

    const { scrollTop } = context;

    for (const element of this.parts) {
      // fixed/sticky対応：毎フレーム実際のビューポート位置を取得
      const partRect = element.getBoundingClientRect();
      const partCenterVp = partRect.top + partRect.height * 0.5;

      let invert = false;
      for (const areaItem of this.activeAreas) {
        // 絶対座標 → ビューポート座標に変換
        const areaTopVp = areaItem.top - scrollTop;
        const areaBottomVp = areaItem.bottom - scrollTop;

        if (partCenterVp >= areaTopVp && partCenterVp <= areaBottomVp) {
          invert = true;
          break;
        }
      }

      const cached = this.invertCache.get(element);
      if (cached !== invert) {
        element.classList.toggle("is-invert", invert);
        this.invertCache.set(element, invert);
      }
    }
  }

  update() {}

  disconnect() {
    if (this._unsubscribe) {
      this._unsubscribe();
      this._unsubscribe = null;
    }
    if (this.measureFrameId) {
      cancelAnimationFrame(this.measureFrameId);
    }
    if (this.intersectionObserver) {
      this.intersectionObserver.disconnect();
    }
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  }

  debug() {
    console.log("[InvertParts] parts=" + this.parts.length + " areas=" + this.areas.length + " activeAreas=" + this.activeAreas.size);
  }
}
registerModule(new InvertParts());
/* ==========================================================================
SubNavCurrent（.l-subNab カレント処理 + 横スクロール中央寄せ）
========================================================================== */
class SubNavCurrent extends BaseModule {
  constructor() {
    super("SubNavCurrent");
    this.inner = null;
    this.items = [];
    this._observer = null;
    this._lastId = null;
  }

  setup() {
    this.inner = document.querySelector(".l-subNab__inner");
    this.items = toArray(document.querySelectorAll(".l-subNab li[data-id]"));
  }

  mount() {
    if (!this.inner || !this.items.length) return;

    // 初期状態を反映
    this._update();

    // body の data-currentSection が変わるたびに更新
    //（SectionState が毎スクロールフレームで書き換えるため、
    //   MutationObserver で変化を拾うのが最も効率的）
    this._observer = new MutationObserver(() => {
      this._update();
    });
    this._observer.observe(document.body, {
      attributes: true,
      attributeFilter: ["data-currentsection"],
    });

    this._stops.push(() => {
      if (this._observer) {
        this._observer.disconnect();
        this._observer = null;
      }
    });
  }

  _update() {
    if (!this.inner || !this.items.length) return;

    const id = document.body.getAttribute("data-currentSection") || "";
    if (id === this._lastId) return;
    this._lastId = id;

    let currentItem = null;
    for (const li of this.items) {
      const liId = li.getAttribute("data-id") || "";
      if (liId === id) {
        li.classList.add("is-current");
        currentItem = li;
      } else {
        li.classList.remove("is-current");
      }
    }

    // カレント要素を .l-subNab__inner の横スクロール中央に寄せる
    if (currentItem) {
      // 進行中のスムーススクロールをキャンセルして安定した値を取得
      this.inner.scrollLeft = this.inner.scrollLeft;
      const innerRect = this.inner.getBoundingClientRect();
      const itemRect = currentItem.getBoundingClientRect();
      // 現在のスクロール量 + アイテムの相対位置 → inner上の絶対位置
      const itemAbsLeft = this.inner.scrollLeft + itemRect.left - innerRect.left;
      const scrollLeft = itemAbsLeft - this.inner.clientWidth / 2 + currentItem.clientWidth / 2;
      this.inner.scrollTo({ left: Math.max(0, scrollLeft), behavior: "smooth" });
    }
  }

  onResize() {
    // リサイズ後はキャッシュをクリアして再計算
    this._lastId = null;
    this._update();
  }

  update() {}

  debug() {
    console.log("[SubNavCurrent] current=" + this._lastId);
  }
}
registerModule(new SubNavCurrent());

/* ==========================================================================
UI Components — インタラクション・メディア・モーダル
========================================================================== */

/* ========== AnchorScroll（着地予測版・完全版） ========== */
class AnchorScroll extends BaseModule {
  constructor() {
    super("AnchorScroll");
    this.CONFIG = {
      speedPerPx: 1,
      minDuration: 1200,
      maxDuration: 2400,
      offset: 0,
      lenisDuration: 0.9,
    };
    this.links = [];
    this._handlers = new Map();
    this._isAnimating = false;
    this.opts = {
      easing: Ease.quart,
      lenisEasing: (t) => (t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2),
    };
  }

  setup() {
    const header = document.querySelector(".l-header");
    if (header) {
      this.CONFIG.offset = header.getBoundingClientRect().height;
    }
    this._refresh();
  }

  mount() {}

  onResize() {
    const header = document.querySelector(".l-header");
    if (header) {
      this.CONFIG.offset = header.getBoundingClientRect().height;
    }
    this._refresh();
  }

  update() {}

  _refresh() {
    this._unbind();
    this._collect();
    this._bind();
  }

  _collect() {
    this.links = [];
    const anchors = document.querySelectorAll('a[href*="#"]');

    for (const link of anchors) {
      if (!link) continue;
      const href = link.getAttribute("href");
      if (!href) continue;

      if (href === "#") {
        this.links.push({ link, target: null, isTop: true });
        continue;
      }

      let id = "";
      if (href.charAt(0) === "#") {
        if (href.length <= 1) continue;
        id = href.slice(1);
      } else {
        try {
          const url = new URL(href, location.href);
          if (url.pathname !== location.pathname) continue;
          if (!url.hash || url.hash.length <= 1) continue;
          id = url.hash.slice(1);
        } catch (_) {
          continue;
        }
      }

      if (!id) continue;

      let target = null;
      try {
        const decoded = decodeURIComponent(id);
        target = document.getElementById(decoded) || document.querySelector('[name="' + decoded.replace(/"/g, '\\"') + '"]');
      } catch (_) {}

      if (!target) continue;
      this.links.push({ link, target, isTop: false });
    }
  }

  _bind() {
    for (const item of this.links) {
      const link = item.link;
      const onClick = (e) => {
        e.preventDefault();
        if (this._isAnimating) return;

        if (item.isTop) {
          this.scrollToTop();
        } else if (item.target) {
          this.scrollToElement(item.target);
        }
      };

      link.addEventListener("click", onClick, false);
      this._handlers.set(link, onClick);
      this._stops.push(() => {
        try {
          link.removeEventListener("click", onClick, false);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      });
    }
  }

  _unbind() {
    if (!this._handlers.size) return;
    this._handlers.forEach((handler, link) => {
      try {
        link.removeEventListener("click", handler, false);
      } catch (_) {}
    });
    this._handlers.clear();
  }

  _triggerEarlyUpdate(targetScrollY) {
    const originalScrollTop = Environment.scrollTop;
    Environment.scrollTop = targetScrollY;

    for (const subscriber of ScrollBus) {
      try {
        subscriber(Environment);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }

    Environment.scrollTop = originalScrollTop;
  }

  scrollToTop() {
    const startY = ScrollingElement.scrollTop || 0;
    if (startY === 0) return;

    const reduceMotion = EnvironmentSupport.prefersReducedMotion;
    const targetY = 0;

    if (reduceMotion) {
      ScrollingElement.scrollTop = targetY;
      return;
    }

    // Lenis が有効な場合は lenis.scrollTo() に委譲
    if (lenisInstance) {
      lenisInstance.scrollTo(targetY, {
        duration: this.CONFIG.lenisDuration,
        easing: this.opts.lenisEasing,
      });
      return;
    }

    this._isAnimating = true;
    scrollLockManager.startScrollAnimation();

    requestAnimationFrame(() => {
      this._triggerEarlyUpdate(targetY);
    });

    const distance = Math.abs(targetY - startY);
    const rawDuration = distance * this.CONFIG.speedPerPx;
    const duration = Math.min(Math.max(rawDuration, this.CONFIG.minDuration), this.CONFIG.maxDuration);
    const startTime = performance.now();

    const tick = (now) => {
      const elapsed = now - startTime;
      const t = Math.min(elapsed / duration, 1);
      const eased = this.opts.easing(t);
      const current = startY + (targetY - startY) * eased;

      ScrollingElement.scrollTop = current;

      if (t < 1) {
        requestAnimationFrame(tick);
      } else {
        this._isAnimating = false;
        scrollLockManager.endScrollAnimation();
      }
    };

    requestAnimationFrame(tick);
  }

  scrollToElement(targetEl) {
    if (!targetEl) return;

    const reduceMotion = EnvironmentSupport.prefersReducedMotion;
    const rect = targetEl.getBoundingClientRect();
    const startY = ScrollingElement.scrollTop || 0;
    const scrollHeight = ScrollingElement.scrollHeight;
    const maxScroll = scrollHeight - window.innerHeight;

    let targetY = startY + rect.top - this.CONFIG.offset;
    targetY = Math.max(0, Math.min(targetY, maxScroll));

    if (Math.abs(startY - targetY) < 1) {
      this._setFocusSafely(targetEl);
      return;
    }

    if (reduceMotion) {
      ScrollingElement.scrollTop = targetY;
      this._setFocusSafely(targetEl);
      return;
    }

    // Lenis が有効な場合は lenis.scrollTo() に委譲
    if (lenisInstance) {
      lenisInstance.scrollTo(targetY, {
        duration: this.CONFIG.lenisDuration,
        easing: this.opts.lenisEasing,
        onComplete: () => this._setFocusSafely(targetEl),
      });
      return;
    }

    this._isAnimating = true;
    scrollLockManager.startScrollAnimation();

    requestAnimationFrame(() => {
      this._triggerEarlyUpdate(targetY);
    });

    const distance = Math.abs(targetY - startY);
    const rawDuration = distance * this.CONFIG.speedPerPx;
    const duration = Math.min(Math.max(rawDuration, this.CONFIG.minDuration), this.CONFIG.maxDuration);
    const startTime = performance.now();

    const tick = (now) => {
      const elapsed = now - startTime;
      const t = Math.min(elapsed / duration, 1);
      const eased = this.opts.easing(t);
      const current = startY + (targetY - startY) * eased;

      ScrollingElement.scrollTop = current;

      if (t < 1) {
        requestAnimationFrame(tick);
      } else {
        this._setFocusSafely(targetEl);
        this._isAnimating = false;
        scrollLockManager.endScrollAnimation();
      }
    };

    requestAnimationFrame(tick);
  }

  _setFocusSafely(element) {
    if (!element || typeof element.focus !== "function") return;

    const prevTabIndex = element.getAttribute("tabindex");
    if (element.tabIndex < 0) {
      element.setAttribute("tabindex", "-1");
    }

    try {
      element.focus({ preventScroll: true });
    } catch (_) {
      try {
        element.focus();
      } catch (_) {}
    }

    if (prevTabIndex === null) {
      element.removeAttribute("tabindex");
    } else {
      element.setAttribute("tabindex", prevTabIndex);
    }
  }

  debug() {
    console.log("[AnchorScroll] links=" + this.links.length + " offset=" + this.CONFIG.offset);
  }
}
registerModule(new AnchorScroll());

/* ========== HamburgerMenu ========== */
class HamburgerMenu extends BaseModule {
  constructor() {
    super("HamburgerMenu");
    this.CONFIG = {
      activeClass: "is-hbgOpen",
      buttonSelector: ".js-hbg__button",
      modalSelector: ".js-hbg__modal",
      insideSelectors: "a, input, button, .has-child",
    };
    this.body = null;
    this.button = null;
    this.modal = null;
    this._buttonHandler = null;
    this._modalHandler = null;
    this._scrollCloseHandler = null;
  }

  setup() {
    this.body = document.body;
    this.button = document.querySelector(this.CONFIG.buttonSelector);
    this.modal = document.querySelector(this.CONFIG.modalSelector);
  }

  mount() {
    this._bind();
  }

  onResize() {}
  update() {}

  _bind() {
    if (this.button && !this._buttonHandler) {
      this._buttonHandler = (event) => {
        event.preventDefault();
        this.toggle();
      };
      this.button.addEventListener("click", this._buttonHandler, false);
      this._stops.push(() => {
        try {
          this.button.removeEventListener("click", this._buttonHandler, false);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      });
    }

    if (this.modal && !this._modalHandler) {
      this._modalHandler = (event) => {
        const target = event.target;
        if (!target) return;
        const inside = target.closest(this.CONFIG.insideSelectors);
        if (inside && this.modal.contains(inside)) return;
        this.close();
      };
      this.modal.addEventListener("click", this._modalHandler, false);
      this._stops.push(() => {
        try {
          this.modal.removeEventListener("click", this._modalHandler, false);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      });
    }

    // スクロール（wheel / touchmove）でメニューを閉じる
    this._scrollCloseHandler = () => {
      if (this.isOpen()) this.close();
    };
    window.addEventListener("wheel", this._scrollCloseHandler, { passive: true });
    window.addEventListener("touchmove", this._scrollCloseHandler, { passive: true });
    this._stops.push(() => {
      window.removeEventListener("wheel", this._scrollCloseHandler);
      window.removeEventListener("touchmove", this._scrollCloseHandler);
    });
  }

  isOpen() {
    return this.body?.classList.contains(this.CONFIG.activeClass) || false;
  }

  open() {
    if (!this.body) return;
    this.body.classList.add(this.CONFIG.activeClass);
    if (lenisInstance) lenisInstance.stop();
  }

  close() {
    if (!this.body) return;
    this.body.classList.remove(this.CONFIG.activeClass);
    if (lenisInstance) lenisInstance.start();
  }

  toggle() {
    if (this.isOpen()) {
      this.close();
    } else {
      this.open();
    }
  }

  debug() {
    console.log("[HamburgerMenu] open=" + this.isOpen());
  }
}
registerModule(new HamburgerMenu());

/* ========== Tabs ========== */
class Tabs extends BaseModule {
  constructor() {
    super("Tabs");
    this.groups = [];
    this._handlers = [];
    this._tabData = new WeakMap();
  }

  setup() {
    this._collect();
  }

  mount() {
    this._bind();
  }

  onResize() {}
  update() {}

  _collect() {
    this._unbind();
    this.groups = [];

    const roots = toArray(document.querySelectorAll(".js-tab"));
    for (const root of roots) {
      if (!root) continue;

      const navs = toArray(root.querySelectorAll(".js-tab__nav"));
      const contents = toArray(root.querySelectorAll(".js-tab__content"));
      if (!navs.length || !contents.length) continue;

      for (const nav of navs) {
        if (!nav) continue;
        const name = nav.getAttribute("data-tab");
        if (!name) continue;

        let target = null;
        try {
          target = root.querySelector('.js-tab__content[data-tab="' + name.replace(/"/g, '\\"') + '"]');
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
        this._tabData.set(nav, { name: name, target: target || null });
      }

      this.groups.push({ root, navs, contents });
    }
  }

  _bind() {
    for (const group of this.groups) {
      const navs = group.navs;

      for (const nav of navs) {
        if (!nav) continue;

        const onClick = (event) => {
          event.preventDefault();
          if (hasClass(nav, "is-active")) return;
          this._change(group, nav);
        };

        nav.addEventListener("click", onClick, false);
        this._handlers.push({ element: nav, handler: onClick });
        this._stops.push(() => {
          try {
            nav.removeEventListener("click", onClick, false);
          } catch (error) {
            if (DEBUG) console.warn(error);
          }
        });
      }

      let hasActive = false;
      for (const nav of navs) {
        if (hasClass(nav, "is-active")) {
          hasActive = true;
          break;
        }
      }
      if (!hasActive && navs[0]) {
        this._change(group, navs[0]);
      }
    }
  }

  _unbind() {
    if (!this._handlers.length) return;
    for (const item of this._handlers) {
      if (!item?.element || !item?.handler) continue;
      try {
        item.element.removeEventListener("click", item.handler, false);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }
    this._handlers = [];
  }

  _change(group, nav) {
    if (!group || !nav) return;

    const navs = group.navs;
    const contents = group.contents;
    const data = this._tabData.get(nav);
    const target = data ? data.target : null;

    removeClass(navs, "is-active");
    removeClass(contents, "is-active");
    addClass(nav, "is-active");
    if (target) {
      addClass(target, "is-active");
    }
  }

  debug() {
    console.log("[Tabs] groups=" + this.groups.length);
  }
}
registerModule(new Tabs());

/* ========== Accordion ========== */
class Accordion extends BaseModule {
  constructor() {
    super("Accordion");
    this.groups = [];
    this._handlers = [];
  }

  setup() {
    this._collect();
  }

  mount() {
    this._bind();
  }

  onResize() {}
  update() {}

  _collect() {
    this._unbind();
    this.groups = [];

    const roots = toArray(document.querySelectorAll(".js-accordion"));
    for (const root of roots) {
      if (!root) continue;

      const head = root.querySelector(".js-accordion__head");
      const body = root.querySelector(".js-accordion__body");
      if (!head || !body) continue;

      const isActive = hasClass(head, "is-active");
      body.style.overflow = "hidden";

      if (isActive) {
        const h = body.scrollHeight;
        body.style.height = h + "px";
        body.setAttribute("data-accordion-open", "1");
      } else {
        body.style.height = "0px";
        body.removeAttribute("data-accordion-open");
      }

      this.groups.push({ root, head, body });
    }
  }

  _bind() {
    for (const group of this.groups) {
      const head = group.head;
      const body = group.body;

      const onHeadClick = (event) => {
        event.preventDefault();
        if (hasClass(head, "is-active")) {
          this._close(group);
        } else {
          this._open(group);
        }
      };

      const onBodyClick = (event) => {
        const target = event.target;
        if (!target || target.closest("a")) return;
        this._close(group);
      };

      const onTransitionEnd = (event) => {
        if (event.target !== body || event.propertyName !== "height") return;
        const openFlag = body.getAttribute("data-accordion-open");
        if (openFlag === "1") {
          body.style.height = "auto";
        }
      };

      head.addEventListener("click", onHeadClick, false);
      body.addEventListener("click", onBodyClick, false);
      body.addEventListener("transitionend", onTransitionEnd, false);

      this._handlers.push({ element: head, handler: onHeadClick });
      this._handlers.push({ element: body, handler: onBodyClick });
      this._handlers.push({ element: body, handler: onTransitionEnd });

      this._stops.push(() => {
        try {
          head.removeEventListener("click", onHeadClick, false);
          body.removeEventListener("click", onBodyClick, false);
          body.removeEventListener("transitionend", onTransitionEnd, false);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      });
    }
  }

  _unbind() {
    if (!this._handlers.length) return;
    for (const item of this._handlers) {
      if (!item?.element || !item?.handler) continue;
      try {
        item.element.removeEventListener("click", item.handler, false);
        item.element.removeEventListener("transitionend", item.handler, false);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }
    this._handlers = [];
  }

  _open(group) {
    if (!group) return;

    const head = group.head;
    const body = group.body;

    addClass(head, "is-active");

    const isAuto = !body.style.height || body.style.height === "auto";
    let startHeight = 0;
    if (!isAuto) {
      startHeight = body.getBoundingClientRect().height;
    }

    const targetHeight = body.scrollHeight;
    body.style.overflow = "hidden";
    body.style.height = startHeight + "px";
    void body.offsetHeight;
    body.style.height = targetHeight + "px";
    body.setAttribute("data-accordion-open", "1");
  }

  _close(group) {
    if (!group) return;

    const head = group.head;
    const body = group.body;

    removeClass(head, "is-active");

    let currentHeight = body.getBoundingClientRect().height;
    if (!currentHeight && body.scrollHeight) {
      currentHeight = body.scrollHeight;
    }

    body.style.overflow = "hidden";
    body.style.height = currentHeight + "px";
    void body.offsetHeight;
    body.style.height = "0px";
    body.removeAttribute("data-accordion-open");
  }

  debug() {
    console.log("[Accordion] groups=" + this.groups.length);
  }
}
registerModule(new Accordion());

/* ========== GoogleMap ========== */
class GoogleMap extends BaseModule {
  constructor(element) {
    super("GoogleMap");
    this.CONFIG = {
      zoom: 16,
      pinSize: 56,
      style: [{ stylers: [{ saturation: -100 }] }],
    };
    this.root = element;
    this.wrap = null;
    this.lat = 0;
    this.lng = 0;
    this.pin = "";
    this._mapInstance = null;
    this._marker = null;
  }

  setup() {
    if (!this.root) return;

    this.wrap = this.root.querySelector(".js-map__wrap");
    const lat = this.root.getAttribute("data-lat");
    const lng = this.root.getAttribute("data-lng");
    const pin = this.root.getAttribute("data-pin");

    this.lat = lat ? Number(lat) : 0;
    this.lng = lng ? Number(lng) : 0;
    this.pin = pin || "";
  }

  mount() {
    this._initMap();
  }

  _initMap() {
    if (!this.wrap) return;

    if (!window.google?.maps || typeof google.maps.Map !== "function") {
      if (DEBUG) console.warn("Google Maps API is not available");
      this.root.classList.add("is-mapError");
      this._showErrorMessage();
      return;
    }

    try {
      const pos = { lat: this.lat, lng: this.lng };
      const map = new google.maps.Map(this.wrap, {
        center: pos,
        zoom: this.CONFIG.zoom,
        panControl: false,
        zoomControl: true,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: false,
        rotateControl: false,
        fullscreenControl: false,
        styles: this.CONFIG.style,
      });

      const markerOptions = { map, position: pos };
      if (this.pin) {
        markerOptions.icon = {
          url: this.pin,
          scaledSize: new google.maps.Size(this.CONFIG.pinSize, this.CONFIG.pinSize),
        };
      }

      this._marker = new google.maps.Marker(markerOptions);
      this._mapInstance = map;
    } catch (error) {
      if (DEBUG) console.warn(error);
      this.root.classList.add("is-mapError");
      this._showErrorMessage();
    }
  }

  _showErrorMessage() {
    if (!this.wrap) return;
    const div = document.createElement("div");
    div.className = "map-error";
    div.textContent = "地図を読み込めませんでした。";
    div.style.cssText = "padding:1rem;text-align:center;color:#666";
    this.wrap.appendChild(div);
  }

  debug() {
    console.log("[GoogleMap] lat=" + this.lat + " lng=" + this.lng);
  }
}

(function InitGoogleMaps() {
  const maps = toArray(document.querySelectorAll(".js-map"));
  for (const element of maps) {
    if (!element) continue;
    const instance = new GoogleMap(element);
    registerModule(instance);
  }
})();

/* ========== SlideShow（IntersectionObserver対応） ========== */
class SlideShow extends BaseModule {
  constructor(root) {
    super("SlideShow");
    this.CONFIG = {
      defaultInterval: 4000,
    };
    this.root = root;
    this.list = null;
    this.items = [];
    this.length = 0;
    this.index = 0;
    this.interval = this.CONFIG.defaultInterval;
    this.delay = 0;
    this.dotsEnabled = false;
    this.arrowEnabled = false;
    this._autoplayEnabled = !EnvironmentSupport.prefersReducedMotion;
    this._timerId = 0;
    this._hasStarted = false;
    this._isVisible = false;
    this._dots = [];
    this._arrows = [];
    this._ioStop = null;
  }

  setup() {
    if (!this.root) return;

    this.list = this.root.querySelector(".js-slide__ul");
    if (!this.list) return;

    this.items = toArray(this.list.querySelectorAll(".js-slide__li"));
    this.length = this.items.length;
    if (!this.length) return;

    const intervalAttr = this.root.getAttribute("data-interval");
    if (intervalAttr) {
      const parsed = parseInt(intervalAttr, 10);
      if (parsed > 0) this.interval = parsed;
    }

    const delayAttr = this.root.getAttribute("data-delay");
    if (delayAttr) {
      const parsed = parseInt(delayAttr, 10);
      if (parsed >= 0) this.delay = parsed;
    } else {
      this.delay = this.interval;
    }

    this.dotsEnabled = this._readBooleanAttr("data-dots", false);
    this.arrowEnabled = this._readBooleanAttr("data-arrow", false);

    if (this.dotsEnabled || this.arrowEnabled) {
      this._buildControls();
    }

    removeClass(this.items, "is-active");
    if (this.items[0]) {
      addClass(this.items[0], "is-active");
    }
    if (this.dotsEnabled && this._dots[0]) {
      addClass(this._dots[0], "is-active");
    }
  }

  mount() {
    this._bindControls();

    this._ioStop = IntersectionObserverHub.observe(this.root, (entry) => {
      this._isVisible = entry.isIntersecting;
      this._onVisibilityChange(this._isVisible);
      if (this._isVisible) {
        addClass(this.root, "is-active");
      } else {
        removeClass(this.root, "is-active");
      }
    });
  }

  onResize() {}
  update() {}

  _readBooleanAttr(name, defaultValue) {
    const value = this.root.getAttribute(name);
    if (value === null || value === "") return defaultValue;
    if (value === "true") return true;
    if (value === "false") return false;
    try {
      return Boolean(JSON.parse(value));
    } catch (_) {
      return defaultValue;
    }
  }

  _buildControls() {
    const ctrl = document.createElement("div");
    addClass(ctrl, "js-slide__ctrl");
    const wrap = document.createElement("div");
    addClass(wrap, "js-slide__ctrl__wrap");

    if (this.arrowEnabled) {
      const prev = document.createElement("button");
      addClass(prev, ["js-slide__arrow", "is-prev"]);
      prev.type = "button";
      prev.textContent = "PREV";
      prev.setAttribute("aria-label", "Previous slide");
      wrap.appendChild(prev);
    }

    if (this.dotsEnabled) {
      const ul = document.createElement("ul");
      addClass(ul, "js-slide__dots");
      for (let i = 0; i < this.length; i++) {
        const li = document.createElement("li");
        const button = document.createElement("button");
        button.type = "button";
        button.textContent = String(i + 1);
        button.setAttribute("aria-label", "Slide " + (i + 1));
        li.appendChild(button);
        ul.appendChild(li);
      }
      wrap.appendChild(ul);
    }

    if (this.arrowEnabled) {
      const next = document.createElement("button");
      addClass(next, ["js-slide__arrow", "is-next"]);
      next.type = "button";
      next.textContent = "NEXT";
      next.setAttribute("aria-label", "Next slide");
      wrap.appendChild(next);
    }

    ctrl.appendChild(wrap);
    this.root.appendChild(ctrl);

    if (this.dotsEnabled) {
      this._dots = toArray(this.root.querySelectorAll(".js-slide__dots li"));
    }
    if (this.arrowEnabled) {
      this._arrows = toArray(this.root.querySelectorAll(".js-slide__arrow"));
    }
  }

  _bindControls() {
    if (this.dotsEnabled && this._dots.length) {
      for (let i = 0; i < this._dots.length; i++) {
        const dot = this._dots[i];
        if (!dot) continue;

        const onDotClick = (event) => {
          event.preventDefault();
          this._goTo(i, true);
        };

        dot.addEventListener("click", onDotClick, false);
        this._stops.push(() => {
          try {
            dot.removeEventListener("click", onDotClick, false);
          } catch (error) {
            if (DEBUG) console.warn(error);
          }
        });
      }
    }

    if (this.arrowEnabled && this._arrows.length) {
      for (const arrow of this._arrows) {
        if (!arrow) continue;

        const onArrowClick = (event) => {
          event.preventDefault();
          if (hasClass(arrow, "is-next")) {
            this.next(true);
          } else {
            this.prev(true);
          }
        };

        arrow.addEventListener("click", onArrowClick, false);
        this._stops.push(() => {
          try {
            arrow.removeEventListener("click", onArrowClick, false);
          } catch (error) {
            if (DEBUG) console.warn(error);
          }
        });
      }
    }
  }

  _onVisibilityChange(visible) {
    if (!this._autoplayEnabled) return;
    if (!this.length || this.length <= 1) return;

    if (visible) {
      this._restartTimer(!this._hasStarted);
    } else {
      this._clearTimer();
    }
  }

  _restartTimer(useDelay) {
    this._clearTimer();
    if (!this._autoplayEnabled || !this._isVisible) return;
    if (!this.length || this.length <= 1) return;

    let wait = this.interval;
    if (useDelay) {
      wait = this.delay > 0 ? this.delay : this.interval;
    }

    this._timerId = window.setTimeout(() => {
      this.next(false);
    }, wait);
  }

  _clearTimer() {
    if (this._timerId) {
      window.clearTimeout(this._timerId);
      this._timerId = 0;
    }
  }

  _goTo(index, fromUser) {
    if (!this.length) return;

    index = Math.max(0, Math.min(index, this.length - 1));

    if (index === this.index && this._hasStarted) {
      if (fromUser) this._restartTimer(false);
      return;
    }

    this.index = index;
    removeClass(this.items, "is-active");
    const currentItem = this.items[this.index];
    if (currentItem) {
      addClass(currentItem, "is-active");
    }

    if (this.dotsEnabled && this._dots.length) {
      removeClass(this._dots, "is-active");
      const currentDot = this._dots[this.index];
      if (currentDot) {
        addClass(currentDot, "is-active");
      }
    }

    this._hasStarted = true;
    if (this._autoplayEnabled && this._isVisible) {
      this._restartTimer(false);
    } else {
      this._clearTimer();
    }
  }

  next(fromUser) {
    if (!this.length) return;
    let nextIndex = this.index + 1;
    if (nextIndex >= this.length) nextIndex = 0;
    this._goTo(nextIndex, fromUser);
  }

  prev(fromUser) {
    if (!this.length) return;
    let prevIndex = this.index - 1;
    if (prevIndex < 0) prevIndex = this.length - 1;
    this._goTo(prevIndex, fromUser);
  }

  debug() {
    console.log("[SlideShow] visible=" + this._isVisible + " length=" + this.length);
  }
}

(function InitSlideShow() {
  const roots = toArray(document.querySelectorAll(".js-slide"));
  for (const root of roots) {
    if (!root) continue;
    const instance = new SlideShow(root);
    registerModule(instance);
  }
})();

/* ========== SplideController（IntersectionObserver対応） ========== */
class SplideController extends BaseModule {
  constructor() {
    super("SplideController");
    this.items = [];
  }

  setup() {
    const roots = toArray(document.querySelectorAll(".js-splide"));
    if (!roots.length) return;

    /* ------------------------------
      1) 同期ペアを先に拾う（汎用）
      - 親 .js-splide-pair の中に
        .is-item-main と .is-item-thumbs が両方ある場合のみ同期
      - 同期対象はこの段階で mount まで完了
      - 同期に使った要素は通常初期化から除外する
    ------------------------------ */
    const pairedElements = new Set();
    const pairRoots = toArray(document.querySelectorAll(".js-splide-pair"));

    for (const pairRoot of pairRoots) {
      if (!pairRoot) continue;

      const mainEl = pairRoot.querySelector(".js-splide.is-item-main");
      const thumbsEl = pairRoot.querySelector(".js-splide.is-item-thumbs");
      if (!mainEl || !thumbsEl) continue;

      // options
      const mainOptions = this._readOptions(mainEl);
      const thumbsOptions = this._readOptions(thumbsEl);

      const main = new Splide(mainEl, mainOptions);
      const thumbs = new Splide(thumbsEl, thumbsOptions);

      // sync
      main.sync(thumbs);

      // isNavigation: true のthumbsはmain mount後でないとgetAtエラーになる
      this._mountSplide(main, mainOptions);
      this._mountSplide(thumbs, thumbsOptions);

      // items登録（IO・autoplay制御を効かせる）ペアはoverflowハンドラーをスキップ
      this._registerItem(mainEl, main, mainOptions, true);
      this._registerItem(thumbsEl, thumbs, thumbsOptions, true);

      pairedElements.add(mainEl);
      pairedElements.add(thumbsEl);
    }

    /* ------------------------------
      2) 通常のsplide（単体）を初期化
      - 同期で使った要素はスキップ
    ------------------------------ */
    for (const root of roots) {
      if (!root) continue;
      if (pairedElements.has(root)) continue;

      const options = this._readOptions(root);
      const splide = new Splide(root, options);

      this._mountSplide(splide, options);
      this._registerItem(root, splide, options);
    }
  }

  mount() {}
  onResize() {}
  update() {}

  /* ==============================
    helpers
  ============================== */

  _readOptions(root) {
    const attr = root.getAttribute("data-splide");
    if (!attr) return {};
    try {
      return JSON.parse(attr);
    } catch (error) {
      if (DEBUG) console.warn(error);
      return {};
    }
  }

  _mountSplide(splide, options) {
    if (options?.autoScroll) {
      try {
        splide.mount({ AutoScroll });
      } catch (error) {
        if (DEBUG) console.warn(error);
        splide.mount();
      }
    } else {
      splide.mount();
    }
  }

  _registerItem(root, splide, options, skipOverflow = false) {
    const item = {
      root,
      splide,
      options,
      inView: false,
      ioStop: null,
    };

    splide.on("mounted", () => {
      this._updatePlayback(item);
    });

    if (!skipOverflow)
      splide.on("overflow", (isOverflow) => {
        if (!isOverflow) {
          try {
            splide.go(0);
            const nextOptions = { ...splide.options };
            nextOptions.arrows = isOverflow;
            nextOptions.pagination = isOverflow;
            nextOptions.drag = isOverflow;
            if (nextOptions.type !== "loop") {
              nextOptions.clones = 0;
            }
            splide.options = nextOptions;
          } catch (error) {
            if (DEBUG) console.warn(error);
          }
        }
      });

    item.ioStop = IntersectionObserverHub.observe(root, (entry) => {
      item.inView = entry.isIntersecting;
      this._updatePlayback(item);
    });

    this.items.push(item);
  }

  _updatePlayback(item) {
    const splide = item.splide;
    const options = item.options || {};
    const components = splide.Components || {};
    const hasAutoScroll = options.autoScroll && components.AutoScroll;
    const hasAutoplay = options.autoplay && components.Autoplay;

    if (!hasAutoScroll && !hasAutoplay) return;

    if (item.inView) {
      if (hasAutoScroll) {
        try {
          if (components.AutoScroll.isPaused()) components.AutoScroll.play();
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      } else if (hasAutoplay) {
        try {
          if (components.Autoplay.isPaused()) components.Autoplay.play();
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      }
    } else {
      if (hasAutoScroll) {
        try {
          if (!components.AutoScroll.isPaused()) components.AutoScroll.pause();
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      } else if (hasAutoplay) {
        try {
          if (!components.Autoplay.isPaused()) components.Autoplay.pause();
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      }
    }
  }

  debug() {
    console.log("[SplideController] items=" + this.items.length);
  }
}
registerModule(new SplideController());

/* ========== LazyBackground（is-preloadのみ待機版） ========== */
class LazyBackground extends BaseModule {
  constructor() {
    super("LazyBackground");
    this.CONFIG = {
      initDelay: 50,
      outOfViewDelay: 200,
    };
    this.targets = [];
    this.loading = new Set();
    this.loaded = new Set();
    this.preloadTargets = [];
    this._allPreloadLoaded = false;
    this._completeFired = false;
  }

  setup() {
    this.targets = toArray(document.querySelectorAll(".js-lazyImage__bgi[data-src]"));
  }

  mount() {
    if (!this.targets?.length) {
      this._notifyComplete();
      return;
    }

    setTimeout(() => {
      this._startLoading();
    }, this.CONFIG.initDelay);
  }

  onResize() {}
  update() {}

  _startLoading() {
    if (!this.targets?.length) return;

    const preloadTargets = [];
    const inViewTargets = [];
    const outViewTargets = [];

    for (const el of this.targets) {
      if (!el) continue;

      const parent = el.closest(".js-lazyImage");
      const isPreload = parent?.classList.contains("is-preload");

      if (isPreload) {
        preloadTargets.push(el);
      } else {
        const rect = el.getBoundingClientRect();
        const isInView = rect.top < window.innerHeight && rect.bottom > 0;

        if (isInView) {
          inViewTargets.push(el);
        } else {
          outViewTargets.push(el);
        }
      }
    }

    this.preloadTargets = preloadTargets;

    if (!preloadTargets.length) {
      this._notifyComplete();
    }

    for (const el of preloadTargets) {
      this._loadWithPreload(el, true);
    }

    for (const el of inViewTargets) {
      this._loadWithPreload(el, false);
    }

    if (outViewTargets.length) {
      setTimeout(() => {
        for (const el of outViewTargets) {
          this._loadWithPreload(el, false);
        }
      }, this.CONFIG.outOfViewDelay);
    }

    this.targets = [];
  }

  _loadWithPreload(el, isPriority = false) {
    if (!el) return;
    if (this.loading.has(el) || this.loaded.has(el)) return;

    const src = el.getAttribute("data-src");
    if (!src) return;

    this.loading.add(el);

    const img = new Image();

    img.onload = () => {
      try {
        el.style.backgroundImage = 'url("' + src.replace(/"/g, '\\"') + '")';
      } catch (e) {
        if (DEBUG) console.warn(e);
      }

      el.removeAttribute("data-src");

      const parent = el.closest(".js-lazyImage");
      if (parent) {
        parent.classList.add("is-lazyLoad");
      }

      this.loading.delete(el);
      this.loaded.add(el);

      if (isPriority) {
        this._checkPreloadComplete();
      }
    };

    img.onerror = () => {
      if (DEBUG) console.warn("[LazyBackground] Failed to load:", src);
      el.removeAttribute("data-src");
      this.loading.delete(el);
      this.loaded.add(el);

      if (isPriority) {
        this._checkPreloadComplete();
      }
    };

    img.src = src;
  }

  _checkPreloadComplete() {
    if (this._allPreloadLoaded) return;

    let preloadLoaded = 0;
    for (const el of this.preloadTargets) {
      if (this.loaded.has(el)) {
        preloadLoaded++;
      }
    }

    if (preloadLoaded >= this.preloadTargets.length) {
      this._allPreloadLoaded = true;
      this._notifyComplete();
    }
  }

  _notifyComplete() {
    if (this._completeFired) return;

    this._completeFired = true;

    const event = new CustomEvent("lazybackground:complete", {
      detail: {
        preloadCount: this.preloadTargets.length,
        loadedCount: this.loaded.size,
      },
    });
    document.dispatchEvent(event);
  }

  debug() {
    const preload = this.preloadTargets.length;
    const loading = this.loading.size;
    const loaded = this.loaded.size;
    console.log("[LazyBackground] preload=" + preload + " loading=" + loading + " loaded=" + loaded);
  }
}
registerModule(new LazyBackground());

/* ========== YouTubePlayers（最適化版） ========== */
let YouTubePlayersInstance = null;
let YouTubeApiReady = false;

function loadYouTubeAPI() {
  if (YouTubeApiReady || document.querySelector('script[src*="youtube.com/player_api"]')) {
    return;
  }
  const tag = document.createElement("script");
  tag.src = "https://www.youtube.com/player_api";
  document.body.appendChild(tag);
}

window.onYouTubePlayerAPIReady = function () {
  YouTubeApiReady = true;
  if (YouTubePlayersInstance?.handleApiReady) {
    YouTubePlayersInstance.handleApiReady();
  }
};

class YouTubePlayers extends BaseModule {
  constructor() {
    super("YouTubePlayers");
    this.CONFIG = {
      defaultRatio: 56.25,
      defaultControls: 1,
      defaultMute: 1,
      defaultLoop: 0,
    };
    this.items = [];
    YouTubePlayersInstance = this;
  }

  setup() {
    const roots = toArray(document.querySelectorAll(".js-youtube"));
    if (!roots.length) return;

    for (let i = 0; i < roots.length; i++) {
      const root = roots[i];
      if (!root) continue;

      const videoId = root.getAttribute("data-id");
      if (!videoId) continue;

      const item = {
        root,
        videoId,
        playerId: "YT-" + videoId + "_" + i,
        ratio: this.CONFIG.defaultRatio,
        controls: this.CONFIG.defaultControls,
        mute: this.CONFIG.defaultMute,
        loop: this.CONFIG.defaultLoop,
        auto: false,
        coverElement: null,
        player: null,
        ready: false,
        playing: false,
        ended: false,
        inView: false,
        ioStop: null,
      };

      const ratioAttr = root.getAttribute("data-ratio");
      if (ratioAttr) {
        const parsed = parseFloat(ratioAttr);
        if (!isNaN(parsed) && parsed > 0) item.ratio = parsed;
      }

      const controlsAttr = root.getAttribute("data-controls");
      if (controlsAttr) {
        try {
          item.controls = JSON.parse(controlsAttr) ? 1 : 0;
        } catch (_) {}
      }

      const muteAttr = root.getAttribute("data-mute");
      if (muteAttr) {
        try {
          item.mute = JSON.parse(muteAttr) ? 1 : 0;
        } catch (_) {}
      }

      const loopAttr = root.getAttribute("data-loop");
      if (loopAttr) {
        try {
          item.loop = JSON.parse(loopAttr) ? 1 : 0;
        } catch (_) {}
      }

      const autoAttr = root.getAttribute("data-auto");
      if (autoAttr) {
        try {
          if (JSON.parse(autoAttr)) {
            item.auto = true;
            item.loop = 1;
            item.mute = 1;
            item.controls = 0;
          }
        } catch (_) {}
      }

      const coverAttr = root.getAttribute("data-cover");
      if (coverAttr && !item.auto) {
        const cover = document.createElement("div");
        cover.className = "js-youtube__cover";
        cover.style.backgroundImage = "url(" + coverAttr + ")";
        root.appendChild(cover);
        item.coverElement = cover;
      }

      const playerWrap = document.createElement("div");
      playerWrap.setAttribute("id", item.playerId);
      root.appendChild(playerWrap);

      item.ioStop = IntersectionObserverHub.observe(root, (entry) => {
        item.inView = entry.isIntersecting;
        this._handleVisibility(item);
      });

      this.items.push(item);
    }

    if (this.items.length) {
      loadYouTubeAPI();
    }
  }

  mount() {}
  onResize() {
    for (const item of this.items) {
      this._updateItemSize(item);
    }
  }
  update() {}

  handleApiReady() {
    for (const item of this.items) {
      if (!item?.playerId || item.player) continue;
      if (typeof YT === "undefined" || typeof YT.Player !== "function") continue;

      try {
        item.player = new YT.Player(item.playerId, {
          width: 0,
          height: 0,
          videoId: item.videoId,
          playerVars: {
            autohide: 1,
            controls: item.controls,
            loop: item.loop,
            mute: item.mute,
            modestbranding: 1,
            iv_load_policy: 3,
            showinfo: 0,
            rel: 0,
            autoplay: item.auto ? 1 : 0,
            wmode: "transparent",
            origin: location.origin,
          },
          events: {
            onReady: (event) => this._onPlayerReady(item, event),
            onStateChange: (event) => this._onPlayerStateChange(item, event),
          },
        });
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }
  }

  _onPlayerReady(item, event) {
    item.ready = true;
    this._updateItemSize(item);

    if (item.coverElement) {
      item.coverElement.addEventListener(
        "click",
        (ev) => {
          ev.preventDefault();
          this._play(item);
        },
        false,
      );
    }

    if (item.auto) {
      this._handleVisibility(item);
    }
  }

  _onPlayerStateChange(item, event) {
    const state = event.data;
    const playerState = typeof YT !== "undefined" ? YT.PlayerState : null;

    if (playerState) {
      if (state === playerState.PAUSED) {
        item.playing = false;
        item.ended = false;
      }
      if (state === playerState.ENDED) {
        if (!item.loop) {
          item.playing = false;
          item.ended = true;
        } else {
          item.playing = true;
          item.ended = false;
          this._play(item);
        }
      }
      if (state === playerState.BUFFERING || state === playerState.PLAYING) {
        item.playing = true;
        item.ended = false;
      }
    }

    if (item.playing) {
      addClass(item.root, "is-play");
    }
    if (item.ended) {
      removeClass(item.root, "is-play");
    }
  }

  _handleVisibility(item) {
    if (!item.player || !item.ready) return;

    if (!item.inView && item.playing) {
      this._pause(item);
    }

    if (item.auto && item.inView && !item.playing) {
      this._play(item);
    }
  }

  _updateItemSize(item) {
    if (!item.player) return;
    try {
      const width = item.root.offsetWidth;
      const height = width * (item.ratio / 100);
      item.player.setSize(width, height);
    } catch (error) {
      if (DEBUG) console.warn(error);
    }
  }

  _play(item) {
    if (!item.player || !item.ready) return;
    try {
      item.player.playVideo();
    } catch (error) {
      if (DEBUG) console.warn(error);
    }
  }

  _pause(item) {
    if (!item.player || !item.ready) return;
    try {
      item.player.pauseVideo();
    } catch (error) {
      if (DEBUG) console.warn(error);
    }
  }

  debug() {
    console.log("[YouTubePlayers] items=" + this.items.length);
  }
}
registerModule(new YouTubePlayers());

/* ========== VideoPlayers（IntersectionObserver対応） ========== */
class VideoPlayers extends BaseModule {
  constructor() {
    super("VideoPlayers");
    this.items = [];
  }

  setup() {
    const roots = toArray(document.querySelectorAll(".js-video"));
    if (!roots.length) return;

    for (const root of roots) {
      if (!root) continue;

      const playerWrap = root.querySelector(".js-video__player");
      const video = playerWrap ? playerWrap.querySelector("video") : root.querySelector("video");
      if (!video) continue;

      const srcAttr = root.getAttribute("data-src");
      if (srcAttr) {
        const source = document.createElement("source");
        source.setAttribute("type", "video/mp4");
        source.setAttribute("src", srcAttr);
        try {
          video.appendChild(source);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      }

      const item = {
        root,
        video,
        inView: false,
        ioStop: null,
      };

      this._bindItemEvents(item);

      item.ioStop = IntersectionObserverHub.observe(root, (entry) => {
        item.inView = entry.isIntersecting;
        this._handleVisibility(item);
      });

      this.items.push(item);

      if (!video.autoplay) {
        addClass(root, "is-paused");
      } else {
        this._pause(item);
      }
    }
  }

  mount() {}
  onResize() {}
  update() {}

  _bindItemEvents(item) {
    const root = item.root;
    const video = item.video;

    video.addEventListener(
      "pause",
      () => {
        if (!video.autoplay) {
          addClass(root, "is-paused");
        }
      },
      false,
    );

    video.addEventListener(
      "ended",
      () => {
        if (!video.autoplay) {
          addClass(root, "is-paused");
        }
        try {
          video.currentTime = 0;
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      },
      false,
    );

    video.addEventListener(
      "play",
      () => {
        if (!video.autoplay) {
          removeClass(root, "is-paused");
        }
      },
      false,
    );

    root.addEventListener(
      "click",
      (event) => {
        if (!video.autoplay) {
          if (hasClass(root, "is-paused")) {
            this._play(item);
          } else {
            this._pause(item);
          }
        } else {
          event.preventDefault();
        }
      },
      false,
    );

    root.addEventListener(
      "contextmenu",
      (event) => {
        event.preventDefault();
      },
      false,
    );
  }

  _handleVisibility(item) {
    const video = item.video;
    if (!video) return;

    if (item.inView) {
      if (video.paused && video.autoplay) {
        this._play(item);
      }
    } else {
      if (!video.paused) {
        this._pause(item);
      }
    }
  }

  _play(item) {
    const video = item.video;
    try {
      const promise = video.play();
      if (promise?.then) {
        promise.catch(() => {});
      }
    } catch (error) {
      if (DEBUG) console.warn(error);
    }
  }

  _pause(item) {
    const video = item.video;
    try {
      if (!video.paused) {
        video.pause();
      }
    } catch (error) {
      if (DEBUG) console.warn(error);
    }
  }

  debug() {
    console.log("[VideoPlayers] items=" + this.items.length);
  }
}
registerModule(new VideoPlayers());

/* ========== TileEqualizer（ResizeObserver + Dirty Flag） ========== */
class TileEqualizer extends BaseModule {
  constructor() {
    super("TileEqualizer");
    this.groups = [];
    this._resizeObserver = null;
  }

  setup() {
    this._collect();

    if (typeof ResizeObserver !== "undefined") {
      this._resizeObserver = new ResizeObserver(() => {
        this.markDirty();
        this.update();
      });

      for (const group of this.groups) {
        if (group.root) {
          this._resizeObserver.observe(group.root);
        }
      }
    }
  }

  mount() {
    this.update();
  }

  onResize() {
    this.markDirty();
    this.update();
  }

  update() {
    if (!this.isDirty()) return;

    this._collect();
    if (!this.groups.length) return;

    for (const group of this.groups) {
      if (!group?.items.length) continue;
      this._applyGroup(group);
    }

    this.clearDirty();
  }

  _collect() {
    this.groups = [];
    const roots = toArray(document.querySelectorAll(".js-tile"));
    if (!roots.length) return;

    for (const root of roots) {
      if (!root) continue;

      const items = toArray(root.querySelectorAll(".js-tile__item"));
      if (!items.length) continue;

      const numAll = this._parseNum(root.getAttribute("data-tile-num"));
      const numPc = this._parseNum(root.getAttribute("data-tile-pc"));
      const numTb = this._parseNum(root.getAttribute("data-tile-tb"));
      const numSp = this._parseNum(root.getAttribute("data-tile-sp"));

      this.groups.push({ root, items, numAll, numPc, numTb, numSp });
    }
  }

  _parseNum(value) {
    if (!value && value !== "0") return 0;
    const parsed = parseInt(value, 10);
    return isNaN(parsed) || parsed < 0 ? 0 : parsed;
  }

  _getNumForMedia(group) {
    if (group.numAll > 0) return group.numAll;

    const media = getMedia();
    if (media === "PC" && group.numPc > 0) return group.numPc;
    if (media === "TB" && group.numTb > 0) return group.numTb;
    if (media === "SP" && group.numSp > 0) return group.numSp;

    return 0;
  }

  _applyGroup(group) {
    const items = group.items;
    const len = items.length;
    if (!len) return;

    for (const el of items) {
      if (el) el.style.height = "auto";
    }

    const num = this._getNumForMedia(group);
    if (!num || num <= 1) return;

    const maxArray = [];
    let rowHeights = [];

    for (let i = 0; i < len; i++) {
      const el = items[i];
      if (!el) continue;

      if (i % num === 0) {
        if (rowHeights.length) {
          maxArray.push(Math.max(...rowHeights));
        }
        rowHeights = [];
      }

      rowHeights.push(el.offsetHeight);
    }

    if (rowHeights.length) {
      maxArray.push(Math.max(...rowHeights));
    }

    let currentMax = 0;
    for (let i = 0; i < len; i++) {
      if (i % num === 0) {
        const rowIndex = Math.floor(i / num);
        currentMax = maxArray[rowIndex] || 0;
      }
      const el = items[i];
      if (!el) continue;

      if (currentMax > 0) {
        el.style.height = currentMax + "px";
      } else {
        el.style.height = "auto";
      }
    }
  }

  debug() {
    console.log("[TileEqualizer] groups=" + this.groups.length);
  }
}
registerModule(new TileEqualizer());

/* ========== FormValidation ========== */
class FormValidation extends BaseModule {
  constructor() {
    super("FormValidation");
    this.targets = [];
    this._handlers = [];
  }

  setup() {
    this._collect();
  }

  mount() {
    this._bind();
  }

  onResize() {
    this._collect();
    this._bind();
  }

  update() {}

  _collect() {
    this._unbind();
    this.targets = [];
    const nodes = document.querySelectorAll("form.input input, form.input select, form.input textarea");
    this.targets = toArray(nodes);
  }

  _bind() {
    for (const el of this.targets) {
      if (!el) continue;

      const onChange = () => this._inputCheck(el);
      const onBlur = () => this._inputCheck(el);

      el.addEventListener("change", onChange, false);
      el.addEventListener("blur", onBlur, false);

      this._handlers.push({ element: el, type: "change", handler: onChange });
      this._handlers.push({ element: el, type: "blur", handler: onBlur });

      this._stops.push(() => {
        try {
          el.removeEventListener("change", onChange, false);
          el.removeEventListener("blur", onBlur, false);
        } catch (error) {
          if (DEBUG) console.warn(error);
        }
      });
    }
  }

  _unbind() {
    if (!this._handlers.length) return;
    for (const item of this._handlers) {
      if (!item?.element || !item?.handler || !item?.type) continue;
      try {
        item.element.removeEventListener(item.type, item.handler, false);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }
    this._handlers = [];
  }

  _inputCheck(element) {
    if (!element) return;

    const parent = element.closest("div");
    if (!parent) return;

    let errorMessage = "";
    const oldError = parent.querySelector(".error");
    if (oldError?.parentNode === parent) {
      try {
        parent.removeChild(oldError);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }

    const value = element.value != null ? String(element.value) : "";
    if (element.required && value === "") {
      errorMessage = "必須項目です。入力をしてください。";
    } else if (element.type === "email" && value !== "") {
      const emailPattern = /.+@.+\..+/;
      if (!emailPattern.test(value)) {
        errorMessage = "メールアドレスの形式をご確認ください。";
      }
    }

    if (errorMessage) {
      const span = document.createElement("span");
      span.setAttribute("class", "error");
      span.textContent = errorMessage;
      try {
        parent.appendChild(span);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
    }
  }

  debug() {
    console.log("[FormValidation] targets=" + this.targets.length);
  }
}
registerModule(new FormValidation());

/* ==========================================================================
ModalModule（スクロールロック統合版）
========================================================================== */
const ModalModule = (function () {
  let isInitialized = false;
  let isOpen = false;
  let modalElement = null;
  let wrapElement = null;
  let innerElement = null;
  let contentElement = null;
  let currentTrigger = null;
  let currentType = null;

  function init() {
    if (isInitialized) return;

    const triggers = document.querySelectorAll(".js-modal__open");
    if (!triggers.length) return;

    ensureShell();
    cacheElements();
    bindOpenTriggers();
    bindCloseHandlers();
    bindKeydown();
    isInitialized = true;
  }

  function refreshTriggers() {
    bindOpenTriggers();
  }

  function ensureShell() {
    modalElement = document.querySelector(".js-modal");
    if (modalElement) return;

    modalElement = document.createElement("div");
    modalElement.className = "js-modal";
    modalElement.setAttribute("data-modal", "default");

    const wrap = document.createElement("div");
    wrap.className = "js-modal__wrap";

    const inner = document.createElement("div");
    inner.className = "js-modal__inner";

    const closeHead = document.createElement("div");
    closeHead.className = "js-modal__close js-modal__close__head";
    closeHead.appendChild(document.createElement("span"));

    const content = document.createElement("div");
    content.className = "js-modal__content";

    const closeFoot = document.createElement("div");
    closeFoot.className = "js-modal__close js-modal__close__foot";
    closeFoot.appendChild(document.createElement("span"));

    inner.appendChild(closeHead);
    inner.appendChild(content);
    inner.appendChild(closeFoot);
    wrap.appendChild(inner);
    modalElement.appendChild(wrap);
    document.body.appendChild(modalElement);
  }

  function cacheElements() {
    modalElement = document.querySelector(".js-modal");
    if (!modalElement) return;
    wrapElement = modalElement.querySelector(".js-modal__wrap");
    innerElement = modalElement.querySelector(".js-modal__inner");
    contentElement = modalElement.querySelector(".js-modal__content");
  }

  function bindOpenTriggers() {
    const triggers = document.querySelectorAll(".js-modal__open");
    for (const trigger of triggers) {
      if (trigger.getAttribute("data-modal-bound") === "1") continue;
      trigger.setAttribute("data-modal-bound", "1");
      trigger.addEventListener("click", handleTriggerClick, false);
    }
  }

  function bindCloseHandlers() {
    if (!modalElement) return;

    const closes = modalElement.querySelectorAll(".js-modal__close");
    for (const close of closes) {
      close.addEventListener(
        "click",
        (event) => {
          event.preventDefault();
          closeModal();
        },
        false,
      );
    }

    if (wrapElement) {
      wrapElement.addEventListener(
        "click",
        (event) => {
          const target = event.target;
          if (!target) return;
          const inside = target.closest("a, input, button");
          if (inside && wrapElement.contains(inside)) return;
          closeModal();
        },
        false,
      );
    }
  }

  function bindKeydown() {
    document.addEventListener(
      "keydown",
      (event) => {
        if (!isOpen) return;
        const key = event.key || event.keyCode;
        if (key === "Escape" || key === "Esc" || key === 27) {
          closeModal();
        }
      },
      false,
    );
  }

  function handleTriggerClick(event) {
    event.preventDefault();
    open(event.currentTarget);
  }

  function open(triggerElement) {
    if (!modalElement || !contentElement) return;

    currentTrigger = triggerElement;
    const typeAttr = triggerElement.getAttribute("data-modal-type");
    currentType = typeAttr || null;

    clearContent();
    removeLoadState();
    dispatchEvent("modal:beforeOpen");

    scrollLockManager.lock("Modal");
    modalElement.classList.add("is-active");
    isOpen = true;

    dispatchEvent("modal:afterOpen");

    if (currentType === "inline") {
      setInlineContent();
      setLoaded();
    }
  }

  function closeModal() {
    if (!isOpen || !modalElement) return;

    dispatchEvent("modal:beforeClose");
    modalElement.classList.remove("is-active");
    modalElement.classList.remove("is-load");

    scrollLockManager.unlock("Modal");
    isOpen = false;

    clearContent();
    dispatchEvent("modal:afterClose");

    currentTrigger = null;
    currentType = null;
  }

  function clearContent() {
    if (!contentElement) return;
    while (contentElement.firstChild) {
      contentElement.removeChild(contentElement.firstChild);
    }
  }

  function removeLoadState() {
    if (!modalElement) return;
    modalElement.classList.remove("is-load");
  }

  function setInlineContent() {
    if (!currentTrigger || !contentElement) return;

    const targetSelector = currentTrigger.getAttribute("data-modal-target");
    if (!targetSelector) return;

    const source = document.querySelector(targetSelector);
    if (!source) return;

    let inner = source.firstElementChild || source;
    const clone = inner.cloneNode(true);
    if (clone.id) clone.removeAttribute("id");

    const contentClass = currentTrigger.getAttribute("data-modal-content-class");
    if (contentClass) {
      contentElement.className = "js-modal__content " + contentClass;
    } else {
      contentElement.className = "js-modal__content";
    }

    contentElement.appendChild(clone);
  }

  function setLoaded() {
    if (!modalElement) return;
    modalElement.classList.add("is-load");
  }

  function dispatchEvent(type) {
    if (!modalElement) return;
    const detail = {
      modalElement,
      contentElement,
      triggerElement: currentTrigger,
      modalType: currentType,
    };
    const event = new CustomEvent(type, { detail });
    modalElement.dispatchEvent(event);
  }

  return { init, refreshTriggers, open, close: closeModal, setLoaded };
})();

/* ========== ModalGallerySplide ========== */
const ModalGallerySplide = (function () {
  const CONFIG = {
    speed: 600,
    gap: "16px",
  };

  let modalRoot = null;
  let splideInstance = null;

  function init() {
    window.addEventListener(
      "load",
      () => {
        try {
          ModalModule.init();
        } catch (error) {
          if (DEBUG) console.warn(error);
        }

        modalRoot = document.querySelector(".js-modal");
        if (!modalRoot) return;

        modalRoot.addEventListener("modal:afterOpen", handleAfterOpen, false);
        modalRoot.addEventListener("modal:beforeClose", handleBeforeClose, false);
      },
      { once: true },
    );
  }

  function handleAfterOpen(event) {
    const detail = event.detail;
    if (!detail || detail.modalType !== "gallery") return;

    const trigger = detail.triggerElement;
    const content = detail.contentElement;
    if (!trigger || !content) return;

    const targetSelector = trigger.getAttribute("data-gallery-target");
    if (!targetSelector) return;

    const templateRoot = document.querySelector(targetSelector);
    if (!templateRoot) return;

    while (content.firstChild) {
      content.removeChild(content.firstChild);
    }

    let inner = templateRoot.firstElementChild || templateRoot;
    const clone = inner.cloneNode(true);
    if (clone.id) clone.removeAttribute("id");

    content.className = "js-modal__content";
    content.appendChild(clone);

    const splideRoot = content.querySelector(".js-modalGallery");
    if (!splideRoot) {
      ModalModule.setLoaded();
      return;
    }

    let startIndex = 0;
    const indexAttr = trigger.getAttribute("data-gallery-index");
    if (indexAttr) {
      const parsed = parseInt(indexAttr, 10);
      if (!isNaN(parsed) && parsed >= 0) startIndex = parsed;
    }

    try {
      splideInstance = new Splide(splideRoot, {
        type: "loop",
        rewind: true,
        start: startIndex,
        speed: CONFIG.speed,
        gap: CONFIG.gap,
        pagination: false,
        autoplay: true,
        arrows: true,
      });
      splideInstance.mount();
    } catch (error) {
      if (DEBUG) console.warn(error);
    }

    ModalModule.setLoaded();
  }

  function handleBeforeClose(event) {
    const detail = event.detail;
    if (!detail || detail.modalType !== "gallery") return;

    if (splideInstance) {
      try {
        splideInstance.destroy(true);
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
      splideInstance = null;
    }
  }

  return { init };
})();
ModalGallerySplide.init();

/* ========== ModalYouTube ========== */
const ModalYouTube = (function () {
  let modalRoot = null;

  function init() {
    window.addEventListener(
      "load",
      () => {
        try {
          ModalModule.init();
        } catch (error) {
          if (DEBUG) console.warn(error);
        }

        modalRoot = document.querySelector(".js-modal");
        if (!modalRoot) return;

        modalRoot.addEventListener("modal:afterOpen", handleAfterOpen, false);
        modalRoot.addEventListener("modal:beforeClose", handleBeforeClose, false);
      },
      { once: true },
    );
  }

  function handleAfterOpen(event) {
    const detail = event.detail;
    if (!detail || detail.modalType !== "youtube") return;

    const trigger = detail.triggerElement;
    const content = detail.contentElement;
    if (!trigger || !content) return;

    const videoId = trigger.getAttribute("data-youtube-id");
    if (!videoId) return;

    const title = trigger.getAttribute("data-youtube-title") || "";

    while (content.firstChild) {
      content.removeChild(content.firstChild);
    }

    content.className = "js-modal__content c-modalYouTube";

    const wrap = document.createElement("div");
    wrap.className = "c-modalYouTube__inner";

    if (title) {
      const h = document.createElement("h2");
      h.className = "c-modalYouTube__title";
      h.textContent = title;
      wrap.appendChild(h);
    }

    const playerWrap = document.createElement("div");
    playerWrap.className = "c-modalYouTube__player";

    const iframe = document.createElement("iframe");
    iframe.width = "560";
    iframe.height = "315";
    iframe.src = "https://www.youtube.com/embed/" + encodeURIComponent(videoId) + "?autoplay=1&playsinline=1&rel=0";
    iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
    iframe.allowFullscreen = true;
    iframe.setAttribute("title", title || "YouTube video");

    playerWrap.appendChild(iframe);
    wrap.appendChild(playerWrap);
    content.appendChild(wrap);

    ModalModule.setLoaded();
  }

  function handleBeforeClose(event) {
    const detail = event.detail;
    if (!detail || detail.modalType !== "youtube") return;

    const content = detail.contentElement;
    if (!content) return;

    while (content.firstChild) {
      content.removeChild(content.firstChild);
    }
  }

  return { init };
})();
ModalYouTube.init();

/* ========== ModalVideo ========== */
const ModalVideo = (function () {
  let modalRoot = null;
  let currentVideo = null;

  function init() {
    window.addEventListener(
      "load",
      () => {
        try {
          ModalModule.init();
        } catch (error) {
          if (DEBUG) console.warn(error);
        }

        modalRoot = document.querySelector(".js-modal");
        if (!modalRoot) return;

        modalRoot.addEventListener("modal:afterOpen", handleAfterOpen, false);
        modalRoot.addEventListener("modal:beforeClose", handleBeforeClose, false);
      },
      { once: true },
    );
  }

  function handleAfterOpen(event) {
    const detail = event.detail;
    if (!detail || detail.modalType !== "video") return;

    const trigger = detail.triggerElement;
    const content = detail.contentElement;
    if (!trigger || !content) return;

    const src = trigger.getAttribute("data-video-src");
    if (!src) return;

    const poster = trigger.getAttribute("data-video-poster") || "";
    const title = trigger.getAttribute("data-video-title") || "";

    while (content.firstChild) {
      content.removeChild(content.firstChild);
    }

    content.className = "js-modal__content c-modalVideo";

    const wrap = document.createElement("div");
    wrap.className = "c-modalVideo__inner";

    if (title) {
      const h = document.createElement("h2");
      h.className = "c-modalVideo__title";
      h.textContent = title;
      wrap.appendChild(h);
    }

    const playerWrap = document.createElement("div");
    playerWrap.className = "c-modalVideo__player";

    const video = document.createElement("video");
    video.className = "c-modalVideo__tag";
    video.setAttribute("playsinline", "true");
    video.setAttribute("controls", "true");
    if (poster) video.setAttribute("poster", poster);

    const source = document.createElement("source");
    source.src = src;
    source.type = "video/mp4";
    video.appendChild(source);

    playerWrap.appendChild(video);
    wrap.appendChild(playerWrap);
    content.appendChild(wrap);

    currentVideo = video;

    try {
      const playPromise = video.play();
      if (playPromise?.then) {
        playPromise.catch(() => {});
      }
    } catch (error) {
      if (DEBUG) console.warn(error);
    }

    ModalModule.setLoaded();
  }

  function handleBeforeClose(event) {
    const detail = event.detail;
    if (!detail || detail.modalType !== "video") return;

    if (currentVideo) {
      try {
        currentVideo.pause();
        currentVideo.currentTime = 0;
      } catch (error) {
        if (DEBUG) console.warn(error);
      }
      currentVideo = null;
    }

    const content = detail.contentElement;
    if (!content) return;

    while (content.firstChild) {
      content.removeChild(content.firstChild);
    }
  }

  return { init };
})();
ModalVideo.init();

/* ========== PankuzuNarrow — 最初の section が is-narrow なら .l-header__pankuzu にも is-narrow を付与 ========== */
class PankuzuNarrow extends BaseModule {
  constructor() {
    super("PankuzuNarrow");
    this.pankuzu = null;
    this.firstSection = null;
  }

  setup() {
    this.pankuzu = document.querySelector(".l-header__pankuzu");
    this.firstSection = document.querySelector("section");
  }

  mount() {
    this.update();
  }

  onResize() {}

  update() {
    if (!this.pankuzu || !this.firstSection) return;

    // 最初のsectionに.is-narrowがあるかチェック
    const hasNarrow = this.firstSection.classList.contains("is-narrow");

    // .l-header__pankuzuに.is-narrowを追加または削除
    if (hasNarrow) {
      addClass(this.pankuzu, "is-narrow");
    } else {
      removeClass(this.pankuzu, "is-narrow");
    }
  }

  debug() {
    const hasNarrow = this.firstSection?.classList.contains("is-narrow") || false;
    console.log("[PankuzuNarrow] firstSection.is-narrow=" + hasNarrow + " pankuzu.is-narrow=" + (this.pankuzu?.classList.contains("is-narrow") || false));
  }
}
registerModule(new PankuzuNarrow());

/* ==========================================================================
BrWrap — テキスト内の連続 <br> を検出し、段落ごとに span.is-brwrap で囲む
========================================================================== */
class BrWrap extends BaseModule {
  constructor() {
    super("BrWrap");
    this.containers = [];
    this.originalHTML = new Map();
    this._rafId = null;
  }

  setup() {
    this.containers = toArray(document.querySelectorAll(".p-text, .p-lead"));
    this.containers.forEach((el) => {
      this.originalHTML.set(el, el.innerHTML);
    });
  }

  mount() {
    this._process();
  }

  onResize() {
    if (this._rafId) cancelAnimationFrame(this._rafId);
    this._rafId = requestAnimationFrame(() => {
      this._rafId = null;
      this._process();
    });
  }

  update() {}

  _isVisibleBr(br) {
    return window.getComputedStyle(br).display !== "none";
  }

  _process() {
    for (const container of this.containers) {
      const original = this.originalHTML.get(container);
      if (original !== undefined) {
        container.innerHTML = original;
      }
      // 子孫要素を深い順（bottom-up）に処理してからコンテナ自身を処理
      const descendants = Array.from(container.querySelectorAll("*")).reverse();
      for (const el of descendants) {
        this._processContainer(el);
      }
      this._processContainer(container);
    }
  }

  _processContainer(container) {
    const nodes = Array.from(container.childNodes);
    if (nodes.length === 0) return;

    // ノードをグループに分割:
    // br/空白テキストの連続ランを1グループ、それ以外のコンテンツを1グループとする
    const groups = [];
    let i = 0;

    while (i < nodes.length) {
      const node = nodes[i];
      const isBrLike = node.nodeName === "BR" || (node.nodeType === Node.TEXT_NODE && node.textContent.trim() === "");

      if (isBrLike) {
        // br/空白テキストのランを収集し、visible brの数を数える
        let j = i;
        let visibleBrs = 0;
        const run = [];
        while (j < nodes.length) {
          const n = nodes[j];
          const isBL = n.nodeName === "BR" || (n.nodeType === Node.TEXT_NODE && n.textContent.trim() === "");
          if (!isBL) break;
          if (n.nodeName === "BR" && this._isVisibleBr(n)) visibleBrs++;
          run.push(n);
          j++;
        }
        // visible brが2個以上あればセパレーター
        groups.push({ type: visibleBrs >= 2 ? "separator" : "content", nodes: run });
        i = j;
      } else {
        groups.push({ type: "content", nodes: [node] });
        i++;
      }
    }

    // セパレーターがなければ何もしない
    if (!groups.some((g) => g.type === "separator")) return;

    // 連続するcontentグループをマージ
    const merged = [];
    for (const g of groups) {
      const last = merged[merged.length - 1];
      if (last && last.type === "content" && g.type === "content") {
        last.nodes.push(...g.nodes);
      } else {
        merged.push({ type: g.type, nodes: [...g.nodes] });
      }
    }

    // DOMを再構築
    while (container.firstChild) container.removeChild(container.firstChild);

    for (const group of merged) {
      if (group.type === "separator") {
        for (const node of group.nodes) {
          if (node.nodeName === "BR" && this._isVisibleBr(node)) {
            node.classList.add("is-hidden");
          }
          container.appendChild(node);
        }
      } else {
        // 空白テキスト以外の実コンテンツがあればspanで囲む
        const hasRealContent = group.nodes.some((n) => !(n.nodeType === Node.TEXT_NODE && n.textContent.trim() === ""));
        if (hasRealContent) {
          const span = document.createElement("span");
          span.className = "is-brwrap";
          for (const node of group.nodes) span.appendChild(node);
          container.appendChild(span);
        } else {
          for (const node of group.nodes) container.appendChild(node);
        }
      }
    }
  }

  debug() {
    console.log("[BrWrap] containers=" + this.containers.length);
  }
}
registerModule(new BrWrap());
