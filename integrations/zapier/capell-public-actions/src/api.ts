import type { Bundle, ZObject } from 'zapier-platform-core'

export type CapellAuthData = {
    baseUrl: string
    token: string
}

export const apiUrl = (bundle: Bundle<any>, path: string): string => {
    const baseUrl = String(bundle.authData.baseUrl).replace(/\/+$/, '')
    const normalizedPath = path.startsWith('/') ? path : `/${path}`

    return `${baseUrl}${normalizedPath}`
}

export const requestJson = async <T>(
    z: ZObject,
    bundle: Bundle<any>,
    path: string,
    options: Record<string, unknown> = {},
): Promise<T> => {
    const response = await z.request({
        ...options,
        url: apiUrl(bundle, path),
        headers: {
            Authorization: `Bearer ${String(bundle.authData.token)}`,
            Accept: 'application/json',
            ...(options.headers as Record<string, string> | undefined),
        },
    })

    return response.json as T
}
