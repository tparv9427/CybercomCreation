<?php
class Employee {
    public $name;
    private $salary;

    public function __construct($name, $salary) {
        $this->name = $name;
        $this->salary = $salary;
    }

    public function getSalary() {
        return $this->salary;
    }

    public function setSalary($amount) {
        if ($amount > 0) {
            $this->salary = $amount;
        }
    }
}

$emp = new Employee("Jane", 6000);
echo $emp->getSalary();
echo "<br>";
$emp->setSalary(7000);
echo $emp->getSalary();
?>
