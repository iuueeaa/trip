import fse from "fs-extra";
import path from "path";
import { argv } from "process";

/**
 * 変更されたファイルのみをコピーするスクリプト
 * 使用例: node scripts/copy-changed.mjs --src _src/html --dest _public path/to/changed/file.php
 */

// コマンドライン引数の解析
const args = argv.slice(2);
let srcDir = "";
let destDir = "";
const changedFiles = [];

for (let i = 0; i < args.length; i++) {
  if (args[i] === "--src" && args[i + 1]) {
    srcDir = args[i + 1];
    i++;
  } else if (args[i] === "--dest" && args[i + 1]) {
    destDir = args[i + 1];
    i++;
  } else if (!args[i].startsWith("--")) {
    changedFiles.push(args[i]);
  }
}

if (!srcDir || !destDir) {
  console.error("Error: --src and --dest are required");
  process.exit(1);
}

if (changedFiles.length === 0) {
  console.info("No files to copy.");
  process.exit(0);
}

/**
 * 変更されたファイルをコピー
 */
async function copyChangedFiles() {
  const startTime = Date.now();
  let copiedCount = 0;

  for (const filePath of changedFiles) {
    try {
      // ファイルが存在するか確認（削除された場合は対応するファイルを削除）
      const exists = await fse.pathExists(filePath);

      // 相対パスを計算
      const relativePath = filePath.startsWith(srcDir)
        ? path.relative(srcDir, filePath)
        : path.basename(filePath);

      const destPath = path.join(destDir, relativePath);

      if (exists) {
        // ファイルが存在する場合はコピー
        await fse.ensureDir(path.dirname(destPath));
        await fse.copy(filePath, destPath);
        console.info(`Copied: ${filePath} → ${destPath}`);
        copiedCount++;
      } else {
        // ファイルが削除された場合は出力先も削除
        if (await fse.pathExists(destPath)) {
          await fse.remove(destPath);
          console.info(`Deleted: ${destPath}`);
          copiedCount++;
        }
      }
    } catch (error) {
      console.error(`Error processing ${filePath}:`, error.message);
    }
  }

  console.info(
    `Processed ${copiedCount}/${changedFiles.length} files in ${Date.now() - startTime}ms`
  );
}

await copyChangedFiles();
