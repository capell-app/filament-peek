import type { Bundle, ZObject } from 'zapier-platform-core'
type AccountResponse = {
    id: string
    name: string
    site_name?: string | null
}
export declare const authentication: {
    type: string
    fields: {
        key: string
        label: string
        required: boolean
        type: string
    }[]
    test: (z: ZObject, bundle: Bundle<any>) => Promise<AccountResponse>
    connectionLabel: string
}
export {}
