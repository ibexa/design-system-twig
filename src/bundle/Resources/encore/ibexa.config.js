const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('ibexa-design-system-accordion-js', [path.resolve(__dirname, '../public/ts/components/accordion.ts')]).addEntry(
        'ibexa-design-system-expander-js',
        [path.resolve(__dirname, '../public/ts/components/expander.ts')],
    );
};
