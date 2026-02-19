<?php

class A {

    public $name;
    public $age;

    public function setData($name, $age) {
        $this->name = $name;
        $this->age  = $age;
    }

    public function showData() {
        echo "Name: " . $this->name . ", Age: " . $this->age . "<br>";
    }
}

$obj1 = new A();
$obj1->setData("Parv", 22);

echo "Object 1:<br>";
$obj1->showData();
echo "<br>";

$obj2 = $obj1;

echo "Object 2:<br>";
$obj2->showData();
echo "<br>";

$obj2->setData("parv", 25);
$obj1->setData("Talati", 25);

echo "After modifying Object 2:<br>";
echo "Object 2:<br>";
$obj2->showData();

echo "Object 1:<br>";
$obj1->showData();
echo "<br>";

$obj3 = $obj2;

echo "Object 3:<br>";
$obj3->showData();
echo "<br>";

class B {

    public $copyObj;

    public function __construct($copyObj) {
        $this->copyObj = $copyObj;
    }

    public function showData() {
        echo "Class B contains -> ";
        $this->copyObj->showData();
    }
}

$obj4 = new B($obj3);

echo "Object 4:<br>";
$obj4->showData();

?>
