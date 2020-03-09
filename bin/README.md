# Conversion Scripts

## Convert Metadata from Custom Metaboxes 2 to Advanced Custom Fields

To convert your resource library's metadata from Custom Metaboxes 2 to Advanced Custom Fields, run the following command from the WordPress root using [wp-cli](https://wp-cli.org):

```bash
wp eval-file wp-content/plugins/coop-library-framework/bin/convert-meta.php
```

In order to see the updated metadata on the resource library front-end, you'll need to update to the [1.0.0-alpha.2](https://github.com/platform-coop-toolkit/coop-library/milestones/1.0.0-alpha.2) version of the Co-op Library theme.

## Convert Resource Language from Polylang to Advanced Custom Fields

To convert your resources' language from a Polylang language to a metadata field managed through Advanced Custom Fields, run the following command from the WordPress root using [wp-cli](https://wp-cli.org):

```bash
wp eval-file wp-content/plugins/coop-library-framework/bin/convert-language.php
```

In order to see the updated language on the resource library front-end, you'll need to update to the [1.0.0-alpha.2](https://github.com/platform-coop-toolkit/coop-library/milestones/1.0.0-alpha.2) version of the Co-op Library theme.
