import anchors from 'exports?anchors!anchor-js/anchor';
import docsearch from './ds';

anchors.add('.js-anchor-links h2, .js-anchor-links h3, .js-anchor-links h4, .js-anchor-links h5, .js-anchor-links h6');

docsearch();
