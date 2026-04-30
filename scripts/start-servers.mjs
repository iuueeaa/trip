import { spawn } from "child_process";
import { createServer } from "net";
import os from "os";
import http from "http";
import fse from "fs-extra";

/**
 * ポートが使用可能かチェック
 */
function isPortAvailable(port) {
  return new Promise((resolve) => {
    const server = createServer();
    server.once("error", () => resolve(false));
    server.once("listening", () => {
      server.close();
      resolve(true);
    });
    server.listen(port);
  });
}

/**
 * 使用可能なポートを見つける
 */
async function findAvailablePort(startPort, maxTries = 100) {
  for (let i = 0; i < maxTries; i++) {
    const port = startPort + i;
    if (await isPortAvailable(port)) {
      return port;
    }
  }
  throw new Error(`No available port found in range ${startPort}-${startPort + maxTries - 1}`);
}

/**
 * 環境変数をファイルに保存（他のプロセスで読み込むため）
 */
async function savePortsToFile(phpPort, vitePort) {
  const portsData = {
    PHP_PORT: phpPort,
    VITE_PORT: vitePort,
    timestamp: new Date().toISOString(),
  };

  await Promise.all([fse.writeJson(".ports.json", portsData), fse.writeJson("_public/.ports.json", portsData)]);
  console.log("\n📝 Port configuration saved to .ports.json and _public/.ports.json");
}

/**
 * LAN IPを取得（なければlocalhost）
 */
function getPreferredHost() {
  const interfaces = os.networkInterfaces();
  for (const addresses of Object.values(interfaces)) {
    if (!addresses) {
      continue;
    }
    for (const address of addresses) {
      if (address && address.family === "IPv4" && !address.internal && address.address.startsWith("192.168.")) {
        return address.address;
      }
    }
  }
  return "localhost";
}

/**
 * サーバーが応答するまで待機
 */
function waitForServer(url, maxRetries = 30, interval = 500) {
  return new Promise((resolve, reject) => {
    let attempts = 0;
    const tryRequest = () => {
      http.get(url, (res) => {
        res.resume();
        resolve();
      }).on("error", () => {
        attempts++;
        if (attempts >= maxRetries) {
          reject(new Error(`Server at ${url} did not respond after ${maxRetries} attempts`));
        } else {
          setTimeout(tryRequest, interval);
        }
      });
    };
    tryRequest();
  });
}

/**
 * ブラウザを自動起動（macOS想定）
 */
function openBrowser(url) {
  const opener = spawn("open", [url], {
    detached: true,
    stdio: "ignore",
  });
  opener.unref();
}

/**
 * サーバー起動
 */
async function startServers() {
  try {
    // 使用可能なポートを探す
    const phpPort = await findAvailablePort(8080);
    const vitePort = await findAvailablePort(5173);
    const host = getPreferredHost();
    const phpUrl = `http://${host}:${phpPort}`;

    console.log("\n🔍 Found available ports:");
    console.log(`  PHP Server:    ${phpUrl}`);
    console.log(`  Vite:          http://localhost:${vitePort}`);

    // ポート情報を保存
    await savePortsToFile(phpPort, vitePort);

    // 環境変数を設定
    const env = {
      ...process.env,
      PHP_PORT: phpPort.toString(),
      VITE_PORT: vitePort.toString(),
    };

    // PHPサーバー起動
    console.log(`\n🚀 Starting PHP server on port ${phpPort}...`);
    const phpServer = spawn("php", ["-S", `0.0.0.0:${phpPort}`, "-t", "_public"], {
      env,
      stdio: "inherit",
    });

    // Vite起動
    console.log(`🚀 Starting Vite on port ${vitePort}...`);
    const viteServer = spawn("npx", ["vite", "--mode", "development", "--port", vitePort.toString()], {
      env,
      stdio: "inherit",
    });

    // Watch起動
    console.log(`🚀 Starting file watchers...`);
    const watchProcess = spawn("npm", ["run", "watch"], {
      env,
      stdio: "inherit",
    });

    // ブラウザ自動起動（PHPサーバーURL）
    console.log(`⏳ Waiting for servers to be ready...`);
    await Promise.all([
      waitForServer(`http://localhost:${phpPort}`),
      waitForServer(`http://localhost:${vitePort}`),
    ]);
    console.log(`🚀 Opening browser: ${phpUrl}`);
    openBrowser(phpUrl);

    // プロセス終了時のクリーンアップ
    const cleanup = () => {
      console.log("\n\n🛑 Shutting down servers...");
      phpServer.kill();
      viteServer.kill();
      watchProcess.kill();
      process.exit(0);
    };

    process.on("SIGINT", cleanup);
    process.on("SIGTERM", cleanup);

    console.log("\n✅ All servers started successfully!");
    console.log(`\n👉 Open your browser: ${phpUrl}\n`);
  } catch (error) {
    console.error("❌ Error starting servers:", error.message);
    process.exit(1);
  }
}

startServers();
