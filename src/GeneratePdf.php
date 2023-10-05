<?php

namespace PluginListGenerator;

use Dompdf\Dompdf;

class GeneratePdf extends BaseGenerator
{

    private function getPluginTh($plugin_data)
    {

        $html = '';

        foreach ($this->columnsDescriptions as $index => $desc) {

            if(!$plugin_data[$index])
                continue;

            if(in_array($index, $this->pluginInformation)){
              $html .= '<tr>';
              $html .= '<th>' . $desc . '</th>';
              $html .= '<td>'. $plugin_data[$index] . '<td>';
              $html .= '</tr>';
            }
        }

        return $html;

    }

    private function generatePdfHtml($data)
    {

        $html = '';

        foreach ($data as $index => $plugin) {

            $html .= '<tr class="plugins-list-tr">';
            $html .= '<th>' . $plugin['Name']  . '</th>' . '';
            $html .= '<td>';
            $html .= '<table>';

            $html .= $this->getPluginTh($plugin);

            $html .= '</table>';
            $html .= '</td>';
            $html .=  '</tr>';

        }

        return $html;

    }

    private function getStatusHeader($status)
    {

        return '
        <tr>
          <th class="status-header" colspan="2">
            <h1>' . $status . '</h1>
          </th>
        </tr>
        ';

    }

    private function getPdfStyles()
    {
        return
          '
          <style>
            .plugin-list-generator-table{
              border-collapse: collapse;
              font-family: sans-serif;
            }
            .plugins-list-tr {
              border: 2px solid #2271b1;
            }
            .status-header{
              text-align: center;
            }
            .status-header h1 {
              margin-top: 20px;
            }
          </style>
          ';
    }

    public function generatePdf()
    {

        if (empty($this->statuses)) {
            add_settings_error('status', 'status-not-selected', __('Status Not Selected', 'plugin-list-generator'), 'error');
            return;
        }

        if (empty($this->pluginInformation)) {
            add_settings_error('include-columns', 'column-not-selected', __('Column Not Selected', 'plugin-list-generator'), 'error');
            return;
        }

        $html = '';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html"; charset="utf-8"/>';
        $html .= $this->getPdfStyles();
        $html .= '</head>';
        $html .= '</body>';

        $html .= '<table class="plugin-list-generator-table">';

        if (in_array('all', $this->statuses)) {
          $html .= $this->getStatusHeader(__('All Plugins', 'plugin-list-generator'));
          $html .= $this->generatePdfHtml($this->plugins);
        }

        if (in_array('active', $this->statuses)) {
          $html .= $this->getStatusHeader(__('Active Plugins', 'plugin-list-generator'));
          $html .= $this->generatePdfHtml($this->activePlugins);
        }

        if (in_array('inactive', $this->statuses)) {
          $html .= $this->getStatusHeader(__('Inactive Plugins', 'plugin-list-generator'));
          $html .= $this->generatePdfHtml($this->inactivePlugins);
        }

        if (in_array('update-available', $this->statuses)) {
          $html .= $this->getStatusHeader(__('Update Available Plugins', 'plugin-list-generator'));
          $html .= $this->generatePdfHtml($this->updatePlugins);
        }

        if (isset($this->input['mu-plugins-included'])) {
          $html .= $this->getStatusHeader(__('MU Plugins', 'plugin-list-generator'));
          $html .= $this->generatePdfHtml($this->muPlugins);
        }

        $html .= '</body>';
        $html .= '</html>';

        $html = apply_filters( 'plugin_list_generator_html', $html, $this->statuses, $this->active_plugins, $this->inactive_plugins, $this->update_plugins, $this->mu_plugins );

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream();

    }

}