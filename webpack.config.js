const path = require('path');
const glob = require('glob');
const {merge} = require('lodash');

let result = [];

glob.sync('{app/modules/**,app/installer/**,app/system/**,packages/**}/webpack.config.js', {ignore: 'packages/**/node_modules/**'}).forEach(file => {
  const dir = path.join(__dirname, path.dirname(file));
  result = result.concat(require(`./${file}`).map(config => {
    return merge({context: dir, output: {path: dir}}, config);
  }));
});

module.exports = result;
