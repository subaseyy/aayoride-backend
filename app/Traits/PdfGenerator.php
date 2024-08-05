<?php

namespace App\Traits;

trait PdfGenerator
{
    public static function generatePdf($view, $filePrefix, $filePostfix): string
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'Inter', 'mode' => 'utf-8', 'format' => [190, 250], 'autoLangToFont' => true]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
//        dd($mpdf_view);
        $mpdf->WriteHTML($mpdf_view);
        $mpdf->Output($filePrefix . $filePostfix . '.pdf', 'D');
    }
}
