<?php

class Panzer
{
    private $name;
    private $country;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        if(isset($this->$name))
        {
            return $this->$name;
        }
    }


    public function __call($name, $arguments)
    {
        if($name == 'say')
        {
            echo 'yell: ';
            foreach($arguments as $arg)
            {
                echo $arg.' ';
            }
        }
        else
        {
            echo 'you call: '.$name;
        }
    } 

    public function fire()
    {
        echo 'yalalaa! Boom!';
    }

    public function __toString()
    {
        echo 'name: '.$this->name;
        echo 'country: '.$this->country;
    }

}

$tiger = new Panzer;
$tiger->name = 'tiger';
$tiger->country = 'deutschland';
echo $tiger->name;
echo $tiger->country;

$tiger->say('vor!', 'dddd');
$tiger->say('sieg!');
$tiger->say('sieg!', 'heil!', 'gehen!');

$tiger->fire();

echo $tiger;

?>
