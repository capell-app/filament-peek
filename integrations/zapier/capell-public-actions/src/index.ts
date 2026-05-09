import { version as platformVersion } from 'zapier-platform-core'
import { authentication } from './authentication'
import { submitPublicAction } from './creates/submitPublicAction'
import { findPublicAction } from './searches/findPublicAction'
import { newPublicActionSubmission } from './triggers/newPublicActionSubmission'

const App = {
    version: '0.1.0',
    platformVersion,
    authentication,
    triggers: {
        [newPublicActionSubmission.key]: newPublicActionSubmission,
    },
    creates: {
        [submitPublicAction.key]: submitPublicAction,
    },
    searches: {
        [findPublicAction.key]: findPublicAction,
    },
}

export default App
module.exports = App
