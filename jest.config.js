module.exports = {
    collectCoverage: true,
    collectCoverageFrom: ['resources/js/**/*.{js,jsx,ts,tsx}'],
    coverageDirectory: 'coverage',
    testEnvironment: 'jsdom',
    setupFilesAfterEnv: ['<rootDir>/jest.setup.js'],
    moduleDirectories: ['node_modules'],
    moduleNameMapper: {
     
    },
    transform: {
        '^.+\\.(js|jsx|ts|tsx)$': 'babel-jest',
        ".+\\.(css|styl|less|sass|scss|png|jpg|ttf|woff|woff2|svg)$": "jest-transform-stub"
    },
    transformIgnorePatterns: [],
    testMatch: [
        '<rootDir>/resources/js/**/*.(spec|test).{js,jsx,ts,tsx}',
    ],
};
