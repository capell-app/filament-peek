import type { Bundle, ZObject } from 'zapier-platform-core'
import { type CapellAuthData } from '../api'
type SubmitResponse = {
    success: boolean
    message?: string | null
    redirect_url?: string | null
    created_model_type?: string | null
    created_model_id?: string | null
}
export declare const submitPublicAction: {
    key: string
    noun: string
    display: {
        label: string
        description: string
    }
    operation: {
        inputFields: (
            | {
                  key: string
                  label: string
                  required: boolean
                  dynamic: string
                  type?: undefined
              }
            | {
                  key: string
                  label: string
                  required: boolean
                  type: string
                  dynamic?: undefined
              }
        )[]
        perform: (
            z: ZObject,
            bundle: Bundle<
                CapellAuthData & {
                    action_key: string
                    payload?: string
                }
            >,
        ) => Promise<SubmitResponse>
        sample: {
            success: boolean
            message: string
        }
    }
}
export {}
