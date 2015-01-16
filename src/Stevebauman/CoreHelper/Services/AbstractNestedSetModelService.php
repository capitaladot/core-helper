<?php 

namespace Stevebauman\CoreHelper\Services;

abstract class AbstractNestedSetModelService extends Service {

    public function roots()
    {
        return $this->model->roots()->where('belongs_to', $this->scoped_id)->get();
    }
    
}