/**
 * 热门任务
 * -------------
 *
 * 编译：编译指定软件包的 .less 文件
 * lint：在所有 .js 文件上运行 jshint
 */

var merge = require('merge-stream'),
    gulp = require('gulp'),
    header = require('gulp-header'),
    less = require('gulp-less'),
    rename = require('gulp-rename'),
    eslint = require('gulp-eslint'),
    plumber = require('gulp-plumber'),
    fs = require('fs'),
    path = require('path');

// 编译任务的软件包路径
var pkgs = [
    {path: 'app/installer/', data: '../../composer.json'},
    {path: 'app/system/modules/theme/', data: '../../../../composer.json'}
];

// css 文件的横幅
var banner = "/*! <%= data.title %> <%= data.version %> | (c) 2020 FoxKit | MIT License */\n";

var cldr = {
    cldr: path.join(__dirname, 'node_modules/cldr-core/supplemental/'),
    intl: path.join(__dirname, 'app/system/modules/intl/data/'),
    locales: path.join(__dirname, 'node_modules/cldr-localenames-modern/main/'),
    formats: path.join(__dirname, 'app/assets/vue-intl/dist/locales/'),
    languages: path.join(__dirname, 'app/system/languages/')
};

// plumber 的一般错误处理程序
var errhandler = function (error) {
    this.emit('end');
    return console.error(error.toString());
};

gulp.task('default', ['compile']);

/**
 * 编译所有 less 文件
 */
gulp.task('compile', function () {

    pkgs = pkgs.filter(function (pkg) {
        return fs.existsSync(pkg.path);
    });

    return merge.apply(null, pkgs.map(function (pkg) {
        return gulp.src(pkg.path + '**/less/*.less', {base: pkg.path})
            .pipe(plumber(errhandler))
            .pipe(less({compress: true, relativeUrls: true}))
            .pipe(header(banner, {data: require('./' + pkg.path + pkg.data)}))
            .pipe(rename(function (file) {
                // 编译的 less 文件应存储在 css/ 文件夹中，而不是 less/ 文件夹中
                file.dirname = file.dirname.replace('less', 'css');
            }))
            .pipe(gulp.dest(pkg.path));
    }));

});

/**
 * 监听文件中的变动
 */
gulp.task('watch', function (cb) {
    gulp.watch('**/*.less', ['compile']);
});

/**
 * 整理所有脚本文件
 */
gulp.task('lint', function () {
    return gulp.src([
        'app/modules/**/*.js',
        'app/system/**/*.js',
        'extensions/**/*.js',
        'themes/**/*.js',
        '!**/bundle/*',
        '!**/vendor/**/*'
    ])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failOnError());
});

gulp.task('cldr', function () {

    // territoryContainment
    var data = {}, json = JSON.parse(fs.readFileSync(cldr.cldr + 'territoryContainment.json', 'utf8')).supplemental.territoryContainment;
    Object.keys(json).forEach(function (key) {
        if (isNaN(key)) return;
        data[key] = json[key]._contains;
    });
    fs.writeFileSync(cldr.intl + 'territoryContainment.json', JSON.stringify(data));

    fs.readdirSync(cldr.languages)
        .filter(function (file) {
            return fs.statSync(path.join(cldr.languages, file)).isDirectory();
        })
        .forEach(function (src) {

            var id = src.replace('_', '-'), shortId = id.substr(0, id.indexOf('-')), found;

            ['languages', 'territories'].forEach(function (name) {

                found = false;
                [id, shortId, 'en'].forEach(function (locale) {
                    var file = cldr.locales + locale + '/' + name + '.json';
                    if (!found && fs.existsSync(file)) {
                        found = true;
                        fs.writeFileSync(cldr.languages + src + '/' + name + '.json', JSON.stringify(JSON.parse(fs.readFileSync(file, 'utf8')).main[locale].localeDisplayNames[name]));
                    }
                });

            });

            found = false;
            [id.toLowerCase(), shortId, 'en'].forEach(function (locale) {
                var file = cldr.formats + locale + '.json';
                if (!found && fs.existsSync(file)) {
                    found = true;
                    fs.writeFileSync(cldr.languages + src + '/formats.json', fs.readFileSync(file, 'utf8'));
                }
            });

        });
});
