'use strict'
Object.defineProperty(exports, '__esModule', { value: true })
const zapier_platform_core_1 = require('zapier-platform-core')
const authentication_1 = require('./authentication')
const submitPublicAction_1 = require('./creates/submitPublicAction')
const findPublicAction_1 = require('./searches/findPublicAction')
const newPublicActionSubmission_1 = require('./triggers/newPublicActionSubmission')
const App = {
    version: '0.1.0',
    platformVersion: zapier_platform_core_1.version,
    authentication: authentication_1.authentication,
    triggers: {
        [newPublicActionSubmission_1.newPublicActionSubmission.key]:
            newPublicActionSubmission_1.newPublicActionSubmission,
    },
    creates: {
        [submitPublicAction_1.submitPublicAction.key]:
            submitPublicAction_1.submitPublicAction,
    },
    searches: {
        [findPublicAction_1.findPublicAction.key]:
            findPublicAction_1.findPublicAction,
    },
}
exports.default = App
module.exports = App
