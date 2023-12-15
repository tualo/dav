<?php
namespace Tualo\Office\DAV\Checks;

use Tualo\Office\Basic\Middleware\Session;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\TualoApplication as App;


class Tables  extends PostCheck {
    
    public static function test(array $config){
        $clientdb = App::get('clientDB');
        if (is_null($clientdb)) return;
        $tables = [
            'adressen'=>[],
            'booking_circle'=>[],
            'buchungskonten'=>[],
            'ds_fill'=>[],
            'einspeiserkennzeichen'=>[],
            'geschaeftsstatus'=>[],
            'kundenbetreuer'=>[],
            'preiskategorien'=>[],
            'rechnungsdruck'=>[],
            'rechnungslegung'=>[],
            'steuergruppen'=>[],
            'vertragsarten'=>[],
            'zahlungsarten'=>[],
            'zahlungsschluessel'=>[]
        ];
        self::tableCheck('ds',$tables,
            "please run the following command: `./tm install-sql-erp --client ".$clientdb->dbname."`",
            "please run the following command: `./tm install-sql-erp --client ".$clientdb->dbname."`"

        );
    }
}