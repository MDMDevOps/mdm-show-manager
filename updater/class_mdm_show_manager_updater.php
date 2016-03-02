<?php
/**
 * Update plugin from private github repo
 *
 * @since   1.0.0
 * @link    http://midwestfamilymarketing.com
 * @package mdm_show_manager
 * @license GPL-2.0+
 *
 */

// Prevent loading this file directly and/or if the class is already defined
if ( ! defined( 'ABSPATH' ) || class_exists( 'WPGitHubUpdater' ) || class_exists( 'WP_GitHub_Updater' ) ) {
    return;
}

class Mdm_Show_Manager_Updater {
    private $file;
    private $slug;
    private $plugin;
    private $basename;
    private $active;
    private $username;
    private $repository;
    private $authorize_token;
    private $github_response;
    private $settings_key;

    /**
     * Initialize updater and set options
     * @since 1.0.0
     * @param (string)$file -> path to base plugin file
     */
    public function __construct( $plugin_name, $version, $settings_key, $plugin_base, $plugin_slug ) {
        // Set options
        $this->plugin_name  = $plugin_name;
        $this->version      = $version;
        $this->file         = $plugin_base;
        $this->slug         = $plugin_slug;
        $this->username     = 'MDMDevOps';
        $this->repository   = 'mdm-show-manager';
        $this->settings_key = $settings_key;
        $this->authorize();
        // return update object
        return $this;
    }
    public function authorize() {
        $auth = ( is_multisite() ) ? get_site_option( $this->settings_key ) : get_option( $this->settings_key );
        $this->authorize_token = ( isset( $auth['api_key'] ) ) ? esc_attr( $auth['api_key'] ) : null;
    }
    public function set_plugin_properties() {
        $this->plugin   = get_plugin_data( $this->file );
        $this->basename = plugin_basename( $this->file );
        $this->active   = is_plugin_active( $this->basename );
    }
    /**
     * Get repository data From Gethub
     * @since 1.0.0
     */
    private function get_repository_data() {
        // 1: Check if response is already set (if is null)
        if( !is_null( $this->github_response ) ) {
            return;
        }
        // 2: Verify access token is set
        if( is_null( $this->authorize_token ) ) {
            return;
        }
            // 1.1: Build request URI
            $request_uri = sprintf( 'https://api.github.com/repos/%s/%s/releases', $this->username, $this->repository );
            // 1.2: Check if access token is set
            if( $this->authorize_token ) {
                // 1.2.1: Append access token to URI
                $request_uri = add_query_arg( 'access_token', $this->authorize_token, $request_uri ); // Append it
            }
            // 1.3: Get json response from github and parse it
            $response = json_decode( wp_remote_retrieve_body( wp_remote_get( $request_uri ) ), true );

            // 1.4: If it is an array, get the first item
            if( is_array( $response ) ) {
                $response = current( $response );
            }
            // 1.5: Check if access token is set
            if( $this->authorize_token ) {
                // 1.5.1: Append access token to zipball URL
                $response['zipball_url'] = add_query_arg( 'access_token', $this->authorize_token, $response['zipball_url'] );
            }
            // 1.6: Set github response
            $this->github_response = $response;

    }

    public function modify_transient( $transient ) {
         // Check if transient has a checked property
        if( property_exists( $transient, 'checked') ) {
             // Did Wordpress check for updates?
            if( $checked = $transient->checked ) {
                $this->get_repository_data(); // Get the repo info

                $out_of_date = version_compare( $this->github_response['tag_name'], $checked[ $this->basename ], 'gt' ); // Check if we're out of date

                if( $out_of_date ) {

                    $new_files = $this->github_response['zipball_url']; // Get the ZIP

                    $slug = current( explode('/', $this->basename ) ); // Create valid slug

                    $plugin = array( // setup our plugin info
                        'url' => $this->plugin["PluginURI"],
                        'slug' => $slug,
                        'package' => $new_files,
                        'new_version' => $this->github_response['tag_name']
                    );

                    $transient->response[$this->basename] = (object) $plugin; // Return it in response
                }
            }
        }
        return $transient; // Return filtered transient
    }
    public function plugin_popup( $result, $action, $args ) {
        if( !empty( $args->slug ) ) { // If there is a slug
            if( $args->slug == $this->slug ) { // And it's our slug
                $this->get_repository_data(); // Get our repo info
                // Set it to an array
                $plugin = array(
                    'name'              => $this->plugin["Name"],
                    'slug'              => $this->basename,
                    'version'           => $this->github_response['tag_name'],
                    'author'            => $this->plugin["AuthorName"],
                    'author_profile'    => $this->plugin["AuthorURI"],
                    'last_updated'      => $this->github_response['published_at'],
                    'homepage'          => $this->plugin["PluginURI"],
                    'short_description' => $this->plugin["Description"],
                    'sections'          => array(
                        'Description'   => $this->plugin["Description"],
                        'Updates'       => $this->github_response['body'],
                    ),
                    'download_link'     => $this->github_response['zipball_url'],
                    'action'            => $action
                );
                return (object) $plugin; // Return the data
            }
        }
        return $result; // Otherwise return default
    }
    public function after_install( $response, $hook_extra, $result ) {
        // 1: Get the global filesystem object
        global $wp_filesystem;
        // 2: Get the plugin directory path
        $install_directory = plugin_dir_path( $this->file );
        // 3: Move files to the plugin
        $wp_filesystem->move( $result['destination'], $install_directory );
        // 4. Set the destination for the rest of the stack
        $result['destination'] = $install_directory;
        // 5. If it was active, re-activate
        if ( $this->active ) {
            activate_plugin( $this->basename );
        }
        return $result;
    }
}