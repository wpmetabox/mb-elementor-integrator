const path = require( 'path' );

const externals = {
    'React': 'React',
    // 'react-dom': 'ReactDOM'
};

module.exports = {
    // devtool: '',
    entry: './src/assets/app/mbei.js',
    output: {
        path: path.resolve( 'src/assets/js' ),
        filename: 'mbei.js',
    },
    module: {
        rules: [
            {
                test: /mbei.js/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [ '@babel/preset-env' ],
                        plugins: [ "@babel/plugin-proposal-class-properties" ]
                    }
                }
            }
        ]
    },
    externals
};