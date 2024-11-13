const Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // enable a single runtime chunk
    .enableSingleRuntimeChunk()
    // other configurations...
    .addStyleEntry('styles', './assets/styles/app.css')
    .enablePostCssLoader()
;

// Export the final configuration
module.exports = Encore.getWebpackConfig