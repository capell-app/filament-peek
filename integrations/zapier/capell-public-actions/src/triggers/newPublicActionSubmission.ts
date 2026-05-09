import type { Bundle, ZObject } from 'zapier-platform-core'
import { requestJson, type CapellAuthData } from '../api'

type SubmissionResponse = {
    submissions: Array<Record<string, unknown>>
}

const perform = async (
    z: ZObject,
    bundle: Bundle<CapellAuthData & { after_id?: string }>,
): Promise<Array<Record<string, unknown>>> => {
    const afterId = bundle.inputData?.after_id
        ? `?after_id=${encodeURIComponent(String(bundle.inputData.after_id))}`
        : ''
    const response = await requestJson<SubmissionResponse>(
        z,
        bundle,
        `/api/public-actions/zapier/submissions${afterId}`,
    )

    return response.submissions
}

export const newPublicActionSubmission = {
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
