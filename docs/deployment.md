# Deployment
This application has adopted a continuous integration and continuous deployment flow
using [CircleCI](https://circleci.com/).

Each pull request created using the master branch as a base will be required to pass
full status checks with the full test suite to run without failure before having the
opportunity to merge the pull request into the master branch.

#### Deploying to Master
All pull requests into master branch will be automatically deployed to this application's
staging environment

#### Deploying to Production
All pull requests into production branch will be automatically deployed to this application's
production environment

