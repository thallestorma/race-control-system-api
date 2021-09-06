<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Runner extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'runner';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'cpf', 'birthdate'];

    /**
     * The races that belong to the runner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function races()
    {
        return $this->belongsToMany('App\Race', 'race_runner', 'id_runner', 'id_race')->withPivot('start_time', 'finish_time');
    }

}
