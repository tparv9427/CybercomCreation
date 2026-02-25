<?php

trait A {
    public function log() {
        echo "This is class A<br>";
    }
}

trait B {
    public function log() {
        echo "This is class B<br>";
    }
}

trait D {
    public function log() {
        echo "This is class D<br>";
    }
}

trait E {
    public function log() {
        echo "This is class E<br>";
    }
}

trait F {
    public function log() {
        echo "This is class F<br>";
    }
}

class C {

    use A, B, D, E, F {

        A::log insteadof B, D, E, F;

        B::log as logB;
        D::log as logD;
        E::log as logE;
        F::log as logF;
    }

    public function showLogs() {
        $this->log();   // From A
        $this->logB();  // From B
        $this->logD();  // From D
        $this->logE();  // From E
        $this->logF();  // From F
    }
}

$obj = new C();
$obj->showLogs();

?>