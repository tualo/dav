<?php
namespace Tualo\Office\DAV\Routes;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Sabre\DAV\Auth\Backend\PDO AS AuthPDO;
use Sabre\DAV\CalDAV\Backend\PDO AS CalDAVPDO;
use Sabre\DAVACL\PrincipalBackend\PDO AS PrincipalPDO;
use Sabre\CalDAV\Principal\Collection AS PrincipalCollection;
use Sabre\DAV\Server;
use Sabre\DAV\FS\Directory;

use Sabre\CalDAV\CalendarRoot;
use Sabre\DAV\Auth\Plugin as AuthPlugin;
use Sabre\DAVACL\Plugin as ACLPlugin;
use Sabre\CalDAV\Plugin as CalDAVPlugin;
use Sabre\CalDAV\Subscriptions\Plugin as SubscriptionsPlugin;
use Sabre\CalDAV\Schedule\Plugin as SchedulePlugin;
use Sabre\DAV\Sync\Plugin as SyncPlugin;
use Sabre\DAV\Sharing\Plugin as SharingPlugin;
use Sabre\CalDAV\SharingPlugin as CalDAVSharingPlugin;
use Sabre\DAV\Browser\Plugin as BrowserPlugin;
use Ramsey\Uuid\Uuid;
use Tualo\Office\DS\DSTable;

class Test implements IRoute{
    public static function setupServer(){

        $pdo = new \PDO("mysql:host=localhost;dbname=dav", 'thomashoffmann', '');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

        // Backends
        $authBackend = new \Tualo\Office\DAV\Classes\Auth\System();
        $calendarBackend = new \Sabre\CalDAV\Backend\PDO($pdo);
        $principalBackend = new \Sabre\DAVACL\PrincipalBackend\PDO($pdo);
        $rootDirectory = new Directory('public');
        // Directory structure
        $tree = [
            $rootDirectory,
            new \Tualo\Office\DAV\Classes\FS\DSDirectory('tualocms_bilder'),
            new PrincipalCollection($principalBackend),
            new CalendarRoot($principalBackend, $calendarBackend),
        ];


        $server = new Server($tree);
        $server->setBaseUri('/server/tualocms/page/dav/');
        /* Server Plugins */
        $authPlugin = new AuthPlugin($authBackend);
        $server->addPlugin($authPlugin);

        $aclPlugin = new ACLPlugin();
        $server->addPlugin($aclPlugin);

        /* CalDAV support */
        $caldavPlugin = new CalDAVPlugin();
        $server->addPlugin($caldavPlugin);

        /* Calendar subscription support */
        $server->addPlugin(
        new SubscriptionsPlugin()
        );

        /* Calendar scheduling support */
        $server->addPlugin(
        new SchedulePlugin()
        );

        /* WebDAV-Sync plugin */
        $server->addPlugin(new SyncPlugin());

        /* CalDAV Sharing support */
        $server->addPlugin(new SharingPlugin());
        $server->addPlugin(new CalDAVSharingPlugin());

        // Support for html frontend
        $browser = new BrowserPlugin();
        $server->addPlugin($browser);

        // And off we go!
        $server->start();
    }

    public static function register(){
       
        BasicRoute::add('/tualocms/page/dav(?P<file>(.)+)',function(){
            $db = App::get('session')->getDB();
            self::setupServer();
            
            BasicRoute::$finished=true;
            exit();         
        },['get','post','OPTIONS','PROPFIND'],false);

    }
}