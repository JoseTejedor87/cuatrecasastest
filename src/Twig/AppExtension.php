<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;
use Twig\TwigFunction;


class AppExtension extends AbstractExtension
{
 
    public function getTests()
    {
        return [
            new TwigTest('instanceof', [$this, 'isInstanceof'])
        ];
    }

    /**
     * @param $var
     * @param $instance
     * @return bool
     */
    public function isInstanceof($var, $instance) {
        return  $var instanceof $instance;
    }



    public function getFunctions()
    {
        return [
            new TwigFunction('entity_type', [$this, 'entityType'] ),
            new TwigFunction('get_pathda', [$this, 'getPathDetailByActivity'] ),
        ];
    }

    /**
     * @param $entity
     * @return string
     */
    public function entityType($entity)
    {
        return   get_class($entity);
    }
    

    /**
     * @param $entity
     * @return string
     */
    public function getPathDetailByActivity($entity){
        $strClass = get_class($entity);
        $arr = explode("\\",$strClass);
        $text = end($arr);

        switch ($text){
            case 'Practice':
                return 'practices_detail';
            case 'Sector':
                return 'sectors_detail';
            case 'Desk':
                return 'sectors_detail';
            case 'Product':
                return 'products_detail';
            case 'Event':
                return 'events_detail';                             
            default:
                return 'no-definido';
        }

    }
    
}

?>