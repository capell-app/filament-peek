'use strict'
Object.defineProperty(exports, '__esModule', { value: true })
exports.submitPublicAction = void 0
const api_1 = require('../api')
const perform = async (z, bundle) => {
    const payload = bundle.inputData.payload
        ? JSON.parse(String(bundle.inputData.payload))
        : {}
    return (0, api_1.requestJson)(
        z,
        bundle,
        `/api/public-actions/zapier/actions/${encodeURIComponent(bundle.inputData.action_key)}/submissions`,
        {
            method: 'POST',
            body: payload,
        },
    )
}
exports.submitPublicAction = {
    key: 'submit_public_action',
    noun: 'Public Action Submission',
    display: {
        label: 'Submit Public Action',
        description: 'Submits data to a configured Capell public action.',
    },
    operation: {
        inputFields: [
            {
                key: 'action_key',
                label: 'Action key',
                required: true,
                dynamic: 'find_public_action.key.name',
            },
            {
                key: 'payload',
                label: 'Payload JSON',
                required: false,
                type: 'text',
            },
        ],
        perform,
        sample: {
            success: true,
            message: 'Submitted',
        },
    },
}
