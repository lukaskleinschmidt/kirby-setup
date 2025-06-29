import { defineConfig, loadEnv, mergeConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import picomatch from 'picomatch';
import path from 'path';
import fs from 'fs';

export default defineConfig({
  plugins: [
    kirby({
      input: [
        'resources/scripts/app.js',
        'resources/styles/app.css',
        'resources/scripts/panel.js',
        'resources/styles/panel.css',
      ],
      reload: [
        'site/layouts/**',
        'site/snippets/**',
        'site/templates/**',
      ]
    }),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      '@fonts': 'resources/fonts',
      '@media': 'resources/media',
      '@scripts': 'resources/scripts',
      '@styles': 'resources/styles',
    },
  },
});

function kirby(options) {
  const root = process.cwd();
  const file = path.resolve('public/vite');

  return {
    name: 'kirby',
    enforce: 'post',
    config(config, { mode }) {
      const env = loadEnv(mode, root, '');

      return mergeConfig({
        publicDir: false,
        build: {
          manifest: 'manifest.json',
          outDir: 'public/build',
          rollupOptions: {
            input: options.input,
          }
        },
        server: {
          cors: {
            origin: [
              /^https?:\/\/(?:(?:[^:]+\.)?localhost|127\.0\.0\.1|\[::1\])(?::\d+)?$/,
              /^https?:\/\/.*\.ddev.site(:\d+)?$/,
              /^https?:\/\/.*\.test(:\d+)?$/,
              env.APP_URL,
            ]
          },
          https: {
            key: env.VITE_SERVER_KEY,
            cert: env.VITE_SERVER_CERT,
          }
        }
      }, config);
    },
    configureServer(server) {
      server.httpServer?.once('listening', () => {
        const address = server.httpServer.address();
        const protocol = server.config.server.https ? 'https' : 'http';
        const host = server.config.server.host || address.address || 'localhost';
        const port = server.config.server.port || address.port || 5173;

        fs.writeFileSync(file, `${protocol}://${host}:${port}`);
      });

      process.on('exit', () => {
        if (fs.statSync(file)) {
          fs.unlinkSync(file);
        }
      });

      process.on('SIGINT', () => process.exit());
      process.on('SIGTERM', () => process.exit());
      process.on('SIGHUP', () => process.exit());

      const reload = file => {
        const relative = path.relative(root, file).replace(/\\/g, '/');

        if (picomatch(options.reload)(relative)) {
          server.ws.send({
            type: 'full-reload',
            path: '*',
          });
        }
      };

      server.watcher.on('add', reload);
      server.watcher.on('change', reload);
    }
  };
}
