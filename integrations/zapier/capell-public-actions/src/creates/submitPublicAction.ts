import type { Bundle, ZObject } from 'zapier-platform-core'
import { requestJson, type CapellAuthData } from '../api'

type SubmitResponse = {
    success: boolean
    message?: string | null
    redirect_url?: string | null
    created_model_type?: string | null
    created_model_id?: string | null
}

const perform = async (
    z: ZObject,
    bundle: Bundle<CapellAuthData & { action_key: string; payload?: string }>,
): Promise<SubmitResponse> => {
    const payload = bundle.inputData.payload
        ? JSON.parse(String(bundle.inputData.payload))
        : {}

    return requestJson<SubmitResponse>(
        z,
        bundle,
        `/api/public-actions/zapier/actions/${encodeURIComponent(bundle.inputData.action_key)}/submissions`,
        {
            method: 'POST',
            body: payload,
        },
    )
}

export const submitPublicAction = {
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
