import type { Bundle, ZObject } from 'zapier-platform-core'
import { requestJson, type CapellAuthData } from '../api'

type ActionsResponse = {
    actions: Array<{ id: string; key: string; name: string }>
}

const perform = async (
    z: ZObject,
    bundle: Bundle<CapellAuthData & { key?: string }>,
): Promise<Array<{ id: string; key: string; name: string }>> => {
    const response = await requestJson<ActionsResponse>(
        z,
        bundle,
        '/api/public-actions/zapier/actions',
    )
    const key = bundle.inputData?.key

    return key
        ? response.actions.filter((action) => action.key === key)
        : response.actions
}

export const findPublicAction = {
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
