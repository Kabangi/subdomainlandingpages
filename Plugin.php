<?php 
namespace Julius\Multidomain;

use System\Classes\PluginBase;
use Julius\Multidomain\Models\Setting;
use Cms\Classes\Theme;
use Cms\Classes\Router;
use Cms\Classes\Page;
use BackendAuth;
use Backend;
use Config;
use Event;
use Cache;
use Request;
use App;
use Lang;
use Log;
use Flash;
use RainLab\Pages\Classes\Controller;
/**
 * Multidomain Plugin Information File
 * Plugin icon is used with Creative Commons (CC BY 4.0) Licence
 * Icon author: http://pixelkit.com/
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'julius.multidomain::lang.details.title',
            'description' => 'julius.multidomain::lang.details.description',
            'author' => 'Julius',
            'icon' => 'icon-cubes'
        ];
    }

    public function registerSettings()
    {
        return [
            'Multidomain' => [
                'label' => 'julius.multidomain::lang.details.title',
                'description' => 'julius.multidomain::lang.details.description',
                'category' => 'system::lang.system.categories.cms',
                'icon' => 'icon-cubes',
                'url' => Backend::url('julius/multidomain/settings'),
                'order' => 500,
                'keywords' => 'Multidomain home pages'
            ]
        ];
    }

    public function boot()
    {
        $backendUri = Config::get('cms.backendUri');
        $requestUrl = Request::url();
        $currentHostUrl = Request::getHost();

        /*
         * Get domain to theme bindings from cache, if it's not there, load them from database,
         * save to cache and use for theme selection.
         */
        $binds = Cache::rememberForever('julius_multidomain_settings', function () {
            try {
                $cacheableRecords = Setting::generateCacheableRecords();
            } catch (\Illuminate\Database\QueryException $e) {
                if (BackendAuth::check())
                    Flash::error(trans('julius.multidomain:lang.flash.db-error'));
                return null;
            }
            return $cacheableRecords;
        });

        /*
         * Oooops something went wrong, abort.
         */
        if ($binds === null) return;

        /*
         * If current request is in backend scope, do not continue
         */

        if (preg_match('/\\' . $backendUri . '/', $requestUrl)) return;

        /*
         * Check if this request is in backend scope and is using domain,
         * that is protected from using backend
        */
        foreach ($binds as $domain => $bind) {
            if (preg_match('/\\' . $backendUri . '/', $requestUrl) && preg_match('/' . $currentHostUrl . '/i', $domain) && $bind['is_protected']) {
                return App::abort(401, 'Unauthorized.');
            };
        }

        /*
         * Overide the rainlab pages with custom domain ones
         *
         */
        $menuItemsToOveride = [];
        foreach ($binds as $domain => $value) {
            if(isset($value['page_url'])){
                $menuItemsToOveride[] = [
                'domain' => $domain,
                'url' => $value['page_url'],
                'type' => $value['type']
                ]; 
            }       
        }

        Event::listen('cms.router.beforeRoute',function() use ($menuItemsToOveride,$currentHostUrl,$requestUrl){
           
            $url = null;
            $type = null;
            $domain = null;
            foreach ($menuItemsToOveride as $key => $value) {
                $configuredDomain = $value['domain'];
                $currentHostUrl = Request::url();
                if($currentHostUrl === $configuredDomain){
                    $url = $value['url'];
                    $domain = $value['domain'];
                    $type = $value['type'];
                }
            }
            if(!is_null($url)){
                if($type === 'pages_plugin'){
                    return Controller::instance()->initCmsPage($url);
                }elseif($type === 'cms_pages'){
                    $theme = Theme::getEditTheme();
                    $router = new Router(Theme::getEditTheme());
                    for ($pass = 1; $pass <= 2; $pass++) {
                        $fileName = null;
                        $urlList = [];
                        /*
                         * Find the page by URL
                         */
                        if (!$fileName) {
                            if ($router->match($url)) {
                                $fileName = $router->matchedRoute();
                            }
                        }

                        /*
                         * Return the page
                         */
                        if ($fileName) {
                            if (($page = Page::loadCached($theme, $fileName)) === null) {
                                /*
                                 * If the page was not found on the disk, clear the URL cache
                                 * and repeat the routing process.
                                 */
                                if ($pass == 1) {
                                    continue;
                                }

                                return null;
                            }

                            return $page;
                        }

                        return null;
                    }
                }                    
            }
        },1000);
      
    }

}
