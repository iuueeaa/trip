// @done HMR-2: PHP_PORT/WP_PORT/VITE_PORT を動的検索、docker compose を内部起動、ブラウザ自動起動
import { spawn } from "child_process";
import { createServer } from "net";
import http from "http";
import os from "os";
import fse from "fs-extra";

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

async function findAvailablePort(startPort, maxTries = 100) {
  for (let i = 0; i < maxTries; i++) {
    const port = startPort + i;
    if (await isPortAvailable(port)) {
      return port;
    }
  }
  throw new Error(`No available port found in range ${startPort}-${startPort + maxTries - 1}`);
}

function getPreferredHost() {
  const interfaces = os.networkInterfaces();
  for (const addresses of Object.values(interfaces)) {
    if (!addresses) continue;
    for (const address of addresses) {
      if (address && address.family === "IPv4" && !address.internal && address.address.startsWith("192.168.")) {
        return address.address;
      }
    }
  }
  return "localhost";
}

function waitForServer(url, maxRetries = 30, interval = 500) {
  return new Promise((resolve, reject) => {
    let attempts = 0;
    const tryRequest = () => {
      http
        .get(url, (res) => {
          res.resume();
          resolve();
        })
        .on("error", () => {
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

function openBrowser(url) {
  const opener = spawn("open", [url], { detached: true, stdio: "ignore" });
  opener.unref();
}

async function savePortsToFile(phpPort, wpPort, vitePort) {
  const portsData = {
    PHP_PORT: phpPort,
    WP_PORT: wpPort,
    VITE_PORT: vitePort,
    timestamp: new Date().toISOString(),
  };
  await Promise.all([fse.writeJson(".ports.json", portsData), fse.writeJson("_public/.ports.json", portsData)]);
  console.log("\n📝 Port configuration saved to .ports.json and _public/.ports.json");
}

async function cleanupPortsFile() {
  await Promise.all([fse.remove(".ports.json").catch(() => {}), fse.remove("_public/.ports.json").catch(() => {})]);
  console.log("\n🗑️  Cleaned up .ports.json");
}

async function startDockerServers() {
  try {
    const phpPort = await findAvailablePort(3000);
    const wpPort = await findAvailablePort(9000);
    const vitePort = await findAvailablePort(5173);
    const host = getPreferredHost();

    console.log("\n🔍 Found available ports:");
    console.log(`  PHP:  http://${host}:${phpPort}`);
    console.log(`  WP:   http://${host}:${wpPort}`);
    console.log(`  Vite: http://localhost:${vitePort}`);

    await savePortsToFile(phpPort, wpPort, vitePort);

    const env = {
      ...process.env,
      PHP_PORT: phpPort.toString(),
      WP_PORT: wpPort.toString(),
      VITE_PORT: vitePort.toString(),
    };

    // docker compose 起動（完了を待つ）
    console.log("\n🐳 Starting docker compose...");
    await new Promise((resolve, reject) => {
      const dc = spawn("docker", ["compose", "up", "-d", "web_heteml", "web_wordpress", "db"], {
        env,
        stdio: "inherit",
      });
      dc.on("close", (code) => {
        if (code === 0) resolve();
        else reject(new Error(`docker compose exited with code ${code}`));
      });
    });

    // Vite 起動
    console.log(`\n🚀 Starting Vite (docker mode) on port ${vitePort}...`);
    const viteServer = spawn("npx", ["vite", "--mode", "docker", "--port", vitePort.toString()], {
      env,
      stdio: "inherit",
    });

    // PHP と Vite の応答を待ってブラウザ起動
    console.log("⏳ Waiting for servers to be ready...");
    await Promise.all([waitForServer(`http://localhost:${phpPort}`), waitForServer(`http://localhost:${vitePort}`)]);
    console.log(`\n🚀 Opening browser: http://${host}:${phpPort}/`);
    openBrowser(`http://${host}:${phpPort}/`);

    // watch 起動
    console.log("🚀 Starting file watchers...");
    const watchProcess = spawn("npm", ["run", "watch"], { env, stdio: "inherit" });

    const cleanup = async () => {
      console.log("\n\n🛑 Shutting down...");
      viteServer.kill();
      watchProcess.kill();
      await cleanupPortsFile();
      process.exit(0);
    };

    process.on("SIGINT", cleanup);
    process.on("SIGTERM", cleanup);

    console.log("\n✅ Docker servers started successfully!");
  } catch (error) {
    console.error("❌ Error starting docker servers:", error.message);
    process.exit(1);
  }
}

startDockerServers();
