import { submitPublicAction } from './creates/submitPublicAction'
import { findPublicAction } from './searches/findPublicAction'
import { newPublicActionSubmission } from './triggers/newPublicActionSubmission'
declare const App: {
    version: string
    platformVersion: string
    authentication: {
        type: string
        fields: {
            key: string
            label: string
            required: boolean
            type: string
        }[]
        test: (
            z: import('zapier-platform-core').ZObject,
            bundle: import('zapier-platform-core').Bundle<any>,
        ) => Promise<{
            id: string
            name: string
            site_name?: string | null
        }>
        connectionLabel: string
    }
    triggers: {
        [newPublicActionSubmission.key]: {
            key: string
            noun: string
            display: {
                label: string
                description: string
            }
            operation: {
                perform: (
                    z: import('zapier-platform-core').ZObject,
                    bundle: import('zapier-platform-core').Bundle<
                        import('./api').CapellAuthData & {
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
    }
    creates: {
        [submitPublicAction.key]: {
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
                    z: import('zapier-platform-core').ZObject,
                    bundle: import('zapier-platform-core').Bundle<
                        import('./api').CapellAuthData & {
                            action_key: string
                            payload?: string
                        }
                    >,
                ) => Promise<{
                    success: boolean
                    message?: string | null
                    redirect_url?: string | null
                    created_model_type?: string | null
                    created_model_id?: string | null
                }>
                sample: {
                    success: boolean
                    message: string
                }
            }
        }
    }
    searches: {
        [findPublicAction.key]: {
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
                    z: import('zapier-platform-core').ZObject,
                    bundle: import('zapier-platform-core').Bundle<
                        import('./api').CapellAuthData & {
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
    }
}
export default App
