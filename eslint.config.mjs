import getIbexaConfig from '@ibexa/eslint-config/eslint';

export default [
    ...getIbexaConfig({ react: false }),
    {
        files: ['**/*.ts'],
        rules: {
            '@typescript-eslint/unbound-method': 'off',
        },
    },
];
