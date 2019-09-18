<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 */

/**
 * Represents the abstract class for the health check.
 */
abstract class WPSEO_Health_Check {

	const STATUS_GOOD = 'good';
	const STATUS_RECOMMENDED = 'recommended';
	const STATUS_CRITICAL = 'critical';

	/**
	 * Name of the test.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * The value of the section header in the Health check.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Section the result should be displayed in.
	 *
	 * @var string
	 */
	protected $status = '';

	/**
	 * What the badge should say with a color.
	 *
	 * @var array
	 */
	protected $badge = array(
		'label' => '',
		'color' => '',
	);

	/**
	 * Additional details about the results of the test.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * A link or button to allow the end user to take action on the result.
	 *
	 * @var string
	 */
	protected $actions = '';

	/**
	 * The name of the test.
	 *
	 * @var string
	 */
	protected $test = '';

	/**
	 * Whether or not the test should be ran on AJAX as well.
	 *
	 * @var bool True when is async, default false.
	 */
	protected $async = false;

	/**
	 * Runs the test and returns the result.
	 *
	 * @return array The result.
	 */
	abstract public function test();

	/**
	 * Registers the test to WordPress.
	 */
	public function register_test() {
		if ( $this->async ) {
			add_filter( 'wp_ajax_site_status_tests', array( $this, 'add_async_test' ) );

			return;
		}

		add_filter( 'site_status_tests', array( $this, 'add_test' ) );
	}

	/**
	 * Runs the test.
	 *
	 * @param array $tests Array with the current tests.
	 *
	 * @return array The extended array.
	 */
	public function add_test( $tests ) {
		$tests['direct'][ $this->name ] = array(
			'test' => array( $this, 'get_test_result' ),
			'name' => $this->name,
		);

		return $tests;
	}

	/**
	 * Runs the test in async mode.
	 *
	 * @param array $tests Array with the current tests.
	 *
	 * @return array The extended array.
	 */
	public function add_async_test( $tests ) {
		$tests['async'][ $this->name ] = array(
			'test' => array( $this, 'get_test_result' ),
			'name' => $this->name,
		);

		return $tests;
	}

	/**
	 * Formats the test result as an array.
	 *
	 * @return array The formatted test result.
	 */
	public function get_test_result() {
		$this->test();

		return array(
			'label'       => $this->label,
			'status'      => $this->status,
			'badge'       => $this->get_badge(),
			'description' => $this->description,
			'actions'     => $this->actions,
		);
	}

	/**
	 * Retrieves the badge and ensure usable values are set.
	 *
	 * @return array The proper formatted badge.
	 */
	protected function get_badge() {
		if ( ! is_array( $this->badge ) ) {
			$this->badge = array();
		}

		if ( empty( $this->badge['label'] ) ) {
			$this->badge['label'] = __( 'SEO', 'wordpress-seo' );
		}

		if ( empty( $this->badge['color'] ) ) {
			$this->badge['color'] = 'green';
		}

		return $this->badge;
	}
}
