import getIbexaConfig from '@ibexa/eslint-config/eslint';

export default [
    ...getIbexaConfig({ react: false }),
    {
        files: ['**/*.ts'],
        rules: {
            'no-magic-numbers': ['error', { ignore: [-1, 0] }],
            '@typescript-eslint/unbound-method': 'off',
        },
    },
];
