const App = require('../dist')

test('exports the Capell Public Actions Zapier app', () => {
    expect(App.authentication).toBeDefined()
    expect(App.triggers.new_public_action_submission).toBeDefined()
    expect(App.creates.submit_public_action).toBeDefined()
    expect(App.searches.find_public_action).toBeDefined()
})
