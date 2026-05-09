import type { Bundle, ZObject } from 'zapier-platform-core'
import { type CapellAuthData } from '../api'
export declare const findPublicAction: {
    key: string
    noun: string
    display: {
        label: string
        description: string
    }
    operation: {
        inputFields: {
            key: string
            label: string
            required: boolean
        }[]
        perform: (
            z: ZObject,
            bundle: Bundle<
                CapellAuthData & {
                    key?: string
                }
            >,
        ) => Promise<
            Array<{
                id: string
                key: string
                name: string
            }>
        >
        sample: {
            id: string
            key: string
            name: string
        }
    }
}
