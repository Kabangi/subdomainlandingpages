<?php 
namespace Julius\Multidomain\Models;

use Model;
use Config;
use Cms\Classes\Theme;
use Cms\Classes\Page;
use Cache;
use Log;
use Request;
use Cms\Classes\CmsObject;
use Cms\Classes\Router;
use RainLab\Pages\Classes\Page as StaticPageClass;

/**
 * Setting Model
 */
class Setting extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'domain' => 'required|url',
        'page_url' => 'required',
    ];
    /**
     * @var string The database table used by the model.
     */
    public $table = 'julius_multidomain_settings';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['domain', 'page_url','is_protected'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /*
     * Get all currently available pages, return them to form widget for selection
     */
    public function getPageUrlOptions()
    {
        $currentTheme = Theme::getEditTheme();
        $allThemePages = Page::listInTheme($currentTheme, true);
        $options = [];
        foreach ($allThemePages as $p){
            $options['url='.$p->url.'&type=cms_pages'] = $p->title;
        }

        $tree = StaticPageClass::buildMenuTree($currentTheme);        
        foreach ($tree as $key => $page){
            if(isset($page['title']) && isset($page['url'])){
                $options['url='.$page['url'].'&type=pages_plugin'] = $page['title'];
            }
        }
        return $options;
    }

    public function beforeSave()
    {
        parse_str($this->page_url,$selected_url);
        $this->page_url = $selected_url['url'];
        $this->type = $selected_url['type']; 
        return true;
    }

    /*
     * Update cache after saving new domain 
     */
    public function afterSave()
    {
        // forget current data
        Cache::forget('julius_multidomain_settings');

        // get all records available now
        $cacheableRecords = Setting::generateCacheableRecords();

        //save them in cache
        Cache::forever('julius_multidomain_settings', $cacheableRecords);
    }

    public static function generateCacheableRecords()
    {
        $allRecords = Setting::all()->toArray();
        $cacheableRecords = [];

        foreach ($allRecords as $record) {
            $cacheableRecords[$record['domain']] = [
                'page_url' => $record['page_url'],
                'type' => $record['type'],
                'is_protected' => $record['is_protected']
            ];
        }

        return $cacheableRecords;
    }

}
