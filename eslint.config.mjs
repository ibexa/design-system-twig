import getIbexaConfig from '@ibexa/eslint-config/eslint';
[
    ...getIbexaConfig({ react: false }),
    {
        files: ['**/*.stories.ts'],
        rules: {
            '@typescript-eslint/unbound-method': 'off',
        },
    },
];
