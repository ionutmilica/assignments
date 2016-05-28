<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(definition="plot", required={"id", "name", "template", "image", "password", "created_at", "updated_at"})
 * @SWG\Property(property="id", description="Id of the plot", format="int64")
 * @SWG\Property(property="name", type="string", description="Name of the plot")
 * @SWG\Property(property="template", description="Template id")
 * @SWG\Property(property="image", description="Generated image url")
 * @SWG\Property(property="password", description="Password of the plot")
 * @SWG\Property(property="created_at", description="The date of creation")
 * @SWG\Property(property="updated_at", description="The date of update")
 */
class Plot extends Model
{
    protected $fillable = ['name', 'password'];

    protected $hidden = ['password'];

    /**
     * Has one templates
     */
    public function template()
    {
        return $this->hasOne('App\Models\Template', 'id', 'template');
    }
}