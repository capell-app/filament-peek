const fs = require('fs')
const path = require('path')

const root = process.cwd()
const manifestPath = path.join(root, 'docs/package-screenshot-manifest.json')
const packageDirs = fs
    .readdirSync(path.join(root, 'packages'), { withFileTypes: true })
    .filter((entry) => entry.isDirectory())
    .map((entry) => entry.name)

const failures = []

const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'))
const manifestPackages = new Set(
    (manifest.entries ?? []).map((entry) => entry.package),
)

for (const packageName of packageDirs) {
    const screenshotsPath = path.join(
        root,
        'packages',
        packageName,
        'docs/screenshots.json',
    )

    if (!fs.existsSync(screenshotsPath)) {
        continue
    }

    try {
        const packageManifest = JSON.parse(
            fs.readFileSync(screenshotsPath, 'utf8'),
        )

        if (packageManifest.package !== packageName) {
            failures.push(
                `${screenshotsPath}: package key "${packageManifest.package}" does not match directory "${packageName}"`,
            )
        }

        if (!manifestPackages.has(packageName)) {
            failures.push(
                `${screenshotsPath}: package is missing from docs/package-screenshot-manifest.json`,
            )
        }
    } catch (error) {
        failures.push(`${screenshotsPath}: ${error.message}`)
    }
}

for (const packageName of manifestPackages) {
    const screenshotsPath = path.join(
        root,
        'packages',
        packageName,
        'docs/screenshots.json',
    )

    if (!fs.existsSync(screenshotsPath)) {
        failures.push(
            `docs/package-screenshot-manifest.json references "${packageName}" but ${screenshotsPath} does not exist`,
        )
    }
}

if (failures.length > 0) {
    throw new Error(
        `Screenshot manifest validation failed:\n${failures.map((failure) => `- ${failure}`).join('\n')}`,
    )
}

console.log('Screenshot manifests are in sync.')
