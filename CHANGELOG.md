# Changelog #

## [1.0.0-RC.1](https://github.com/platform-coop-toolkit/coop-library-framework/compare/1.0.0-alpha.3...1.0.0-rc.1) (2020-03-31)


### Bug Fixes

* make sub fields required to prevent empty input (resolves [#310](https://github.com/platform-coop-toolkit/coop-library-framework/issues/310)) ([bb14052](https://github.com/platform-coop-toolkit/coop-library-framework/commit/bb14052d4157fd20474f1a88853eeeb7a0eabc3c))
* set null publication date to em dash ([#334](https://github.com/platform-coop-toolkit/coop-library-framework/issues/334)) ([fb856db](https://github.com/platform-coop-toolkit/coop-library-framework/commit/fb856dba75ea0448498b1c311947f0b54b3ccda2)), closes [platform-coop-toolkit/coop-library#245](https://github.com/platform-coop-toolkit/coop-library/issues/245)


### Features

* add hidden favorites field ([80e7f4d](https://github.com/platform-coop-toolkit/coop-library-framework/commit/80e7f4d33eaa51667a685e2325b5aaf94ff9da13))
* add URL helper text (resolves [#311](https://github.com/platform-coop-toolkit/coop-library-framework/issues/311)) ([#333](https://github.com/platform-coop-toolkit/coop-library-framework/issues/333)) ([0b18052](https://github.com/platform-coop-toolkit/coop-library-framework/commit/0b1805217638a351c4856424024b0e3998d309ce))
* differentiate between resource and display language ([#317](https://github.com/platform-coop-toolkit/coop-library-framework/issues/317)) ([e67a341](https://github.com/platform-coop-toolkit/coop-library-framework/commit/e67a34121bbac3bc46eaf0fad3f95014382e2f46))

## 1.0.0-alpha.3 ##
**Bug Fixes**

* add wp-a11y dependency to scripts ([4cbd457](https://github.com/platform-coop-toolkit/coop-library-framework/commit/4cbd4570219b858146d3e3d4236f5cfcc9afc5cc))
* publication year is optional ([#293](https://github.com/platform-coop-toolkit/coop-library-framework/issues/293)) ([b6c6f2c](https://github.com/platform-coop-toolkit/coop-library-framework/commit/b6c6f2c275da7946083fbbed49c54c82e8c5d08a))
* reorder taxonomy menus on dashboard ([#296](https://github.com/platform-coop-toolkit/coop-library-framework/issues/296)) ([3d22d2c](https://github.com/platform-coop-toolkit/coop-library-framework/commit/3d22d2c28081ff1826bc5da3424a065e145c89db))

**Features**

* add a setting to indicate whether or not a resource is paywalled ([#288](https://github.com/platform-coop-toolkit/coop-library-framework/issues/288)) ([b1e708f](https://github.com/platform-coop-toolkit/coop-library-framework/commit/b1e708f1e6663a088b98f540d104a168e906a20f))
* add date published column ([66d8201](https://github.com/platform-coop-toolkit/coop-library-framework/commit/66d82015144b8b53098ecd7f6b20b08d9aea2bf8))
* add Koko Analytics for view tracking ([#262](https://github.com/platform-coop-toolkit/coop-library-framework/issues/262)) ([a7f0b55](https://github.com/platform-coop-toolkit/coop-library-framework/commit/a7f0b5593c93ae5fc7bb5124450632987f5d6061))
* add Relevanssi for improved searching ([#297](https://github.com/platform-coop-toolkit/coop-library-framework/issues/297)) ([5e694f3](https://github.com/platform-coop-toolkit/coop-library-framework/commit/5e694f3bdd4b40d50ab073e78036fd97cab0caaa))
* add support for parent goals (fixes [#263](https://github.com/platform-coop-toolkit/coop-library-framework/issues/263)) ([#264](https://github.com/platform-coop-toolkit/coop-library-framework/issues/264)) ([5595099](https://github.com/platform-coop-toolkit/coop-library-framework/commit/559509918ef0556ee15e0a2b39b26a1addfaaaca))
* add term order field (resolves [#294](https://github.com/platform-coop-toolkit/coop-library-framework/issues/294)) ([#295](https://github.com/platform-coop-toolkit/coop-library-framework/issues/295)) ([db6eef8](https://github.com/platform-coop-toolkit/coop-library-framework/commit/db6eef823d3b5c11b5545eee5befa9f4c1588a23))
* limit short title to 72 characters (resolves [#271](https://github.com/platform-coop-toolkit/coop-library-framework/issues/271)) ([#289](https://github.com/platform-coop-toolkit/coop-library-framework/issues/289)) ([71ece51](https://github.com/platform-coop-toolkit/coop-library-framework/commit/71ece519e34d7061f9a47370273a6cd92cf09da4))
* migrate fields to ACF Pro ([#308](https://github.com/platform-coop-toolkit/coop-library-framework/issues/308)) ([8341619](https://github.com/platform-coop-toolkit/coop-library-framework/commit/83416192daf2ebb73b79104a4e6384205d1ec149))
* remove Polylang dependency ([028607e](https://github.com/platform-coop-toolkit/coop-library-framework/commit/028607ec522464fab4580ea6d5131b1b2cfd6127))
* remove resources from Polylang ([264aaa2](https://github.com/platform-coop-toolkit/coop-library-framework/commit/264aaa2270b22a4ab03d5e6d78ec5a747ea77f47))
* remove strings translation ([3fb673d](https://github.com/platform-coop-toolkit/coop-library-framework/commit/3fb673d75967a00957b76ca02b1e7fec4a24a04e))
* show added date in resource list ([20d8ab3](https://github.com/platform-coop-toolkit/coop-library-framework/commit/20d8ab33d28adbd86791f7e567cf1915a976a945))
* store language in metadata ([#316](https://github.com/platform-coop-toolkit/coop-library-framework/issues/316)) ([ab499a8](https://github.com/platform-coop-toolkit/coop-library-framework/commit/ab499a87551905d4a5e562fdb4088848a79ed4bd))

## 1.0.0-alpha.2 ##
**Major Changes**

- Rename to Co-op Library Framework: #200

**Minor Changes**

- Add favorites to metadata: #258
- Add commitlint configuration: #243
- Add GitHub Actions: #186

## 1.0.0-alpha.1 ##
* Initial release.
