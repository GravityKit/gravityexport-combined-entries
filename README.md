# GravityExport - Combined Entries

Enables ability to merge entries from multiple forms into one GravityExport download.

## Installation

1. Upload plugin files to your `plugins` folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Add mapping configuration using the `gk-gravityexport-combined-entries-mapping` filter hook.
4. Use the download url for the main form and add `?combine=44,45` to merge the entries for forms `44` and `45`

```php
add_action( 'gk-gravityexport-combined-entries-mapping', function ( array $mapping ): array {
	$mapping[43] = [ // For form 43 (Main form)
		44 => [ // The combined form
			1 => 3, // Key = field ID on the combined form
			3 => 5, // Value = field ID on the main form
		],
		45 => [
			1 => 1,
			3 => 4,
		],
	];

	return $mapping;
} );
```

## Changelog

### Unreleased
* Bugfix: Fields with subfields weren't mapped properly.
* Bugfix: Sorting could have an ambiguous field name, and therefor fail.

### 0.1.0 on December 8, 2022

* Initial release
