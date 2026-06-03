// vite.config.js
import { defineConfig } from "file:///C:/xampp/htdocs/doantotnghiep/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/xampp/htdocs/doantotnghiep/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/xampp/htdocs/doantotnghiep/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import tailwindcss from "file:///C:/xampp/htdocs/doantotnghiep/node_modules/@tailwindcss/vite/dist/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: "resources/js/app.js",
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    tailwindcss()
  ],
  resolve: {
    alias: {
      "@": "/resources/js"
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFx4YW1wcFxcXFxodGRvY3NcXFxcZG9hbnRvdG5naGllcFwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxceGFtcHBcXFxcaHRkb2NzXFxcXGRvYW50b3RuZ2hpZXBcXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L3hhbXBwL2h0ZG9jcy9kb2FudG90bmdoaWVwL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJztcbmltcG9ydCB0YWlsd2luZGNzcyBmcm9tICdAdGFpbHdpbmRjc3Mvdml0ZSc7XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiAncmVzb3VyY2VzL2pzL2FwcC5qcycsXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgdnVlKHtcbiAgICAgICAgICAgIHRlbXBsYXRlOiB7XG4gICAgICAgICAgICAgICAgdHJhbnNmb3JtQXNzZXRVcmxzOiB7XG4gICAgICAgICAgICAgICAgICAgIGJhc2U6IG51bGwsXG4gICAgICAgICAgICAgICAgICAgIGluY2x1ZGVBYnNvbHV0ZTogZmFsc2UsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0pLFxuICAgICAgICB0YWlsd2luZGNzcygpLFxuICAgIF0sXG4gICAgcmVzb2x2ZToge1xuICAgICAgICBhbGlhczoge1xuICAgICAgICAgICAgJ0AnOiAnL3Jlc291cmNlcy9qcycsXG4gICAgICAgIH0sXG4gICAgfSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFpUixTQUFTLG9CQUFvQjtBQUM5UyxPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBQ2hCLE9BQU8saUJBQWlCO0FBRXhCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxNQUNQLFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUNELElBQUk7QUFBQSxNQUNBLFVBQVU7QUFBQSxRQUNOLG9CQUFvQjtBQUFBLFVBQ2hCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ3JCO0FBQUEsTUFDSjtBQUFBLElBQ0osQ0FBQztBQUFBLElBQ0QsWUFBWTtBQUFBLEVBQ2hCO0FBQUEsRUFDQSxTQUFTO0FBQUEsSUFDTCxPQUFPO0FBQUEsTUFDSCxLQUFLO0FBQUEsSUFDVDtBQUFBLEVBQ0o7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
