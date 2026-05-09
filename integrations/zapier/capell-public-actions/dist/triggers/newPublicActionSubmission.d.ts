import type { Bundle, ZObject } from 'zapier-platform-core'
import { type CapellAuthData } from '../api'
export declare const newPublicActionSubmission: {
    key: string
    noun: string
    display: {
        label: string
        description: string
    }
    operation: {
        perform: (
            z: ZObject,
            bundle: Bundle<
                CapellAuthData & {
                    after_id?: string
                }
            >,
        ) => Promise<Array<Record<string, unknown>>>
        sample: {
            id: string
            action_key: string
            submitted_at: string
            payload: {
                email: string
            }
        }
    }
}
