'use strict'
Object.defineProperty(exports, '__esModule', { value: true })
exports.newPublicActionSubmission = void 0
const api_1 = require('../api')
const perform = async (z, bundle) => {
    const afterId = bundle.inputData?.after_id
        ? `?after_id=${encodeURIComponent(String(bundle.inputData.after_id))}`
        : ''
    const response = await (0, api_1.requestJson)(
        z,
        bundle,
        `/api/public-actions/zapier/submissions${afterId}`,
    )
    return response.submissions
}
exports.newPublicActionSubmission = {
    key: 'new_public_action_submission',
    noun: 'Public Action Submission',
    display: {
        label: 'New Public Action Submission',
        description:
            'Triggers when a Capell public action receives a new submission.',
    },
    operation: {
        perform,
        sample: {
            id: '123',
            action_key: 'access-gate.request',
            submitted_at: '2026-05-09T10:00:00+00:00',
            payload: {
                email: 'person@example.com',
            },
        },
    },
}
