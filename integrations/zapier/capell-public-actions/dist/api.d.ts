import type { Bundle, ZObject } from 'zapier-platform-core'
export type CapellAuthData = {
    baseUrl: string
    token: string
}
export declare const apiUrl: (bundle: Bundle<any>, path: string) => string
export declare const requestJson: <T>(
    z: ZObject,
    bundle: Bundle<any>,
    path: string,
    options?: Record<string, unknown>,
) => Promise<T>
