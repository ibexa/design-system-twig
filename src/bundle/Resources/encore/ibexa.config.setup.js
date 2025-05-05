const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-design-system': path.resolve('./vendor/ibexa/design-system-twig'),
    });
};
