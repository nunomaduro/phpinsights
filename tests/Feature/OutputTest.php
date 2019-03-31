<?php

namespace Tests\Feature;

use NunoMaduro\PhpInsights\Application\Console\Commands\AnalyseCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\Container;

final class OutputTest extends TestCase
{
    public function testOutput(): void
    {
        /** @var AnalyseCommand $command */
        $command = Container::resolve(AnalyseCommand::class);

        $command(new ArrayInput(['directory' => 'tests/Fixtures/Code'], new InputDefinition([
            new InputArgument('directory', InputArgument::REQUIRED),
        ])), $output = new BufferedOutput());

        $this->assertEquals($output->fetch(), <<<EOF
    
  ✍️  Lines Of Code               🔎  Code Quality at 50.00%                                                                
                                                                                                                           
  Total                           62                                                                                       
  •Comments 0.00%                 0                                                                                        
  •Non Comments 100.00%           62                                                                                       
  Source Code                     37                                                                                       
  • Classes 94.59%                35 avg:11 max: 35            <-- Having `classes` with more than 30 lines is prohibited  
                                                                   • tests/Fixtures/Code/Test.php  -->  35 lines           
  • Methods 0.00%                 avg:17 max:35                                                                            
  • Functions 0.00%               0 avg:0                                                                                  
  • Global 5.41%                  2                                                                                        
                                                                                                                           
  👔  Cyclomatic Complexity                                                                                                 
                                                                                                                           
  Cyclomatic Complexity           0.00                         ✔                                                           
  Cyclomatic Complexity Classes   1.00 max:1                                                                               
  Cyclomatic Complexity Methods   1.00 max:1                                                                               
                                                                                                                           
  🔗  Dependencies                                                                                                          
                                                                                                                           
  Global Accesses                 0                                                                                        
  • Constants 0.00%               0                                                                                        
  • Variables 0.00%               0                                                                                        
  • Super Variables 0.00%         0                                                                                        
  Attribute Accesses              0                                                                                        
  • Static 0.00%                  0                                                                                        
  • Non Static 0.00%              0                                                                                        
  Method Calls                    0                                                                                        
  • Static 0.00%                  0                                                                                        
  • Non Static 0.00%              0                                                                                        
                                                                                                                           
  🧱  Structure                                                                                                             
                                                                                                                           
  Namespaces                      1                                                                                        
  Interfaces                      0                                                                                        
  Traits                          1                            <-- The use of `traits` is prohibited                       
                                                                   • tests/Fixtures/Code/helpers.php                       
  Classes                         2                                                                                        
  • Abstract 0.00%                0                                                                                        
  • Concrete 100.00%              2                                                                                        
  Methods                         2                                                                                        
  • Static 0.00%                  0                                                                                        
  • Non Static 100.00%            2                                                                                        
  • Public 100.00%                2                                                                                        
  • Non Public 0.00%              0                                                                                        
  Functions                       0                                                                                        
  • Named 0.00%                   0                                                                                        
  • Anonymous 0.00%               0                                                                                        
  Constants                       0                                                                                        
  • Global 0.00%                  0                            ✔                                                           
  • Class 0.00%                   0                                                                                        
    

EOF
        );
    }
}
