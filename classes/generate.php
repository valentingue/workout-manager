<?php 

namespace Classes;

use mikehaertl\pdftk\Pdf;

require WORKOUT_MANAGER_DIR.'/includes/vendor/autoload.php';

class GeneratePDF{

    public function generate($data){
        
        $filename = 'contract_'. rand(0, 120000);

        $pdf = new Pdf(WORKOUT_MANAGER_DIR.'/tmp/contrat_vierge.pdf', array("command" => '/opt/pdflabs/pdftk/bin/pdftk'));
        
        $pdf->fillForm($data);
        
        $pdf->flatten();

        $pdf->saveAs(WORKOUT_MANAGER_DIR.'/tmp/rendered/filled_'.$filename.'.pdf');

        print_r($pdf);

        return $filename;
    }
}