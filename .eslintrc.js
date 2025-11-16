module.exports = {
    plugins: ['@typescript-eslint', 'import', 'cypress', 'prettier', 'react', 'jest'],
    extends: [
        'plugin:shopify/react',
        'plugin:shopify/polaris',
        'plugin:shopify/jest',
        'plugin:shopify/webpack',
        'plugin:import/typescript',
        'plugin:react/recommended',
        'plugin:jest/recommended',
        'prettier',
    ],
    rules: {
        'import/no-unresolved': 'off',
    },
    overrides: [
        {
            files: ['*.test.*'],
            rules: {
                'shopify/jsx-no-hardcoded-content': 'off',
            },
        },
    ],
};
