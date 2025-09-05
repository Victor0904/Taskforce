module.exports = {
    root: true,
    env: {
        node: true,
        browser: true,
        es2022: true,
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/vue3-essential',
    ],
    parserOptions: {
        ecmaVersion: 2022,
        sourceType: 'module',
    },
    plugins: [
        'vue',
    ],
    rules: {
        'no-console': 'warn',
        'no-debugger': 'warn',
        'no-unused-vars': 'warn',
        'vue/multi-word-component-names': 'off',
        'vue/no-unused-vars': 'warn',
    },
    ignorePatterns: [
        'dist/',
        'node_modules/',
        'coverage/',
    ],
}
