<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'race';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'event_date'];


    /**
     * The runners that belong to the race.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function runners()
    {
        return $this->belongsToMany('App\Runner', 'race_runner', 'id_race', 'id_runner')->withPivot('start_time', 'finish_time');
    }
    
    
}
