import sharp from "sharp";
import path from "path";
import fs from "fs";
import fse from "fs-extra";
import { globSync } from "glob";
import { argv } from "process";

const ENCODER_OPTIONS = {
  png: { compressionLevel: 9, progressive: true },
  jpg: { quality: 90 },
  webp: { png: { lossless: true }, jpg: { quality: 90 } },
};

const IMAGE_DIR = "_src/image";
const OUTPUT_DIR = "_public/assets/image";
const allowedExtensions = ["jpg", "jpeg", "png", "webp", "svg", "ico"];

function loadEnvFile(filePath = ".env") {
  if (!fs.existsSync(filePath)) {
    return {};
  }

  return fs
    .readFileSync(filePath, "utf8")
    .split(/\r?\n/)
    .reduce((env, line) => {
      const trimmedLine = line.trim();
      if (!trimmedLine || trimmedLine.startsWith("#")) {
        return env;
      }

      const separatorIndex = trimmedLine.indexOf("=");
      if (separatorIndex === -1) {
        return env;
      }

      const key = trimmedLine.slice(0, separatorIndex).trim();
      let value = trimmedLine.slice(separatorIndex + 1).trim();

      if (
        (value.startsWith('"') && value.endsWith('"')) ||
        (value.startsWith("'") && value.endsWith("'"))
      ) {
        value = value.slice(1, -1);
      }

      env[key] = value;
      return env;
    }, {});
}

const envConfig = loadEnvFile();
const WEBP_ONLY = (process.env.WEBP_ONLY ?? envConfig.WEBP_ONLY ?? "true").toLowerCase() === "true";

function getOutputExtensions(ext) {
  if (ext === "svg" || ext === "ico") {
    return [ext];
  }

  if (ext === "webp") {
    return ["webp"];
  }

  return WEBP_ONLY ? ["webp"] : [ext, "webp"];
}

function getCleanupExtensions() {
  return ["jpg", "jpeg", "png", "webp"];
}

/**
 * `_public` に存在する不要な画像を削除
 */
async function cleanDeletedFiles() {
  // `_src` の画像リストを取得
  const srcFiles = globSync(`${IMAGE_DIR}/**/*.*`).map(
    (file) => path.relative(IMAGE_DIR, file).replace(/\.\w+$/, "") // 拡張子を除去
  );

  // `_public` の画像リストを取得
  const publicFiles = globSync(`${OUTPUT_DIR}/**/*.*`).map(
    (file) => path.relative(OUTPUT_DIR, file).replace(/\.\w+$/, "") // 拡張子を除去
  );

  // `_src` に存在しない画像を特定
  const filesToDelete = publicFiles.filter((file) => !srcFiles.includes(file));

  // 不要な画像とその WebP を削除
  await Promise.all(
    filesToDelete.map(async (file) => {
      const extensions = getCleanupExtensions();
      for (const ext of extensions) {
        const filePath = path.join(OUTPUT_DIR, `${file}.${ext}`);
        if (await fse.pathExists(filePath)) {
          await fse.remove(filePath);
          console.info(`Deleted: ${filePath}`);
        }
      }
    })
  );

  if (filesToDelete.length === 0) {
    console.info("No files to delete.");
  }
}

/**
 * 指定された画像ファイルを処理する関数
 * @param {string[]} filePaths - 処理対象の画像ファイルパス
 */
async function processImages(filePaths) {
  const validExtensions = ["jpg", "jpeg", "png", "webp", "svg", "ico"];

  // フィルタリングして不要なファイルを除外
  const filteredPaths = filePaths.filter((filePath) => {
    const ext = path.extname(filePath).toLowerCase().replace(".", "");
    return validExtensions.includes(ext);
  });

  if (filteredPaths.length === 0) {
    console.info("No valid image files to process.");
    return;
  }

  const startTime = Date.now();
  let processedCount = 0;

  await Promise.all(
    filteredPaths.map(async (filePath) => {
      try {
        const ext = path.extname(filePath).toLowerCase().replace(".", "");
        const isSvg = ext === "svg";
        const isIco = ext === "ico";

        // 重複するパスを防ぐため、相対パスを計算
        const relativePath = filePath.startsWith(IMAGE_DIR) ? path.relative(IMAGE_DIR, filePath) : filePath;

        const sourcePath = path.join(IMAGE_DIR, relativePath);
        const outputPath = path.join(OUTPUT_DIR, relativePath);

        // 出力ディレクトリを作成
        await fse.ensureDir(path.dirname(outputPath));

        if (isSvg || isIco) {
          // SVG はコピー
          await fse.copy(sourcePath, outputPath);
          console.info(`Copied: ${filePath}`);
        } else {
          // 他の画像形式はエンコード
          const encoder = ext === "jpg" ? "jpeg" : ext;

          if (!WEBP_ONLY || ext === "webp") {
            await sharp(sourcePath).toFormat(encoder, ENCODER_OPTIONS[encoder]).toFile(outputPath);
            console.info(`Processed: ${filePath}`);
          } else if (await fse.pathExists(outputPath)) {
            await fse.remove(outputPath);
            console.info(`Deleted original: ${outputPath}`);
          }

          if (getOutputExtensions(ext).includes("webp") && ext !== "webp") {
            const webpPath = outputPath.replace(/\.\w+$/, ".webp");
            await sharp(sourcePath).webp(ENCODER_OPTIONS.webp[encoder]).toFile(webpPath);
            console.info(`Processed WebP: ${webpPath}`);
          }
        }

        processedCount++;
      } catch (error) {
        console.error(`Error processing ${filePath}:`, error.message);
      }
    })
  );

  console.info(`Processed ${processedCount}/${filteredPaths.length} files in ${Date.now() - startTime}ms`);
}

/**
 * ビルドモード
 * すべての画像を処理
 */
async function buildMode() {
  console.info("Cleaning deleted files...");
  await cleanDeletedFiles();
  const allFiles = globSync("**/*.*", { cwd: IMAGE_DIR }).filter((file) => allowedExtensions.includes(path.extname(file).toLowerCase().replace(".", "")));
  console.info(`Starting build: ${allFiles.length} files found.`);
  await processImages(allFiles);
}

/**
 * ウォッチモード
 * 変更や削除のあった画像を処理
 */
async function watchMode() {
  // コマンドライン引数から変更ファイルを取得
  const changedFiles = argv.filter((arg) => !arg.startsWith("--"));

  // `_src/image` 配下のファイルに限定
  const validFiles = changedFiles.filter((filePath) => filePath.startsWith("_src/image"));

  if (validFiles.length === 0) {
    console.info("No valid files to process.");
    return;
  }

  console.info(`Starting watch: ${validFiles.length} files changed.`);

  // 変更または追加されたファイルを処理
  const existingFiles = validFiles.filter((filePath) => fse.pathExistsSync(filePath));
  await processImages(existingFiles);

  // 削除されたファイルに対応する `_public` の画像を削除
  const deletedFiles = validFiles.filter((filePath) => !fse.pathExistsSync(filePath));
  await Promise.all(deletedFiles.map(deleteCorrespondingFiles));
}

/**
 * 対応する `_public` の画像を削除
 * @param {string} filePath - 削除された `_src` の画像パス
 */
async function deleteCorrespondingFiles(filePath) {
  const relativePath = path.relative(IMAGE_DIR, filePath).replace(/\.\w+$/, "");
  const extensions = getCleanupExtensions();

  await Promise.all(
    extensions.map(async (ext) => {
      const fileToDelete = path.join(OUTPUT_DIR, `${relativePath}.${ext}`);
      if (await fse.pathExists(fileToDelete)) {
        await fse.remove(fileToDelete);
        console.info(`Deleted: ${fileToDelete}`);
      }
    })
  );
}

// モード判定
if (argv.includes("--watch")) {
  await watchMode();
} else {
  await buildMode();
}
