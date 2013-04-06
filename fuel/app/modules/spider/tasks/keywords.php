<?php
namespace Fuel\Tasks;

use Fuel\Core\Package as Package;  
        
// php oil refine spider::keywords  

class Keywords
{
 

    public function __construct() {
        Package::load('Spider');  
    }

    public function run($keyword)
    {  
        $starttime = microtime(true);

        if (!$keyword) {
            \Cli::error('Please provide keyword');   
            return;
        } 

        if (!\Collection\Keyword::createStats($keyword)) {
            \Cli::error('Error creating stats. Keyword doesn\'t exist?');   
            return;
        }  
        
        \Cli::write('Stats created.');     
        \Cli::write('Calcuation time: ' . (microtime(true) - $starttime));

    }

    public function add($keyword)
    {   

        if (!$keyword) {
            \Cli::error('Please provide keyword');   
            return;
        }

        if (!\Collection\Keyword::addKeywordToDB($keyword)) {
            \Cli::error('Keyword already exists');   
            return;
        } 

        \Cli::write('Keyword added.');     

    }
 
}