<?php

namespace Tualo\Office\DAV\Routes;

use Sabre\DAV;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class Test2 extends \Tualo\Office\Basic\RouteWrapper
{
    public static function register()
    {
        BasicRoute::add('/word', function () {
            echo "<a href='ms-word:ofv|u|http://localhost/server/~/7328914b-623a-45f7-b0b0-9449eb89533b/dav/server.php/Test123.docx'>Öffnen (ofv)</a>";
            echo "<a href='ms-word:ofe|u|http://localhost/server/~/7328914b-623a-45f7-b0b0-9449eb89533b/dav/server.php/Test123.docx'>Öffnen (ofe)</a>";
            BasicRoute::$finished = true;
            exit();
        }, [
            'get',
            'post',
            'OPTIONS',
            'PROPFIND'
        ], false);

        BasicRoute::add('/dav(?P<file>(.)+)', function () {
            $db = App::get('session')->getDB();

            App::logger('DAV')->debug("DAV server:" . print_r($_SERVER, true));
            App::logger('DAV')->debug("DAV gestartet");
            App::logger('DAV')->debug("DAV REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);


            // Let's start by creating the object tree. This tree needs to
            // implement Sabre\DAV\INode

            // Now we're creating a whole bunch of objects

            // $rootDirectory = new DAV\FS\Directory(App::get('basePath') . '/public');
            $rootDirectory = new \Tualo\Office\BinaryDocx\VFSDirectory('public');

            // The object tree needs in turn to be passed to the server class
            $server = new DAV\Server($rootDirectory);

            // If your server is not on your webroot, make sure the following line has the
            // correct information
            $server->setBaseUri(App::get('requestPath') . '/dav/server.php');

            // The lock manager is reponsible for making sure users don't overwrite
            // each others changes.
            $lockBackend = new DAV\Locks\Backend\File('data/locks');
            $lockPlugin = new DAV\Locks\Plugin($lockBackend);
            $server->addPlugin(new \Sabre\DAV\Mount\Plugin());
            $server->addPlugin($lockPlugin);

            // This ensures that we get a pretty index in the browser, but it is
            // optional.
            $server->addPlugin(new DAV\Browser\Plugin());

            // All we need to do now, is to fire up the server
            $server->start();

            BasicRoute::$finished = true;
            exit();
        }, [
            'get',
            'post',
            'OPTIONS',
            'PROPFIND',
            'HEAD',
            'MKCOL',
            'PUT',
            'PATCH',
            'DELETE',
            'MOVE',
            'COPY',
            'LOCK',
            'UNLOCK',
            'PROPPATCH',
            'REPORT',
            'CHECKOUT',
            'CHECKIN',
            'UNCHECKOUT',
            'VERSION-CONTROL',
            'LABEL',
            'UPDATE',
            'MERGE',
            'BASELINE-CONTROL',
            'MKWORKSPACE'
            /*
            'PUT',
            'PATCH',
            'DELETE',
            'MKCOL',
            'MOVE',
            'COPY',
            'LOCK',
            'UNLOCK',
            'PROPPATCH',
            'REPORT',
            'CHECKOUT',
            'CHECKIN',
            'UNCHECKOUT',
            'VERSION-CONTROL',
            'LABEL',
            'UPDATE',
            'MERGE',
            'BASELINE-CONTROL',
            'MKWORKSPACE'
            */
        ], false);
    }
}
