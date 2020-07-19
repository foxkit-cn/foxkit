'use strict';

const fs = require('fs');
const gulp = require('gulp');
const less = require('gulp-less');
const header = require('gulp-header');
const rename = require('gulp-rename');

// css 文件的横幅
const banner = "/*! <%= data.name %> <%= data.version %> | (c) 2020 FoxKit | MIT License */\n";

// 编译任务的软件包路径
let pkgs = [
  {path: 'app/installer/', data: '../../package.json'},
  {path: 'app/system/modules/theme/', data: '../../../../package.json'}
];

// 任务：编译所有 less 文件
function compile(cb) {
  pkgs.map(pkg => {
    if (fs.existsSync(pkg.path)) {
      gulp.src(`${pkg.path}**/less/*.less`, {base: pkg.path})
        .pipe(less({compress: true, relativeUrls: true}))
        .pipe(header(banner, {data: require(`./${pkg.path}${pkg.data}`)}))
        .pipe(rename(file => {
          // 编译的 less 文件应存储在 css/ 文件夹中，而不是 less/ 文件夹中
          file.dirname = file.dirname.replace('less', 'css');
        }))
        .pipe(gulp.dest(pkg.path));
    }
  })
  cb();
}

exports.default = gulp.series(compile);
