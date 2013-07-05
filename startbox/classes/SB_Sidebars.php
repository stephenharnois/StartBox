<?php
/**
 * StartBox Sidebars
 *
 * A class structure for handling sidebar registration,
 * output, markup, the works.
 *
 * @package StartBox
 * @subpackage Classes
 * @since 3.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Check to see if current theme supports sidebars, skip the rest if not
if ( ! current_theme_supports( 'sb-sidebars' ) )
	return;

/**
 * This is the main SB Sidebars class.
 *
 * You can extend this within your theme to alter the widget markup
 *
 * @since 2.5.0
 */
class SB_Sidebars {

	/**
	 * Variable for storing all registered sidebars, don't override this.
	 *
	 * @since 2.5.0
	 * @var array
	 */
	public $sidebars = array();

	/**
	 * Auto-load default and custom sidebars. Don't override this.
	 *
	 * @since 2.5.0
	 */
	function __construct() {

		// Grab the default supported sidebars
		$supported_sidebars = get_theme_support( 'sb-sidebars' );
		$this->sidebars = $supported_sidebars[0];

		// Register and activate all the sidebars
		add_action( 'init', array( $this, 'register_default_sidebars') );

		// Available hook for other functions
		do_action( 'sb_sidebars_init' );

	}

	/**
	 * Registers all default sidebars (don't override this)
	 *
	 * @since 2.5.0
	 */
	function register_default_sidebars() {

		// If there aren't any sidebars, skip the rest
		if ( empty( $this->sidebars ) )
			return;

		/* Get the available post layouts and store them in an array */
		foreach ( $this->sidebars as $sidebar ) {
			$this->register_sidebar( $sidebar );
		}

	}

	/**
	 * Register a sidebar (don't override this)
	 *
	 * @since 2.5.0
	 * @param array $args an array of arguments for naming and identifying a sidebar
	 */
	function register_sidebar( $args = '' ) {

		// Setup our defaults (all null, for the most part)
		$defaults = array(
			'name'        => '',
			'id'          => '',
			'description' => '',
			'editable'    => 1 // Makes this sidebar replaceable via SB Custom Sidebars extension
		);
		$sidebar = wp_parse_args( $args, $defaults );

		// Rudimentary sanitization for editable var
		$editable = ( $sidebar['editable'] ) ? 1 : 0;

		// Register the sidebar
		register_sidebar( apply_filters( 'sb_sidebars_register_sidebar', array(
			'id'            => esc_attr( $sidebar['id'] ),
			'name'          => esc_attr( $sidebar['name'] ),
			'description'   => esc_attr( $sidebar['description'] ),
			'editable'      => absint( $sidebar['editable'] ),
			'before_widget' => apply_filters( 'sb_sidebars_before_widget', '<aside id="%1$s" class="widget %2$s">', $sidebar['id'], $sidebar ),
			'after_widget'  => apply_filters( 'sb_sidebars_after_widget', '</aside><!-- #%1$s -->', $sidebar['id'], $sidebar ),
			'before_title'  => apply_filters( 'sb_sidebars_before_title', '<h1 class="widget-title">', $sidebar['id'], $sidebar ),
			'after_title'   => apply_filters( 'sb_sidebars_after_title', '</h1>', $sidebar['id'], $sidebar )
		), $sidebar ) );
	}

	/**
	 * Render markup and action hooks for a given sidebar (override this to customize your markup)
	 *
	 * @since 2.5.0
	 * @param string $location Unique ID applied to sidebar containers
	 * @param string $sidebar  The default sidebar to render
	 * @param string $classes  Additional classes to add to the container
	 */
	function do_sidebar( $location = null, $sidebar = null, $classes = null ) {

		// Maybe replace the default sidebar with a custom sidebar
		$sidebar = apply_filters( 'sb_do_sidebar', $sidebar, $location );

		// If the sidebar has widgets, or an action attached to it, commence output
		if ( is_active_sidebar( $sidebar ) || has_action( "sb_no_{$location}_widgets" ) ) {

			do_action( "sb_before_{$location}" );
			echo '<div id="' . esc_attr( $location ) . '" class="aside ' . esc_attr( $location ) . '-aside ' . esc_attr( $classes ) . '" role="complimentary">';
			do_action( "sb_before_{$location}_widgets" );

			if ( ! dynamic_sidebar( $sidebar ) )
				do_action( "sb_no_{$location}_widgets" );

			do_action( "sb_after_{$location}_widgets" );
			echo '</div><!-- #' . esc_attr( $location ) . ' .aside-' . esc_attr( $location ) . ' -->';
			do_action( "sb_after_{$location}" );
		}
	}

}
$GLOBALS['startbox']->sidebars = new SB_Sidebars;

/**
 * Wrapper Function for SB_Sidebars::register_sidebar()
 *
 * @since 2.5.2
 * @param string  $name        Sidebar display name
 * @param string  $id          Sidebar's unique ID
 * @param string  $description Sidebar description
 * @param boolean $editable    True if this sidebar can be overriden by Custom Sidebars (Default: false)
 */
function sb_register_sidebar( $name = null, $id = null, $description = null, $editable = 0 ) {
	global $startbox;
	$startbox->sidebars->register_sidebar( array( 'name' => $name, 'id' => $id, 'description' => $description, 'editable' => $editable ) );
}

/**
 * Wrapper Function for SB_Sidebars::do_sidebar()
 *
 * @since 2.5.0
 * @param string $location the unique ID to give the container for this sidebar
 * @param string $sidebar the ID of the sidebar to attach to this location by default
 * @param string $classes additional custom classes to add to the container for this sidebar
 */
function sb_do_sidebar( $location = null, $sidebar = null, $classes = null ) {
	global $startbox;
	$startbox->sidebars->do_sidebar( $location, $sidebar, $classes );
}
