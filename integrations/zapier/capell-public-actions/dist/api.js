'use strict'
Object.defineProperty(exports, '__esModule', { value: true })
exports.requestJson = exports.apiUrl = void 0
const apiUrl = (bundle, path) => {
    const baseUrl = String(bundle.authData.baseUrl).replace(/\/+$/, '')
    const normalizedPath = path.startsWith('/') ? path : `/${path}`
    return `${baseUrl}${normalizedPath}`
}
exports.apiUrl = apiUrl
const requestJson = async (z, bundle, path, options = {}) => {
    const response = await z.request({
        ...options,
        url: (0, exports.apiUrl)(bundle, path),
        headers: {
            Authorization: `Bearer ${String(bundle.authData.token)}`,
            Accept: 'application/json',
            ...options.headers,
        },
    })
    return response.json
}
exports.requestJson = requestJson
