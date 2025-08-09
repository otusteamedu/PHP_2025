<?php

namespace Dkopasov\Hw4;

class App
{

    public function run(): string {

        if (isset($_POST['string'])) {
            $checker = new Checker();
            return $checker->check($_POST['string']);
        }

        return $this->viewForm();
    }

    private function viewForm(): string {
        return "
            <form method='post'>
            
            <input type='text' name='string' placeholder='Name'>
            
            <button type='submit'>submit</button>
            
            </form>";
    }

}