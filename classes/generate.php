<?php 

namespace Classes;


use mikehaertl\pdftk\Pdf;

require WORKOUT_MANAGER_DIR.'/includes/vendor/autoload.php';

class GeneratePDF{

    public function generate($data){
        
        $filename = 'contract_'. rand(0, 120000).'.pdf';

        $pdf = new Pdf(WORKOUT_MANAGER_DIR.'/tmp/contrat_vierge_copie.pdf');
        
        $pdf->fillForm($data)
            ->flatten()
            ->saveAs(WORKOUT_MANAGER_DIR.'/tmp/rendered/'.$filename);
        
        return $filename;
    }
}