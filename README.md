# GravityExport - Combined Entries

Enables ability to merge entries from multiple forms into one GravityExport download.

## Installation

0. **[Download the plugin file](https://github.com/gravityview/gravityexport-combined-entries/archive/refs/heads/main.zip)**
1. Upload plugin files to your `plugins` folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Add mapping configuration using the `gk-gravityexport-combined-entries-mapping` filter hook. [Not sure how to add code to your site?](https://docs.gravitykit.com/article/210-where-to-put-code-samples)

```php
/**
 * THIS CODE IS AN EXAMPLE. DO NOT USE WITHOUT MODIFICATION.
 * 
 * You need to update the form ids (43, 44, and 45) with your own form IDs.
 * Also update the field IDs to match the IDs in your parent and combined forms.
 */
add_action( 'gk-gravityexport-combined-entries-mapping', function ( array $mapping ): array {

	// For form 43 (the main form)
	$mapping[43] = [

		// The first combined form (update with your own form ID)
		44 => [
			1 => 3, // Key = field ID on the combined form. Update with your own field ID.
			3 => 5, // Value = field ID on the main form. Update with your own field ID.
		],
		
		// The second connected form (update with your own form ID)
		45 => [
			1 => 1, // Key = field ID on the combined form. Update with your own field ID.
			3 => 4, // Value = field ID on the main form. Update with your own field ID.
		],
	];

	return $mapping;
} );
```

4. Use the download url for the main form and add `?combine=44,45` to merge the entries for forms `44` and `45`, for example:

<pre>https://www.example.com/gf-entries-in-excel/fff97e7f54b72431ab1cb5dd16ecc<strong>?combine=44,45</strong></pre>

## Changelog

### 0.1.0 on December 8, 2022

* Initial release
