export function convertKeysToCamelCase(obj: any): any {
    if (Array.isArray(obj)) {
        return obj.map((item) => convertKeysToCamelCase(item));
    } else if (obj !== null && typeof obj === 'object') {
        const formattedObj: any = {};
        for (const key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                const formattedKey = key.replace(/_([a-z])/g, (g) => g[1].toUpperCase());
                formattedObj[formattedKey] = convertKeysToCamelCase(obj[key]);
            }
        }
        return formattedObj;
    }
    return obj;
}

export function convertKeysToSnakeCase(obj: any): any {
    if (Array.isArray(obj)) {
        return obj.map((item) => convertKeysToSnakeCase(item));
    } else if (obj !== null && typeof obj === 'object') {
        const formattedObj: any = {};
        for (const key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                const formattedKey = key.replace(/[A-Z]/g, (letter) => `_${letter.toLowerCase()}`);
                formattedObj[formattedKey] = convertKeysToSnakeCase(obj[key]);
            }
        }
        return formattedObj;
    }
    return obj;
}