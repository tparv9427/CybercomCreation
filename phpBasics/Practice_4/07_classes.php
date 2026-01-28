<?php
class Employee {
    public $name;
    private $salary;

    public function __construct($name, $salary) {
        $this->name = $name;
        $this->salary = $salary;
    }

    public function showSalary() {
        return $this->salary;
    }   
}

$emp = new Employee("John", 5000);
echo $emp->name;
echo "<br>";
echo $emp->showSalary();
?>
