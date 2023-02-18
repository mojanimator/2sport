const mix = require('laravel-mix');
require('dotenv').config();
require('laravel-mix-polyfill');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .browserSync('http://localhost:8000/')
    .sass('resources/sass/app.scss', 'public/css')
    // .postCss('public/css/app.css', 'public/css/app.rtl.css', [
    //     require('rtlcss'),
    // ])
    .options({processCssUrls: false})
    .webpackConfig(webpack => {
        return {
            devtool: 'source-map'
//         plugins: [
//             new webpack.DefinePlugin({
//                 __VUE_OPTIONS_API__: false,
//                 __VUE_PROD_DEVTOOLS__: false,
//             }),
//         ],
        }
    })
    .polyfill({
        enabled: true,
        useBuiltIns: "usage",
        targets: "> 0.05%, not ie < 10, safari >= 8",
    });
// .webpackConfig({
//     module: {
//         rules: [
//             {
//                 test: /\.(woff2?|ttf|eot|svg)$/,
//                 loader: 'file-loader',
//                 options: {
//                     name: '/public/fonts/[name].[ext]',
//                     publicPath: '..'
//                 }
//             },
//         ]
//     }
// })
;
