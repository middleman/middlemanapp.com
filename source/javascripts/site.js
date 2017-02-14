import anchors from 'exports?anchors!anchor-js/anchor';
import setupDocsNavAnimation from './docs-nav-animation';
import docsearch from './ds';

setupDocsNavAnimation();

anchors.add('.main h2, .main h3, .main h4, .main h5, .main h6');

docsearch();
