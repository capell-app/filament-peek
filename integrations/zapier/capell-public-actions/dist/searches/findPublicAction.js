'use strict'
Object.defineProperty(exports, '__esModule', { value: true })
exports.findPublicAction = void 0
const api_1 = require('../api')
const perform = async (z, bundle) => {
    const response = await (0, api_1.requestJson)(
        z,
        bundle,
        '/api/public-actions/zapier/actions',
    )
    const key = bundle.inputData?.key
    return key
        ? response.actions.filter((action) => action.key === key)
        : response.actions
}
exports.findPublicAction = {
    key: 'find_public_action',
    noun: 'Public Action',
    display: {
        label: 'Find Public Action',
        description: 'Finds an active Capell public action by key.',
    },
    operation: {
        inputFields: [{ key: 'key', label: 'Action key', required: false }],
        perform,
        sample: {
            id: '1',
            key: 'access-gate.request',
            name: 'Request access',
        },
    },
}
