<?php

namespace Snaik\Interpreter\Commands;

use Snaik\Interpreter\Services\InputOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InterpreterCommand extends Command
{


    protected $commandName = 'read';
    protected $commandDescription = "read scheme langage";

    protected $commandArgumentName = "nae";
    protected $commandArgumentDescription = "interpret?";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::OPTIONAL,
                $this->commandArgumentDescription
            )
            ->setHelp('This command allows you to interpret Scheme langage...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $term1 = rand(1, 10);
        $term2 = rand(1, 10);
        $result = $term1 + $term2;

        $io = new InputOutput($input, $output);
       
        $filename = "./src/Parses/parse.php";
        $f = fopen($filename, 'w');
        if (!$f) {
            die('Error creating the file ' . $filename);
        }
        
        $parse = "<?php\n";
        $name = $input->getArgument($this->commandArgumentName);
        $value = explode(" ",$name);

        $this->interpreter($value,$io);
      
        $parse .= $name;

        fputs($f, $parse);
        fclose($f);

        return Command::SUCCESS;
    }
   
    protected function interpreter(array $value,$io){
            
        $newValue = array();
        $newValue = $value;
        $addition = false;
        $soustraction = false;
        $multiplication = false;
        $division = false;
        $result = 0;
        $number= 0;
       
        for($i=1 ; $i<=count($value)-1;$i++){
           
            if($value[$i]==="+"){
                $addition = true;
            }
            if($value[$i]==="-"){
                $soustraction = true;
            }
            if($value[$i]==="*"){
                $multiplication = true;
            }
            if($value[$i]==="/"){
                $division = true;
            }
            if(($addition|| $soustraction|| $multiplication || $division) && $value[$i]===")"){
                if($division && $number!==3){
                    $arguments = $number-1;
                    return $io->wrong("Expected 2 arguments, got {$arguments}");
                }
                array_splice($newValue, $i-$number ,$number+1,strval($result));
                break;
            }elseif(($addition || $soustraction || $multiplication) && $value[$i]==="("){
                $addition= false;
                $soustraction = false;
                $multiplication = false;
                $division = false;
                $result = 0;
                $number=0;
            }elseif($addition && $value[$i]!=="+"){
                $result += intval($value[$i]);
            }elseif($soustraction && $value[$i]!=="-"){
                if($value[$i-1]==="-" && $value[$i+1]!=="(" && $value[$i+1]!==")"){
                    $result += intval($value[$i]);
                }else{
                  $result -= intval($value[$i]);  
                }

            }elseif($multiplication && $value[$i]!=="*"){
                if($result===0 && $value[$i-1]==="*"){
                    $result = 1;
                }
                $result = $result *  intval($value[$i]);
                
            }elseif($division && $value[$i]!="/"){
                if($value[$i-1]!=="/" && intval($value[$i]===0)){
                    return $io->wrong("Division by 0");
                }
                if($value[$i-1]==="/"){
                    $result += intval($value[$i]);
                }else{
                    $result = $result /  intval($value[$i]);
                }
               
            }
            $number++;
        }
        
        if(count($newValue)>3){
            echo implode(" ",$newValue), "\n";
            return $this->interpreter($newValue,$io);

        }else{
           return $io->right("Result : {$newValue[1]}");
        }
    }
}
