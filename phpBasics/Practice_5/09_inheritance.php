<?php
class Employee {
    private $name;

    public function setName($name) {
        $this->name = $name;
    }

    public function getDetails() {
        return "Employee: " . $this->name;
    }
}

class Manager extends Employee {
    public $department;

    public function getDetails() {
        return parent::getDetails() . "<br>" . " Department: " . $this->department;
    }
}

$manager = new Manager();
$manager->setName("Alice");
$manager->department = "Sales";
echo $manager->getDetails();
?>
