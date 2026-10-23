---
name: php-zookeeper-release
description: Use when preparing or completing a php-zookeeper version release, updating release metadata, or validating its PEAR tgz distribution.
---

# php-zookeeper Release

Keep the user-confirmed version, validated PEAR source archive, and exact Git commit aligned as one release. Communicate with the user in Chinese. This skill applies only to this project.

## 1. Confirm the release baseline

- Read project instructions. Consult existing build records according to the user's knowledge-base conventions, and verify recorded environments before reusing them.
- Check `git status --short --branch`, the remote default branch, branches, and tags. Refresh remote refs and inspect changes since the previous official tag. Identify any existing uncommitted work and preserve the user's changes.
- Propose a version based on the changes: bug fixes normally warrant a patch increment; compatible new features normally warrant a minor increment. **Obtain confirmation of the exact version before editing version files or creating its release branch.** Check both local and remote tags first; never overwrite an existing release.
- Identify the actual mainline branch. If the user says `main` but the repository only has `master`, explain the discrepancy and confirm the target. Do not create or rename the mainline branch yourself. Reuse versions, targets, and operation authorizations already confirmed in the conversation.
- Create `release/v<version>` from the synchronized target mainline branch, following Git Flow. A clean checkout can switch branches directly. If a worktree is needed, keep it inside the project. If sandbox restrictions prevent creation, request elevated access; do not bypass the restriction by changing locations or falling back to the current checkout.

Loading this skill does not authorize publishing or pushing. If the user has authorized the complete release workflow, continue through merging and pushing. If the request covers only preparation or validation, finish that scope and preserve the results.

## 2. Synchronize release metadata

| File | Changes |
| --- | --- |
| `ChangeLog` | Move this cycle's `Unreleased` entries into `Version <version>`, covering actual changes since the previous tag and preserving history |
| `php_zookeeper.h` | Update `PHP_ZOOKEEPER_VERSION` |
| `package.xml` | Update the top-level release version, actual release date, stability, and notes; prepend the current release to the changelog |

Determine the release date in the user's timezone. Preserve historical dates and notes. The API version in `package.xml` is independent of the release version; retain it when the API has not changed. Do not globally replace historical versions. Search for other active internal version constants, check that release notes match the code, and run `git diff --check`.

## 3. Prepare and record build dependencies

Prefer an available environment. Verify that PHP CLI, phpize, php-config, and PHP headers agree on version/API and NTS/ZTS configuration. Confirm availability of PEAR, a C compiler, make, autoconf, libtool, and the libzookeeper headers and runtime library.

**For every validation, record libzookeeper's source type, repository or download URL, distribution/mirror, exact package or source version, actual paths, and the library version reported by the module.** "Using the local library" is insufficient. Recheck provenance when reusing an old directory.

If local tools are missing, use [references/toolchain.md](references/toolchain.md) to select a build environment. Store logs and checksum manifests in the project's ignored `reports/release-<version>/` directory. Exclude toolchains, build output, and tgz archives from source commits. Verify ignore rules without automatically broadening them.

## 4. Package with PEAR and inspect the archive

From the project root containing the completed release metadata, run `pear package package.xml`. Save the command, full log, and exit status. The distribution artifact is `zookeeper-<version>.tgz`; do not replace PEAR packaging with `git archive` or a plain tar command.

Before extracting, inspect the archive and save its SHA256 and per-file manifest:

1. Derive the complete file set from the nested contents/dir/file entries in the source `package.xml`. Do not hardcode a file count.
2. Require exactly the root `package.xml` and the declared files under `zookeeper-<version>/`. Reject duplicate paths, absolute paths, `..`, links, and additional build artifacts.
3. Compare each archived file byte-for-byte with the release source, check its MD5 against the packaged manifest, and record SHA256. Check that sources and headers required by `config.m4` are not missing from the manifest.
4. Compare the packaged and source manifests for release/API versions, date, stability, notes, contents, and current release history. PEAR rewrites XML formatting and populates checksums, so compare metadata semantically rather than requiring identical XML bytes.

## 5. Build from the extracted archive

Extract **this exact tgz** into a new empty directory. Do not use repository sources omitted from the package during the build. Run the following example from the project root after selecting a matching toolchain. Use `set -euo pipefail` in the shell and convert relative paths to absolute paths first:

```bash
release_version='<user-confirmed-version>'
release_root="$PWD"
release_report="$release_root/reports/release-$release_version"
mkdir -p "$release_report"
release_unpack=$(mktemp -d "$release_report/unpacked.XXXXXX")
tar -xzf "$release_root/zookeeper-$release_version.tgz" -C "$release_unpack"
cd "$release_unpack/zookeeper-$release_version"
phpize > "$release_report/phpize.log" 2>&1
./configure --enable-zookeeper > "$release_report/configure.log" 2>&1
make -j2 > "$release_report/make.log" 2>&1
RELEASE_EXPECTED_VERSION="$release_version" php -n \
  -d "extension=$PWD/modules/zookeeper.so" -r '
  if (!extension_loaded("zookeeper") ||
      phpversion("zookeeper") !== getenv("RELEASE_EXPECTED_VERSION") ||
      !class_exists("Zookeeper")) { exit(1); }
  echo "PASS: zookeeper ", phpversion("zookeeper"), PHP_EOL;
' > "$release_report/smoke.log" 2>&1
php -n -d "extension=$PWD/modules/zookeeper.so" --ri zookeeper \
  > "$release_report/module-info.log" 2>&1
cd "$release_root"
```

For a non-system prefix, explicitly add the matching `--with-php-config`, `--with-libzookeeper-dir`, and applicable `--with-libdir` configure options. Set the corresponding runtime library search path for dynamic loading, and record all actual arguments.

Inspect each stage's exit status and logs. On failure, stop tagging, pushing, and merging. Do not modify extracted sources or suppress compiler errors and then claim that the original archive passed. If release sources need changes, package again, extract again, and validate again.

The baseline acceptance checks are archive completeness, phpize/configure/make, and module loading/version checks. Run service integration tests, ZTS checks, and version matrices according to the current request and available environment. Clearly distinguish tested and untested coverage; a successful build does not establish those results.

## 6. Fix the release commit, retain the archive, and push the tag

- Review the final diff and commit using an English Conventional Commit, such as `chore(release): prepare <version>`. Commit messages must not contain `Co-Authored-By`.
- Record the commit SHA and compare archived sources against that commit file-by-file, including source `package.xml` metadata. Any change to packaging inputs after validation invalidates the result and requires repackaging and revalidation. Changing only a Git commit message while retaining identical file contents does not require recompilation.
- Keep the original validated tgz in the project root, together with checksums and logs. Do not leave the only copy in a temporary directory scheduled for cleanup.
- Recheck remote tag state and create an annotated `v<version>` tag pointing to the release commit. With authorization for the corresponding operations, prefer `git push --atomic -u origin <release-branch> refs/tags/<tag>`. If atomic pushes are unsupported, push separately and record each result. After partial success, inspect remote state before continuing; do not automatically delete or overwrite remote refs.
- Do not force-push or move existing release tags. Pause to resolve tag conflicts or unclear provenance. Resume a partially completed release from its verified checkpoint.

## 7. Merge into the confirmed mainline branch

After successfully pushing the tag and release branch, refresh the mainline branch and check whether it has advanced since the release branch was created. If concurrent updates exist, assess conflicts and their effect on the archive and validation. Do not apply old archive validation results to a changed merge result.

Switch to the confirmed mainline branch and merge with `--no-ff`, using a message such as `chore(release): merge <version> into <target-branch>`. Without concurrent changes, use `git diff --exit-code <tag> HEAD` to prove that the merged tree equals the release tree, then recheck archive/commit correspondence, SHA256, and module version. If additional code is present, complete the appropriate build and tests before proceeding.

Push the mainline branch. Use `git ls-remote` to verify the remote mainline, release branch, annotated tag, and its dereferenced commit. Check local tracking state and the working tree. If a push is rejected, inspect remote changes rather than force-pushing. Retain the release branch and validated archive by default. Do not additionally create a GitHub Release, upload to PECL, or delete files.

## Delivery record

The final record must include:

- Release/API versions and date, release commit SHA, release branch, tag target, and final mainline SHA.
- Absolute tgz path, SHA256, manifest/source correspondence, and validation log paths.
- PHP/PEAR/compiler versions, NTS/ZTS configuration, libzookeeper provenance and version, installation prefix, and actual build arguments.
- Results for each validation stage, executed tests and coverage limits, remote verification, and incomplete steps.

Common mistakes: merging into a nonexistent branch named in conversation; changing historical/API versions alongside the release version; building in the source checkout instead of the extracted archive; editing release inputs after archive validation; inferring current dependencies from historical logs; reporting unsuccessful pushes as complete.
