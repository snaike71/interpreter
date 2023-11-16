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

    protected $commandArgumentName = "scheme";
    protected $commandArgumentDescription = "scheme to interprete";

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
        
        $io = new InputOutput($input, $output);
       
        $read = $input->getArgument($this->commandArgumentName);

        // vérifie si on cherche à lire un fichier .scm
        if(strpos($read, ".scm" )){
            $read = file_get_contents($read);
        }

        $value = $this->addSpace($read);
        $value = explode(" ",$value);
        $this->interpreter($value,$io);
        
        return Command::SUCCESS;
    }
   
    protected function interpreter(array $value,$io){
            
        $newValue = array();
        $newValue = $value;
        $addition = false;
        $soustraction = false;
        $multiplication = false;
        $division = false;
        $modulo = false;
        $result = 0;
        $number= 0;

        for($i=1 ; $i<=count($value)-1;$i++){
            if(is_numeric($value[$i]) && $value[$i-1]==="("){
                $number++;
                return $io->wrong("SchemeError: Cannot call {$value[$i]}, index: {$number}");
            }
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
            if($value[$i]==="modulo"){
                $modulo = true;
            }
            if(($addition|| $soustraction|| $multiplication || $division || $modulo) && $value[$i]===")"){
                if(($division ||$modulo) && $number!==3){
                    $arguments = $number-1;
                    return $io->wrong("SchemeError: Expected 2 arguments, got {$arguments}, index: {$number}");
                }
                array_splice($newValue, $i-$number ,$number+1,strval($result));
                break;
            }elseif(($addition || $soustraction || $multiplication) && $value[$i]==="("){
                $addition= false;
                $soustraction = false;
                $multiplication = false;
                $division = false;
                $modulo = false;
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
                
            }elseif($division && $value[$i]!=="/"){
                if($value[$i-1]!=="/" && intval($value[$i])===0){
                    return $io->wrong("SchemeError: Division by 0, index: {$number}");
                }
                if($value[$i-1]==="/"){
                    $result += intval($value[$i]);
                }else{
                    $result = $result /  intval($value[$i]);
                } 
            }
            elseif($modulo && $value[$i]!=="modulo"){
                $result = $result % intval($value[$i]);
            }
            $number++;
        }
        echo implode(" ",$newValue), "\n";
        if(count($newValue)>3){
          
            return $this->interpreter($newValue,$io);

        }else{
           return $io->right(sprintf("Result : {$newValue[1]}"));
        }
    }
    protected function addSpace($chaine) {
        // Ajouter un espace avant la parenthèse ouvrante si nécessaire
        $chaine = preg_replace('/\(([^ ])/', '( $1', $chaine);
        // Ajouter un espace après la parenthèse fermante si nécessaire
        $chaine = preg_replace('/([^ ])\)/', '$1 )', $chaine);
    
        return $chaine;
    }
    
}
