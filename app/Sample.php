<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $fillable = [
        // Normally don't allow IDs to be updated from a form..
        'basc_group_id',
        'batch_id',
        'name',
        'i7_index_id',
        'i5_index_id',
    ];

    /**
     * A sample is owned by a user
     *
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
