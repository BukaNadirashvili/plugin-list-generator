<?php
namespace PluginListGenerator;

// The class is used to set various options and download files
class Form extends Helper
{

    /**
     * Plugin options
     *
     * @var array
     */
    private $options;

    /**
	 * 
	 * Column descriptions
     * 
	 * @var array
	 */
    private $columnsDescriptions;

    function __construct()
    {
        add_action('admin_menu', [$this, 'PluginListGeneratorSettingsPage']);
        add_action( 'admin_enqueue_scripts', [$this, 'PluginListGeneratorScripts'] );
        add_action( 'admin_init', [$this, 'PluginListGeneratorSettingsInit'] );
    }

    // Callback function for enqueue plugin-related CSS and JS
    function PluginListGeneratorScripts()
    {
        wp_enqueue_style('admin-form-style', PLUGIN_LIST_GENERATOR_DIR_URL . 'assets/css/style.css');
        wp_enqueue_script('admin-form-js', PLUGIN_LIST_GENERATOR_DIR_URL . '/assets/js/common.js', array('jquery'), '1.0', true);
    }

    // Callback function for adding the settings submenu page
    function PluginListGeneratorSettingsPage() {

        add_submenu_page('options-general.php', 
                        'Plugin List Generator', 
                        'Plugin List Generator', 
                        'manage_options', 
                        'plugin-list-generator', 
                        [$this, 'CreateAdminMenu'] 
        );
    }

    // Callback function for the output content of the page
    function CreateAdminMenu()
    {

        $this->options = get_option( 'plugin-list-generator' );
    
        ?>
        <h1>
          <?php esc_html_e( 'Plugin List Generator', 'plugin-list-generator' ); ?> 
        </h1>
        <form method="POST" action="options.php">
          <?php
            settings_fields( 'option' );
          ?>
          <?php
            do_settings_sections( 'plugin-list-generator' );
          ?>
          <?php 
            submit_button(__('Generate File And Save Options', 'plugin-list-generator'));
          ?>
        </form>
        <?php
    }

    // This function is used for different purposes: to register plugin settings,
    // to add a settings section and to add settings fields.
    function PluginListGeneratorSettingsInit()
    {

        $this->columnsDescriptions = [
          'Title'         => __('Title', 'plugin-list-generator'),
          'PluginURI'     => __('Plugin Url', 'plugin-list-generator'),
          'Version'       => __('Version', 'plugin-list-generator'),
          'Description'   => __('Description', 'plugin-list-generator'),
          'Author'        => __('Author', 'plugin-list-generator'),
          'AuthorURI'     => __('Author Url', 'plugin-list-generator'),
          'RequiresWP'    => __('Requires WP', 'plugin-list-generator'),
          'RequiresPHP'   => __('Requires PHP', 'plugin-list-generator'),
        ];

        register_setting( 'option', 'plugin-list-generator', function($input){

            $file_path = PLUGIN_LIST_GENERATOR_DIR_PATH . '/data/options.json';

            file_put_contents($file_path, json_encode($input));

            return  (new GeneratePdf($input, $this->columnsDescriptions))->generatePdf();

        });
    
        add_settings_section(
          'plugin-list-generator',
          __( 'Custom settings', 'plugin-list-generator' ),
          [$this, 'settings_section_text'],
          'plugin-list-generator'
        );

        add_settings_field(
            'include-columns',
            esc_html('Inlcude Columns', 'plugin-list-generator'),
            [$this, 'checkbox'],
            'plugin-list-generator',
            'plugin-list-generator',
            [
                'items' => [
                    [
                        'name' => 'include-title',
                        'value' => 'Title',
                        'description' => $this->columnsDescriptions['Title'],
                    ],
                    [
                        'name' => 'include-plugin-url',
                        'value' => 'PluginURI',
                        'description' => $this->columnsDescriptions['PluginURI'],
                    ],
                    [
                        'name' => 'include-version',
                        'value' => 'Version',
                        'description' => $this->columnsDescriptions['Version'],
                    ],
                    [
                        'name' => 'include-description',
                        'value' => 'Description',
                        'description' => $this->columnsDescriptions['Description'],
                    ],
                    [
                        'name' => 'include-author',
                        'value' => 'Author',
                        'description' => $this->columnsDescriptions['Author'],
                    ],
                    [
                        'name' => 'include-author-url',
                        'value' => 'AuthorURI',
                        'description' => $this->columnsDescriptions['AuthorURI'],
                    ],
                    [
                        'name' => 'include-requires-wp',
                        'value' => 'RequiresWP',
                        'description' => $this->columnsDescriptions['RequiresWP'],
                    ],
                    [
                        'name' => 'include-requires-php',
                        'value' => 'RequiresPHP',
                        'description' => $this->columnsDescriptions['RequiresPHP'],
                    ],
                ],
                'option'  => 'plugin-list-generator'
            ]
        );

        add_settings_field(
            'status',
            esc_html('Plugins Status', 'plugin-list-generator'),
            [$this, 'checkbox'],
            'plugin-list-generator',
            'plugin-list-generator',
            [
                'items' => [
                    [
                        'name' => 'status-all',
                        'value' => 'all',
                        'description' => __('All', 'plugin-list-generator'),
                    ],
                    [
                        'name' => 'status-active',
                        'value' => 'active',
                        'description' => __('Active', 'plugin-list-generator'),
                    ],
                    [
                        'name' => 'status-inactive',
                        'value' => 'inactive',
                        'description' => __('Inactive', 'plugin-list-generator'),
                    ],
                    [
                        'name' => 'status-update-available',
                        'value' => 'update-available',
                        'description' => __('Update Available', 'plugin-list-generator'),
                    ],
                ],
                'option'  => 'plugin-list-generator'
            ]
        );

        add_settings_field(
            'mu-plugins-included',
            esc_html('MU Plugins Included?', 'plugin-list-generator'),
            [$this, 'checkbox'],
            'plugin-list-generator',
            'plugin-list-generator',
            [
                'items' => [
                    [
                        'name' => 'mu-plugins-included',
                        'value' => 'mu-plugins-included',
                        'description' => __('Yes', 'plugin-list-generator'),
                    ],
                ],
                'option'  => 'plugin-list-generator',
            ]
        );

        add_settings_field(
            'add-this-plugin',
            esc_html('Include This Plugin?', 'plugin-list-generator'),
            [$this, 'checkbox'],
            'plugin-list-generator',
            'plugin-list-generator',
            [
                'items' => [
                    [
                        'name' => 'add-this-plugin',
                        'value' => 'add-this-plugin',
                        'description' => __('Yes', 'plugin-list-generator'),
                    ],
                ],
                'option'  => 'plugin-list-generator',
            ]
        );
    }
  

    function settings_section_text()
    {
        echo '<p>' . __('Settings For File Generation', 'plugin-list-generator') . '</p>';
    }

}