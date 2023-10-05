<?php

namespace PluginListGenerator;

// Helper class for additional functionlality
class Helper
{

    /**
     * Get checkbox output
     *
     * @param array $args different arguments for output
     */
    function checkbox($args)
    {
        foreach ($args['items'] as $item) {

            $option  = $args['option'];
            $name    = $args['option'] . '[' . $item['name'] . ']' . '';

            if (file_exists(PLUGIN_LIST_GENERATOR_DIR_PATH . '/data/options.json'))
                $checked = isset(json_decode(file_get_contents(PLUGIN_LIST_GENERATOR_DIR_PATH . '/data/options.json'), true)[$item['name']]) ? 'checked' : '';

            echo '
              <div class="form-check">
              <input ' . $checked  . ' id=' . esc_attr( $item['name']) . ' name="' . $name . '" value="' . $item['value'] . '" type="checkbox" />' . 
              '<label class="checkbox-label" for="' . $item['name']  . '">' . $item['description'] . '</label>' . 
              '</div>';
        }

    }

    /**
     * Get radio buttons output
     *
     * @param array $args different arguments for output
     */
    function radio($args)
    {

  	    $option = get_option($args['option']);
  	    $items  = $args['items'];

        foreach ($items as $item) {

            $name = $args['option'] . '[' . $item['name'] . ']' . '';

            $checked = (isset($option['format']) && $option['format'] == $item['value']) ? ' checked="checked" ' : '';
            echo '<label><input '.$checked.' value="' . $item['value'] .'" name="' . $name . '" type="radio" />' . $item['description'] .'</label>';

        }

    }

}