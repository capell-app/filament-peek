import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import Tooltip from '@ryangjchandler/alpine-tooltip'

import './utilities/lightbox'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Tooltip)
    window.Alpine.plugin(AlpineFloatingUI)
})
