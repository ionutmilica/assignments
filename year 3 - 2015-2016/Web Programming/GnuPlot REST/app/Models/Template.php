<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(definition="template", required={"id", "name", "script", "preview", "created_at", "updated_at"})
 * @SWG\Property(property="id", format="int64", description="Id of the template")
 * @SWG\Property(property="name", description="The name of the template")
 * @SWG\Property(property="script", description="Script of the template")
 * @SWG\Property(property="preview", description="Preview image")
 * @SWG\Property(property="created_at", description="The date of creation")
 * @SWG\Property(property="updated_at", description="The date of update")
 */
class Template extends Model
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plot()
    {
        return $this->hasOne('App/Models/Plot');
    }
}