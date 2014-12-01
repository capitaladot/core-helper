<?php 

namespace Stevebauman\CoreHelper\Services;

use Stevebauman\CoreHelper\Services\AbstractModelService;

abstract class AbstractNestedSetModelService extends AbstractModelService {
    
    public function create()
    {
        
        $this->dbStartTransaction();
        
        try {
            
            $insert = array(
                'name' => $this->getInput('name')
            );

            $record = $this->model->create($insert);

            $this->dbCommitTransaction();

            return $record;
        
        } catch (Exception $e) {
            
            $this->dbRollbackTransaction();
            
            return false;
        }
    }
    
    
    
}