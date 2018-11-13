'use strict'
// Template version: 1.3.1
// see http://vuejs-templates.github.io/webpack for documentation.

const path = require('path')
const BuildPath= path.resolve(__dirname, '../../');
const BuildIndex=path.resolve(BuildPath, 'index.html')
const assetsPublicPath='/polleras/';

module.exports = {
  dev: {

    // Paths
    assetsSubDirectory: 'static',
    assetsPublicPath: assetsPublicPath,
    proxyTable: 
    {
        '/polleras/api':{
            target: 'http://127.0.0.1:8080/polleras/api',
            changeOrigin: true,
            pathRewrite: {'^/polleras/api': ''}
        },
        '/polleras/static':{
            target: 'http://127.0.0.1:8081/static',
            changeOrigin: true,
            pathRewrite: {'^/polleras/static': ''}
        },
        '/polleras/icono':{
            target: 'http://127.0.0.1:8080/polleras/icono',
            changeOrigin: true,
            pathRewrite: {'/polleras/icono': ''}
        }   
     },

    // Various Dev Server settings
    host: 'localhost', // can be overwritten by process.env.HOST
    port: 8081, // can be overwritten by process.env.PORT, if port is in use, a free one will be determined
    autoOpenBrowser: true,
    errorOverlay: true,
    notifyOnErrors: true,
    poll: false, // https://webpack.js.org/configuration/dev-server/#devserver-watchoptions-

    
    /**
     * Source Maps
     */

    // https://webpack.js.org/configuration/devtool/#development
    devtool: 'cheap-module-eval-source-map',

    // If you have problems debugging vue-files in devtools,
    // set this to false - it *may* help
    // https://vue-loader.vuejs.org/en/options.html#cachebusting
    cacheBusting: true,

    cssSourceMap: true
  },

  build: {
    // Template for index.html
    index: BuildIndex,

    // Paths
    assetsRoot: BuildPath,
    assetsSubDirectory: 'static',
    assetsPublicPath: assetsPublicPath,
    apiSubDirectory:'api',
    /**
     * Source Maps
     */

    productionSourceMap: false,
    // https://webpack.js.org/configuration/devtool/#production
    devtool: '#source-map',

    // Gzip off by default as many popular static hosts such as
    // Surge or Netlify already gzip all static assets for you.
    // Before setting to `true`, make sure to:
    // npm install --save-dev compression-webpack-plugin
    productionGzip: false,
    productionGzipExtensions: ['js', 'css'],

    // Run the build command with an extra argument to
    // View the bundle analyzer report after build finishes:
    // `npm run build --report`
    // Set to `true` or `false` to always turn it on or off
    bundleAnalyzerReport: process.env.npm_config_report
  }
}
