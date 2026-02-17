<?php

class B {
    public function say() {
        echo "B working\n";
    }
}

class A {
    private $b;

    public function __construct() {
        $this->b = new B();
    }

    public function execute() {
        $this->b->say();
        echo "A executed\n";
    }
}

$a = new A();
$a->execute();

?>
