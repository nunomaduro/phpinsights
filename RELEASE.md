# Release process

Upon releasing a new version there's some checks and updates to be made:

- Clear your local repository with: `git add . && git reset --hard && git checkout master`
- Update the version number in `src/Domain/Kernel` file
- Update the version number in `docs/package.json` file
- Check the contents on https://github.com/nunomaduro/phpinsights/compare/{latest_tag}...master and update the [changelog](CHANGELOG.md) file with the modifications on this release
> Note: make sure that there is no breaking changes and you may use `git tag --list` to check the latest release
- Commit the `src/Domain/Kernel`, `docs/package.json` and `CHANGELOG.md` with the message: `git commit -m "Bumps version to v{version}"`
- `git push`
- `git tag {new_version}`
- `git push --tags`
