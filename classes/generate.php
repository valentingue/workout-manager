<?php 

namespace Classes;

use \Dompdf\Dompdf;
use \Dompdf\Options;

require WORKOUT_MANAGER_DIR.'/includes/vendor/autoload.php';

class GeneratePDF{

    public function generate($data){
        $tmp_path = WORKOUT_MANAGER_DIR.'/tmp/rendered/';
        if(!is_dir($tmp_path)) mkdir($tmp_path);
        
        $filename = 'contract_'. rand(0, 120000);
        
        /**----------------------
         *    FPDM
         *------------------------**/
        $pdf = new \FPDM(WORKOUT_MANAGER_DIR.'/tmp/contrat_vierge.pdf');

        $pdf->useCheckboxParser = true;
        $pdf->Load($data, true);
        $pdf->Merge();
        $pdf->Output('F', WORKOUT_MANAGER_DIR.'/tmp/rendered/'.$filename.'.pdf');


        /**----------------------
         *    PDFTK
         *------------------------**/
        // $pdf = new Pdf(WORKOUT_MANAGER_DIR.'/tmp/contrat_vierge.pdf'); /* , array("command" => '/opt/pdflabs/pdftk/bin/pdftk') */
        // var_dump($data);

        // $result = $pdf/* ->fillForm($data) */
        //         ->needAppearances()
        //         ->saveAs(WORKOUT_MANAGER_DIR.'/tmp/rendered/filled_'.$filename.'.pdf');

        // if ($result === false) {
        //     $error = $pdf->getError();
        // }

        /**----------------------
         *    DOMPDF
         *------------------------**/
        /* $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        ob_start();

        \Timber::render(WORKOUT_MANAGER_DIR."/views/pages/contract.twig", [
            'datas' => $data,
        ]);
    
        $html = ob_get_contents();
        ob_end_clean();

        echo $html;

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'cover');
        $dompdf->render(); 

        $filename = 'contrat_'. $data['client_firstname'] . '_' .$data['client_firstname'] . '.pdf';

        $filePath = WORKOUT_MANAGER_DIR.'/tmp/rendered/'.$filename;
        if(file_exists($filePath)) unlink($filePath);

        // Output the generated PDF to Browser
        $output = $dompdf->output();

        file_put_contents($filePath, $output);

        echo $filePath;*/

        return str_replace(WORKOUT_MANAGER_DIR.'/', WORKOUT_MANAGER_URL, $tmp_path).$filename.'.pdf';
    }
}