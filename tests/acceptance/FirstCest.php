<?php

namespace VerteXVaaR\Acceptance;

use VerteXVaaR\BlueDistTest\AcceptanceTester;

class FirstCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Welcome to VerteXVaaR.BlueSprints');

        $I->click('follow me');
        $I->see('OH WAIT! There is no Fruit yet.');

        $I->click('Create a bunch of fruits');

        $I->click('I am a Apple and my color is red');
        $I->see('Change a Apple');

        $I->submitForm('[action="updateFruit"]', [
            'color' => 'green-red',
        ]);
        $I->see('I am a Apple and my color is green-red');
    }
}
