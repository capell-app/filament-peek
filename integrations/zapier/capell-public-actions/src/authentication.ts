import type { Bundle, ZObject } from 'zapier-platform-core'
import { requestJson } from './api'

type AccountResponse = {
    id: string
    name: string
    site_name?: string | null
}

const test = async (
    z: ZObject,
    bundle: Bundle<any>,
): Promise<AccountResponse> => {
    return requestJson<AccountResponse>(
        z,
        bundle,
        '/api/public-actions/zapier/me',
    )
}

export const authentication = {
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
