<?php
namespace Tualo\Office\DAV\Commands;
use Garden\Cli\Cli;
use Garden\Cli\Args;
use phpseclib3\Math\BigInteger\Engines\PHP;
use Tualo\Office\Basic\ICommandline;
use Tualo\Office\ExtJSCompiler\Helper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\PostCheck;

class Install implements ICommandline{

    public static function getCommandName():string { return 'install-sql-dav';}

    public static function setup(Cli $cli){
        $cli->command(self::getCommandName())
            ->description('installs needed sql for dav module')
            ->opt('client', 'only use this client', true, 'string');
            
    }

   
    public static function setupClients(string $msg,string $clientName,string $file,callable $callback){
        $_SERVER['REQUEST_URI']='';
        $_SERVER['REQUEST_METHOD']='none';
        App::run();

        $session = App::get('session');
        $sessiondb = $session->db;
        $dbs = $sessiondb->direct('select username dbuser, password dbpass, id dbname, host dbhost, port dbport from macc_clients ');
        foreach($dbs as $db){
            if (($clientName!='') && ($clientName!=$db['dbname'])){ 
                continue;
            }else{
                App::set('clientDB',$session->newDBByRow($db));
                PostCheck::formatPrint(['blue'],$msg.'('.$db['dbname'].'):  ');
                $callback($file);
                PostCheck::formatPrintLn(['green'],"\t".' done');

            }
        }
    }

    public static function run(Args $args){

        $files = [
            
            'install/booking_circle'    => 'setup booking_circle',
            'install/booking_circle.ds'    => 'setup booking_circle.ds',

            'install/einspeiserkennzeichen'    => 'setup einspeiserkennzeichen',
            'install/einspeiserkennzeichen.ds'    => 'setup einspeiserkennzeichen.ds',
            
            'install/geschaeftsstatus'    => 'setup geschaeftsstatus',
            'install/geschaeftsstatus.ds'    => 'setup geschaeftsstatus.ds',

            'install/kundenbetreuer'    => 'setup kundenbetreuer',
            'install/kundenbetreuer.ds'    => 'setup kundenbetreuer.ds',

            'install/preiskategorien'    => 'setup preiskategorien',
            'install/preiskategorien.ds'    => 'setup preiskategorien.ds',

            'install/rechnungsdruck'    => 'setup rechnungsdruck',
            'install/rechnungsdruck.ds'    => 'setup rechnungsdruck.ds',

            'install/rechnungslegung'    => 'setup rechnungslegung',
            'install/rechnungslegung.ds'    => 'setup rechnungslegung.ds',

            'install/steuergruppen'    => 'setup steuergruppen',
            'install/steuergruppen.ds'    => 'setup steuergruppen.ds',

            'install/vertragsarten'    => 'setup vertragsarten',
            'install/vertragsarten.ds'    => 'setup vertragsarten.ds',

            'install/zahlungsarten'    => 'setup zahlungsarten',
            'install/zahlungsarten.ds'    => 'setup zahlungsarten.ds',

            'install/zahlungsschluessel'    => 'setup zahlungsschluessel',
            'install/zahlungsschluessel.ds'    => 'setup zahlungsschluessel.ds',

            'install/warenhauptgruppen'    => 'setup warenhauptgruppen',
            'install/warenhauptgruppen.ds'    => 'setup warenhauptgruppen.ds',

            'install/warengruppen'    => 'setup warengruppen',
            'install/warengruppen.ds'    => 'setup warengruppen.ds',

            'install/lager'    => 'setup lager',
            'install/lager.ds'    => 'setup lager.ds',

            'install/preisorientierung'    => 'setup preisorientierung',
            'install/preisorientierung.ds'    => 'setup preisorientierung.ds',

            'install/buchungskonten'    => 'setup buchungskonten',
            'install/buchungskonten.ds'    => 'setup buchungskonten.ds',

            'install/adressen'    => 'setup adressen',
            'install/adressen.ds'    => 'setup adressen.ds',

            'install/proc.recalculateHeader'    => 'setup recalculateHeader',
            

            // immer zum schluss
            'install/fill_ds'    => 'refreshing ds data',

        ];
        

        foreach($files as $file=>$msg){
            $installSQL = function(string $file){

                $filename = dirname(__DIR__).'/sql/'.$file.'.sql';
                $sql = file_get_contents($filename);
                $sql = preg_replace('!/\*.*?\*/!s', '', $sql);
                $sql = preg_replace('#^\s*\-\-.+$#m', '', $sql);

                $sinlgeStatements = App::get('clientDB')->explode_by_delimiter($sql);
                foreach($sinlgeStatements as $commandIndex => $statement){
                    try{
                        App::get('clientDB')->execute($statement);
                        App::get('clientDB')->moreResults();
                    }catch(\Exception $e){
                        echo PHP_EOL;
                        PostCheck::formatPrintLn(['red'], $e->getMessage().': commandIndex => '.$commandIndex);
                    }
                }
            };
            $clientName = $args->getOpt('client');
            if( is_null($clientName) ) $clientName = '';
            self::setupClients($msg,$clientName,$file,$installSQL);
        }


    }
}
