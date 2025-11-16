import '@testing-library/jest-dom';
import 'isomorphic-fetch'

Object.defineProperty(window, 'matchMedia', {
    writable: true,
    value: jest.fn().mockImplementation((query) => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: jest.fn(),
        removeListener: jest.fn(),
        addEventListener: jest.fn(),
        removeEventListener: jest.fn(),
        dispatchEvent: jest.fn(),
    })),
});

Object.defineProperty(global, 'TextEncoder', {
    value: class TextEncoder {
        encode(str) {
            return this.encode(str);
        }
    },
    writable: true,
    configurable: true,
});

Object.defineProperty(global, 'TextDecoder', {
    value: class TextDecoder {
        decode(str) {
            return this.decode(str);
        }
    },
    writable: true,
    configurable: true,
});
