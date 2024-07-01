<?php

namespace App\Resources;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Str;
use ReflectionClass;
use Carbon\Carbon;

class Resources
{
    // getResByKeyword
    public function _invoke($keyword){
        $paths = __DIR__.'/Resources';
        $namespace = app()->getNamespace();
        $res = null;
        foreach ((new Finder)->in($paths)->files() as $file) {
            $resource = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($file->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
            );

            // is_subclass_of($resource, Resource::class) &&
            if (! (new ReflectionClass($resource))->isAbstract()) {
        			$isEnable = true; // TODO weight 
        	    if($isEnable){
        	        $resource = app($resource);
        	        $res = $resource->_invoke($keyword);
                    if($res) return $res;
        	        // if(!is_null($res)) break;
        	    }
            }
        }
    }
}
