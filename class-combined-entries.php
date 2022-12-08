<?php

final class GravityExport_CombinedEntries {
	/**
	 * The mapping.
	 * @var array
	 */
	private $mapping;

	/**
	 * The main form ID.
	 * @var int
	 */
	private $form_id;

	/**
	 * The form id's to combine into the results.
	 * @var int[]
	 */
	private $combined_form_ids = [];

	/**
	 * Whether to add sorting by form_id.
	 * @var bool
	 */
	private $should_sort = false;

	/**
	 * Singleton holder.
	 * @var null|self
	 */
	private static $_instance = null;

	/**
	 * Setting up the mapping configuration.
	 */
	private function __construct() {
		add_filter( 'gfexcel_event_download', \Closure::fromCallable( [ $this, 'initialize' ] ) );
	}

	/**
	 * Instantiates and returns a single instance.
	 * @return self
	 */
	public static function get_instance(): self {
		if ( ! self::$_instance instanceof self ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initializes the hooks when the current downloaded form has mapping to any provided combined forms.
	 */
	private function initialize( int $form_id ): void {
		$this->mapping = apply_filters( 'gk-gravityexport-combined-entries-mapping', [] );;

		$combined_form_ids       = array_map( 'intval', explode( ',', rgget( 'combine' ) ) );
		$this->combined_form_ids = array_intersect( $combined_form_ids, array_keys( $this->mapping[ $form_id ] ?? [] ) );
		$this->form_id           = $form_id;


		if ( ! isset( $this->mapping[ $form_id ] ) || ! $this->combined_form_ids ) {
			// No mapping for this form or any of the provided combining forms.
			return;
		}

		add_filter( 'gfexcel_get_entries', \Closure::fromCallable( [ $this, 'add_combined_entries' ] ), 10, 5 );
		add_filter( 'gform_gf_query_sql', \Closure::fromCallable( [ $this, 'sort_by_form_id_first' ] ) );
	}

	/**
	 * Returns a single array containing the current form id and the available combined form ids.
	 * @return array[int]
	 */
	private function get_all_form_ids(): array {
		return array_merge( [ $this->form_id ], $this->combined_form_ids );
	}

	/**
	 * Add the entries from the combined forms into the entries stream.
	 */
	private function add_combined_entries(
		int $form_id,
		?int $feed_id,
		array $search_criteria,
		array $sorting,
		array $paging
	): array {
		$this->should_sort = true; // make sure to sort by form first on the query.

		return array_map(
			\Closure::fromCallable( [ $this, 'remap_entry' ] ),
			\GFAPI::get_entries( $this->get_all_form_ids(), $search_criteria, $sorting, $paging )
		);
	}

	/**
	 * Updates the sorting of the entries to force the entries of combined forms below the current form, and group them by form id.
	 */
	private function sort_by_form_id_first( array $sql_parts ): array {
		if ( $this->should_sort ) {
			$sql_parts['order'] = str_replace(
				'ORDER BY',
				sprintf( 'ORDER BY FIND_IN_SET(form_id, \'%s\'),', implode( ',', $this->get_all_form_ids() ) ),
				$sql_parts['order']
			);

			$this->should_sort = false; // reset for subsequent queries.
		}

		return $sql_parts;
	}

	/**
	 * Changes the field id's to match the fields on the original form.
	 */
	private function remap_entry( array $entry ): array {
		if ( (int) ( $entry['form_id'] ) === $this->form_id ) {
			return $entry;
		}

		$values = [];
		foreach ( $entry as $key => $value ) {
			if ( ! is_int( $key ) ) {
				// We only care about integers because those are the fields.
				continue;
			}

			if ( null !== $new_key = $this->mapping[ $this->form_id ][ $entry['form_id'] ][ $key ] ?? null ) {
				// Keep track of the values for the mapped fields on their mapped id.
				$values[ $new_key ] = $value;
			}

			// Remove all fields that aren't mapped to avoid accidental collision of id's.
			unset( $entry[ $key ] );
		}

		// Can't use array_merge because that will remove the correct keys
		foreach ( $values as $key => $value ) {
			$entry[ $key ] = $value;
		}

		return $entry;
	}
}
