const path = require('path');
const fs = require('fs');
const CopyWebpackPlugin = require('copy-webpack-plugin', true);

const defaultConfig = require('@wordpress/scripts/config/webpack.config', true);
const { fromProjectRoot } = require('@wordpress/scripts/utils/file', true);

const srcPath = fromProjectRoot('assets-src');
const distPath = fromProjectRoot('assets');

function getFilesInDir(dirPath) {
    if (!fs.existsSync(dirPath)) {
        return [];
    }

    return fs.readdirSync(dirPath, { withFileTypes: true })
        .filter((entry) => entry.isFile())
        .map((entry) => path.join(dirPath, entry.name));
}

function getCopyPatterns() {
    let patterns = [];

    getFilesInDir(path.join(srcPath, 'icons')).forEach((file) => {
        patterns.push({
            from: file,
            to: path.relative(srcPath, file)
        });
    });

    getFilesInDir(path.join(srcPath, 'json')).forEach((file) => {
        patterns.push({
            from: file,
            to: path.relative(srcPath, file)
        });
    });

    return patterns;
}

module.exports = {
    ...defaultConfig,
    entry: {
        'admin/main': path.join(srcPath, 'admin/main.js')
    },
    output: {
        ...defaultConfig.output,
        path: distPath,
        filename: '[name].js',
    },
    plugins: [
        ...defaultConfig.plugins,
        new CopyWebpackPlugin({
            patterns: getCopyPatterns()
        })
    ]
};
