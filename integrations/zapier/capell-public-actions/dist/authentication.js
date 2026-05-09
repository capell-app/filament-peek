'use strict'
Object.defineProperty(exports, '__esModule', { value: true })
exports.authentication = void 0
const api_1 = require('./api')
const test = async (z, bundle) => {
    return (0, api_1.requestJson)(z, bundle, '/api/public-actions/zapier/me')
}
exports.authentication = {
    type: 'custom',
    fields: [
        {
            key: 'baseUrl',
            label: 'Capell site URL',
            required: true,
            type: 'string',
        },
        {
            key: 'token',
            label: 'Public Actions token',
            required: true,
            type: 'password',
        },
    ],
    test,
    connectionLabel: '{{site_name}} {{name}}',
}
