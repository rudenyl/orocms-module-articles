<?php
namespace Modules\Articles\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticlePublishing extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article_publishing';

    /**
     * Disable timestamps checking
     */
    public $timestamps = false;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded  = ['id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The fillable property.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * @param $value
     */
    public function setPublishUpAttribute($value)
    {
        //
        // Test for datetime
        //
        // in MySQL, "2016-02-16 05:30 PM" --> "0000-00-00 00:00:00"
        // PostgreSQL seems okay ;)
        //
        if (($value = strtotime($value)) !== false) {
            $value = date('Y-m-d H:i:s', $value);
        }

        $this->attributes['publish_up'] = $value;
    }

    /**
     * @param $value
     */
    public function setPublishDownAttribute($value)
    {
        if (($value = strtotime($value)) !== false) {
            $value = date('Y-m-d H:i:s', $value);
        }

        $this->attributes['publish_down'] = $value;
    }

    /**
     * @param void
     */
    public function getPublishUpAttribute()
    {
        return $this->_getDate( $this->attributes['publish_up'] );
    }
    
    /**
     * @param void
     */
    public function getPublishDownAttribute()
    {
        return $this->_getDate( $this->attributes['publish_down'] );
    }

    /**
     * Convert datetime to correct timezone
     *
     * @return \Carbon\Carbon
     */
    private function _getDate($date)
    {
        if (empty($date) || $date == '0000-00-00 00:00:00') {
            return;
        }

        $tz = config('core.timezone', 'UTC');
        return (string)(new Carbon($date))->setTimezone($tz);
    }
}
